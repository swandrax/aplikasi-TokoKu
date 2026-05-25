<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    /**
     * Display a listing of orders processed by this cashier.
     */
    public function index()
    {
        $orders = Order::with('user')
            ->where('kasir_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('kasir.transactions.index', compact('orders'));
    }
}
