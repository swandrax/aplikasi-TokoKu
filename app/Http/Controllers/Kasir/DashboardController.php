<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Show Cashier Dashboard.
     */
    public function index()
    {
        $today = Carbon::today();
        $kasirId = Auth::id();

        // 1. Total transaction volume today processed by this cashier
        $todayOrdersCount = Order::query()->where('kasir_id', $kasirId)
            ->whereDate('created_at', $today)
            ->count();

        // 2. Total sales revenue today processed by this cashier
        $todaySalesAmount = Order::query()->where('kasir_id', $kasirId)
            ->where('status', 'paid')
            ->whereDate('created_at', $today)
            ->sum('total');

        // 3. Best selling products (top 5 by items sold quantity today)
        $topProducts = OrderItem::query()->select(['product_name', DB::raw('SUM(quantity) as total_qty')])
            ->whereHas('order', function ($query) use ($today) {
                $query->where('status', 'paid')->whereDate('created_at', $today);
            })
            ->groupBy('product_id', 'product_name')
            ->orderBy('total_qty', 'desc')
            ->limit(5)
            ->get();

        return view('kasir.dashboard', compact('todayOrdersCount', 'todaySalesAmount', 'topProducts'));
    }
}
