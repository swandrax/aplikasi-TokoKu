<?php

namespace App\Http\Controllers\Pembeli;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Services\SearchService;
use App\Services\StockService;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    protected SearchService $searchService;
    protected StockService $stockService;

    public function __construct(SearchService $searchService, StockService $stockService)
    {
        $this->searchService = $searchService;
        $this->stockService = $stockService;
    }

    /**
     * Storefront Landing & List Page.
     * Supports Hybrid Search, Kategori, and AJAX Lazy Loading (12 items per load).
     */
    public function index(Request $request)
    {
        $keyword = $request->input('keyword', '');
        $categoryId = $request->input('category_id');
        $minPrice = $request->filled('min_price') ? (float) $request->min_price : null;
        $maxPrice = $request->filled('max_price') ? (float) $request->max_price : null;

        // Perform Hybrid Search
        $productsCollection = $this->searchService->hybridProductSearch($keyword, $categoryId, $minPrice, $maxPrice);

        // Inject available stock count
        foreach ($productsCollection as $p) {
            $p->setAttribute('available_stock', $this->stockService->getAvailableStock($p->id));
        }

        // Handle AJAX Lazy Loading (Infinite Scroll / Load More)
        $perPage = 12;
        $page = (int) $request->input('page', 1);
        $offset = ($page - 1) * $perPage;

        $total = $productsCollection->count();
        $products = $productsCollection->slice($offset, $perPage)->values();
        $hasMore = ($offset + $perPage) < $total;

        $categories = Category::query()->where('is_active', true)->orderBy('name', 'asc')->get();

        if ($request->ajax()) {
            // Render partial views and return JSON to append products
            $html = view('partials.product-cards', compact('products'))->render();
            return response()->json([
                'html' => $html,
                'has_more' => $hasMore,
            ]);
        }

        return view('pembeli.shop.index', compact('products', 'categories', 'keyword', 'categoryId', 'minPrice', 'maxPrice', 'hasMore'));
    }

    /**
     * Display a single product's detail.
     */
    public function show(Product $product)
    {
        if (!$product->is_active) {
            abort(404, 'Produk tidak aktif.');
        }

        $availableStock = $this->stockService->getAvailableStock($product->id);
        
        // Related products in the same category
        $relatedProducts = Product::query()->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->limit(4)
            ->get();

        return view('pembeli.shop.show', compact('product', 'availableStock', 'relatedProducts'));
    }
}
