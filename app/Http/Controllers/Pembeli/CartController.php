<?php

namespace App\Http\Controllers\Pembeli;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    protected StockService $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    /**
     * Display shopping cart.
     */
    public function index()
    {
        $cartItems = Cart::query()->where('user_id', Auth::id())
            ->with('product')
            ->get();

        $subtotal = 0;
        $totalWeight = 0;

        foreach ($cartItems as $item) {
            $item->setAttribute('available_stock', $this->stockService->getAvailableStock($item->product_id));
            $subtotal += $item->product->price_sell * $item->quantity;
            $totalWeight += $item->product->weight * $item->quantity;
        }

        return view('pembeli.cart.index', compact('cartItems', 'subtotal', 'totalWeight'));
    }

    /**
     * Add product to cart.
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $productId = $request->product_id;
        $qtyToAdd = (int) $request->quantity;
        $userId = Auth::id();

        $product = Product::findOrFail($productId);
        if (!$product->is_active) {
            return back()->with('error', 'Produk ini sedang tidak aktif.');
        }

        // Validate stock available in the FIFO system
        $availableStock = $this->stockService->getAvailableStock($productId);
        
        $existingCart = Cart::query()->where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        $newQty = $qtyToAdd + ($existingCart ? $existingCart->quantity : 0);

        if ($availableStock < $newQty) {
            return back()->with('error', "Stok tidak mencukupi. Sisa stok tersedia: {$availableStock} unit.");
        }

        if ($existingCart) {
            $existingCart->update(['quantity' => $newQty]);
        } else {
            Cart::create([
                'user_id' => $userId,
                'product_id' => $productId,
                'quantity' => $qtyToAdd,
            ]);
        }

        return redirect()->route('pembeli.cart.index')->with('success', 'Produk berhasil dimasukkan ke keranjang belanja.');
    }

    /**
     * Update cart item quantity.
     */
    public function update(Request $request)
    {
        $request->validate([
            'cart_id' => 'required|exists:carts,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = Cart::findOrFail($request->cart_id);
        
        // Security check
        if ($cart->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $availableStock = $this->stockService->getAvailableStock($cart->product_id);

        if ($availableStock < $request->quantity) {
            return response()->json([
                'success' => false, 
                'message' => "Stok tidak mencukupi. Sisa stok tersedia: {$availableStock} unit."
            ], 422);
        }

        $cart->update(['quantity' => $request->quantity]);

        // Recalculate totals for JSON response
        $cartItems = Cart::query()->where('user_id', Auth::id())->with('product')->get();
        $subtotal = 0;
        foreach ($cartItems as $item) {
            $subtotal += $item->product->price_sell * $item->quantity;
        }

        return response()->json([
            'success' => true,
            'message' => 'Keranjang berhasil diperbarui.',
            'item_subtotal' => 'Rp ' . number_format($cart->product->price_sell * $cart->quantity, 0, ',', '.'),
            'cart_subtotal' => 'Rp ' . number_format($subtotal, 0, ',', '.'),
            'cart_total' => 'Rp ' . number_format($subtotal + ($subtotal * 0.11), 0, ',', '.'), // subtotal + tax
        ]);
    }

    /**
     * Remove item from cart.
     */
    public function remove(Request $request)
    {
        $request->validate([
            'cart_id' => 'required|exists:carts,id',
        ]);

        $cart = Cart::findOrFail($request->cart_id);
        
        // Security check
        if ($cart->user_id !== Auth::id()) {
            abort(403);
        }

        $cart->delete();

        return redirect()->route('pembeli.cart.index')->with('success', 'Produk berhasil dihapus dari keranjang belanja.');
    }
}
