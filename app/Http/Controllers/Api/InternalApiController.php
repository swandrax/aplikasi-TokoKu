<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Cart;
use App\Models\NotificationLog;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InternalApiController extends Controller
{
    protected StockService $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    /**
     * Dashboard Statistics for Admin (Real-time).
     */
    public function dashboardStats()
    {
        // 1. Total Penjualan (Lunas)
        $totalSales = Order::query()->where('status', 'paid')->sum('total');
        
        // 2. Stok Kritis (< 5)
        $lowStockCount = $this->stockService->checkLowStock(5)->count();

        // 3. User Aktif
        $activeUserCount = User::query()->where('is_active', true)->count();

        return response()->json([
            'total_penjualan' => 'Rp ' . number_format($totalSales, 0, ',', '.'),
            'stok_kritis' => $lowStockCount,
            'user_aktif' => $activeUserCount,
        ]);
    }

    /**
     * Get list of critical stock alerts.
     */
    public function stockAlerts()
    {
        $lowStockProducts = $this->stockService->checkLowStock(5)->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'stock' => $this->stockService->getAvailableStock($product->id),
            ];
        })->values();

        return response()->json($lowStockProducts);
    }

    /**
     * Get specific order status for live tracking.
     */
    public function orderStatus(Order $order)
    {
        // Security check
        if (Auth::user()->role === 'pembeli' && $order->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json([
            'order_number' => $order->order_number,
            'status' => $order->status,
            'status_badge_color' => match ($order->status) {
                'paid' => 'bg-emerald-500 text-white',
                'cancelled' => 'bg-rose-500 text-white',
                default => 'bg-amber-500 text-white',
            },
            'paid_at' => $order->paid_at ? $order->paid_at->timezone('Asia/Jakarta')->format('d M Y, H:i') : null,
            'cancelled_at' => $order->cancelled_at ? $order->cancelled_at->timezone('Asia/Jakarta')->format('d M Y, H:i') : null,
        ]);
    }

    /**
     * Get unread notifications count and recent log entries.
     */
    public function notifications()
    {
        $userId = Auth::id();
        $unreadCount = NotificationLog::query()->where('user_id', $userId)->where('is_read', false)->count();
        $recent = NotificationLog::query()->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($notif) {
                return [
                    'id' => $notif->id,
                    'title' => $notif->title,
                    'message' => $notif->message,
                    'is_read' => $notif->is_read,
                    'created_at_human' => $notif->created_at->diffForHumans(),
                ];
            });

        return response()->json([
            'unread_count' => $unreadCount,
            'recent' => $recent,
        ]);
    }

    /**
     * Get shopping cart badge item count.
     */
    public function cartCount()
    {
        $count = Cart::query()->where('user_id', Auth::id())->sum('quantity');
        return response()->json(['count' => $count]);
    }

    /**
     * Get real-time stock count for Cashier POS.
     */
    public function posStock(Product $product)
    {
        $stock = $this->stockService->getAvailableStock($product->id);
        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'stock' => $stock,
            'price_sell' => (float) $product->price_sell,
            'price_sell_formatted' => 'Rp ' . number_format($product->price_sell, 0, ',', '.'),
        ]);
    }
}
