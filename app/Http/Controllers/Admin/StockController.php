<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockLog;
use App\Services\StockService;
use Illuminate\Http\Request;

class StockController extends Controller
{
    protected StockService $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    /**
     * Display a listing of products and their current FIFO stock status.
     */
    public function index()
    {
        $products = Product::query()->where('is_active', true)->orderBy('name', 'asc')->get()->map(function (Product $product) {
            $product->setAttribute('available_stock', $this->stockService->getAvailableStock($product->id));
            return $product;
        });

        $recentLogs = StockLog::query()->with(['product', 'creator', 'stockBatch'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.stock.index', compact('products', 'recentLogs'));
    }

    /**
     * Show the form for creating a new stock batch.
     */
    public function create()
    {
        $products = Product::query()->where('is_active', true)->orderBy('name', 'asc')->get();
        return view('admin.stock.create', compact('products'));
    }

    /**
     * Store a newly created stock batch in storage (FIFO).
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:1000000',
            'purchase_price' => 'required|numeric|min:0|max:999999999999',
            'supplier_name' => 'nullable|string|max:100',
            'note' => 'nullable|string|max:255',
        ], [
            'product_id.required' => 'Produk wajib dipilih.',
            'quantity.required' => 'Jumlah stok awal wajib diisi.',
            'quantity.integer' => 'Jumlah stok harus berupa angka bulat.',
            'quantity.min' => 'Jumlah stok minimal 1 unit.',
            'purchase_price.required' => 'Harga beli batch wajib diisi.',
            'purchase_price.numeric' => 'Harga beli harus berupa angka.',
        ]);

        $product = Product::findOrFail($request->product_id);

        $this->stockService->addStock($request->product_id, [
            'quantity' => $request->quantity,
            'purchase_price' => $request->purchase_price,
            'supplier_name' => $request->supplier_name,
            'note' => $request->note,
            'batch_date' => now(),
            'description' => "Pengadaan batch baru oleh Admin untuk produk {$product->name}",
        ]);

        return redirect()->route('admin.stock.index')->with('success', 'Batch stok baru (FIFO) berhasil diinput ke sistem.');
    }
}
