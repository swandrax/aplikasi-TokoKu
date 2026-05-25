<?php

namespace App\Http\Controllers\Pembeli;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\StockService;
use App\Services\ReceiptService;
use App\Mail\OrderConfirmationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    protected StockService $stockService;
    protected ReceiptService $receiptService;

    public function __construct(StockService $stockService, ReceiptService $receiptService)
    {
        $this->stockService = $stockService;
        $this->receiptService = $receiptService;
    }

    /**
     * Display checkout screen.
     */
    public function checkout()
    {
        $cartItems = Cart::query()->where('user_id', Auth::id())->with('product')->get();
        if ($cartItems->isEmpty()) {
            return redirect()->route('pembeli.cart.index')->with('error', 'Keranjang belanja Anda kosong.');
        }

        $subtotal = 0;
        foreach ($cartItems as $item) {
            $subtotal += $item->product->price_sell * $item->quantity;
        }

        $tax = $subtotal * 0.11; // 11% Tax
        $total = $subtotal + $tax;

        return view('pembeli.order.checkout', compact('cartItems', 'subtotal', 'tax', 'total'));
    }

    /**
     * Submit and process checkout order (FIFO + DB Transactions).
     */
    public function store(Request $request)
    {
        $request->validate([
            'address' => 'required|string|max:255',
            'payment_method' => 'required|string|in:Tunai,Transfer Bank,Qris',
            'payment_proof' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'note' => 'nullable|string|max:255',
        ], [
            'address.required' => 'Alamat pengiriman wajib diisi.',
            'payment_method.required' => 'Metode pembayaran wajib dipilih.',
        ]);

        $userId = Auth::id();
        $cartItems = Cart::query()->where('user_id', $userId)->with('product')->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('pembeli.shop.index')->with('error', 'Keranjang belanja Anda kosong.');
        }

        try {
            $order = DB::transaction(function () use ($userId, $cartItems, $request) {
                // 1. Double check stock availability
                $subtotal = 0;
                $itemsToDeduct = [];

                foreach ($cartItems as $item) {
                    $availableStock = $this->stockService->getAvailableStock($item->product_id);
                    if ($availableStock < $item->quantity) {
                        throw new \Exception("Stok tidak mencukupi untuk produk '{$item->product->name}'. Sisa tersedia: {$availableStock} unit.");
                    }
                    $subtotal += $item->product->price_sell * $item->quantity;
                    $itemsToDeduct[] = [
                        'product_id' => $item->product_id,
                        'product_name' => $item->product->name,
                        'price' => $item->product->price_sell,
                        'quantity' => $item->quantity,
                    ];
                }

                $tax = $subtotal * 0.11;
                $total = $subtotal + $tax;

                $orderNumber = 'ORD-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));

                $paymentProofPath = null;
                if ($request->hasFile('payment_proof')) {
                    $paymentProofPath = $request->file('payment_proof')->store('proofs', 'public');
                }

                // 2. Create Order
                $order = Order::create([
                    'user_id' => $userId,
                    'kasir_id' => null, // Self checkout online
                    'order_number' => $orderNumber,
                    'status' => 'paid', // Mark paid immediately for quick demo
                    'subtotal' => $subtotal,
                    'tax_amount' => $tax,
                    'discount_amount' => 0,
                    'total' => $total,
                    'payment_method' => $request->payment_method,
                    'payment_proof' => $paymentProofPath,
                    'note' => $request->note . " | Alamat: " . $request->address,
                    'paid_at' => now(),
                ]);

                // 3. FIFO deduct stock and save OrderItems
                foreach ($itemsToDeduct as $item) {
                    $deductResult = $this->stockService->deductStock($item['product_id'], $item['quantity'], $order->id);
                    
                    if (!$deductResult['success']) {
                        throw new \Exception("Gagal mengalokasikan stok FIFO untuk produk '{$item['product_name']}'.");
                    }

                    // Create items based on FIFO batches used
                    foreach ($deductResult['batches_used'] as $batchUsed) {
                        OrderItem::create([
                            'order_id' => $order->id,
                            'product_id' => $item['product_id'],
                            'stock_batch_id' => $batchUsed['stock_batch_id'],
                            'product_name' => $item['product_name'],
                            'price' => $item['price'],
                            'quantity' => $batchUsed['quantity'],
                            'subtotal' => $item['price'] * $batchUsed['quantity'],
                        ]);
                    }
                }

                // 4. Delete Cart Items
                Cart::query()->where('user_id', $userId)->delete();

                return $order;
            });

            // 5. Generate PDF invoice
            $pdfPath = $this->receiptService->generatePdf($order);

            // 6. Dispatch Email with PDF attachment (Queued)
            try {
                Mail::to($order->user->email)->send(new OrderConfirmationMail($order, $pdfPath));
            } catch (\Exception $mailEx) {
                Log::error("Failed to send order confirmation email for {$order->order_number}: " . $mailEx->getMessage());
            }

            return redirect()->route('pembeli.order.show', $order->id)->with('success', "Pesanan {$order->order_number} berhasil dibuat dan lunas! Struk PDF telah dikirim ke email Anda.");

        } catch (\Exception $e) {
            Log::error("Checkout transaction failed: " . $e->getMessage());
            return redirect()->route('pembeli.cart.index')->with('error', $e->getMessage());
        }
    }

    /**
     * Display order history index.
     */
    public function index()
    {
        $orders = Order::query()->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('pembeli.order.index', compact('orders'));
    }

    /**
     * Show order details.
     */
    public function show(Order $order)
    {
        // Security Check
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Akses Ditolak.');
        }

        $order->load(['orderItems.product']);

        return view('pembeli.order.show', compact('order'));
    }
}
