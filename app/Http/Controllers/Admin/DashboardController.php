<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\ActivityLog;
use App\Services\StockService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected StockService $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    /**
     * Display the Admin Dashboard.
     */
    public function index()
    {
        // Stats
        $totalSales = Order::query()->where('status', 'paid')->sum('total');
        $lowStockProducts = $this->stockService->checkLowStock(5);
        $activeUsers = User::query()->where('is_active', true)->count();

        // Recent Orders
        $recentOrders = Order::with('user')->orderBy('created_at', 'desc')->limit(6)->get();

        // Low stock list
        $criticalStock = $lowStockProducts->take(5);

        return view('admin.dashboard', compact(
            'totalSales',
            'lowStockProducts',
            'activeUsers',
            'recentOrders',
            'criticalStock'
        ));
    }

    /**
     * Display the System Audit Activity Logs.
     */
    public function logs()
    {
        $logs = ActivityLog::with('user')->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.logs', compact('logs'));
    }
}
