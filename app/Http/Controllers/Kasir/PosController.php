<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\SearchService;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PosController extends Controller
{
    protected SearchService $searchService;
    protected StockService $stockService;

    public function __construct(SearchService $searchService, StockService $stockService)
    {
        $this->searchService = $searchService;
        $this->stockService = $stockService;
    }

    /**
     * Display POS workspace.
     */
    public function index(Request $request)
    {
        $keyword = $request->input('keyword', '');
        
        // Use Boyer-Moore and Sequential search service to look up active products
        if (!empty($keyword)) {
            $products = $this->searchService->hybridProductSearch($keyword);
        } else {
            $products = Product::query()->where('is_active', true)->with('category')->get()->map(function ($p) {
                $p->highlighted_name = $p->name;
                return $p;
            });
        }

        // Inject available stock count
        foreach ($products as $p) {
            $p->setAttribute('available_stock', $this->stockService->getAvailableStock($p->id));
        }

        return view('kasir.pos', compact('products', 'keyword'));
    }

    /**
     * Complete checkout for POS.
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'pembeli_email' => 'required|email|exists:users,email',
            'cart' => 'required|json', // [{"id": 3, "qty": 2}]
            'payment_method' => 'required|string|in:Tunai,Transfer Bank,Qris,Debit',
            'discount_amount' => 'nullable|numeric|min:0',
        ], [
            'pembeli_email.required' => 'Email pembeli wajib diisi.',
            'pembeli_email.exists' => 'Pembeli dengan email ini tidak ditemukan di sistem.',
            'cart.required' => 'Keranjang belanja POS kosong.',
            'payment_method.required' => 'Pilih metode pembayaran.',
        ]);

        $cartItems = json_decode($request->cart, true);
        if (empty($cartItems)) {
            return back()->with('error', 'Keranjang POS kosong. Silakan tambahkan produk.');
        }

        $pembeli = User::query()->where('email', $request->pembeli_email)->firstOrFail();
        $discount = (float) ($request->discount_amount ?? 0);

        try {
            $order = DB::transaction(function () use ($pembeli, $cartItems, $request, $discount) {
                // 1. Calculate pricing first & validate stock
                $subtotal = 0;
                $itemsToDeduct = [];

                foreach ($cartItems as $item) {
                    $product = Product::findOrFail($item['id']);
                    $qtyToBuy = (int) $item['qty'];
                    
                    // Validate available stock
                    $stockAvailable = $this->stockService->getAvailableStock($product->id);
                    if ($stockAvailable < $qtyToBuy) {
                        throw new \Exception("Stok tidak mencukupi untuk produk {$product->name}. Tersedia: {$stockAvailable}, Diminta: {$qtyToBuy}");
                    }

                    $subtotal += $product->price_sell * $qtyToBuy;
                    $itemsToDeduct[] = [
                        'product' => $product,
                        'qty' => $qtyToBuy,
                    ];
                }

                $tax = $subtotal * 0.11; // 11% Tax
                $total = $subtotal + $tax - $discount;

                $orderNumber = 'ORD-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));

                // 2. Create the Order
                $order = Order::create([
                    'user_id' => $pembeli->id,
                    'kasir_id' => Auth::id(),
                    'order_number' => $orderNumber,
                    'status' => 'paid', // Cashier checkout is paid instantly
                    'subtotal' => $subtotal,
                    'tax_amount' => $tax,
                    'discount_amount' => $discount,
                    'total' => max(0, $total),
                    'payment_method' => $request->payment_method,
                    'note' => 'Transaksi POS Kasir.',
                    'paid_at' => now(),
                ]);

                // 3. Deduct stock and save OrderItems
                foreach ($itemsToDeduct as $deductData) {
                    $product = $deductData['product'];
                    $qty = $deductData['qty'];

                    // FIFO Deduction
                    $deductResult = $this->stockService->deductStock($product->id, $qty, $order->id);

                    if (!$deductResult['success']) {
                        throw new \Exception("Gagal mengalokasikan stok FIFO untuk produk {$product->name}.");
                    }

                    // Create OrderItems from batches used
                    foreach ($deductResult['batches_used'] as $batchUsed) {
                        OrderItem::create([
                            'order_id' => $order->id,
                            'product_id' => $product->id,
                            'stock_batch_id' => $batchUsed['stock_batch_id'],
                            'product_name' => $product->name,
                            'price' => $product->price_sell,
                            'quantity' => $batchUsed['quantity'],
                            'subtotal' => $product->price_sell * $batchUsed['quantity'],
                        ]);
                    }
                }

                return $order;
            });

            // Redirect with success and order ID for printing
            return redirect()->route('kasir.pos.index')->with([
                'success' => "Transaksi {$order->order_number} berhasil diselesaikan!",
                'print_order_id' => $order->id,
            ]);

        } catch (\Exception $e) {
            Log::error("POS Checkout Failure: " . $e->getMessage());
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
}
