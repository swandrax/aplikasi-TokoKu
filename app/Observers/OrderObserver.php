<?php

namespace App\Observers;

use App\Models\Order;
use App\Services\ActivityLogger;

class OrderObserver
{
    public function created(Order $order): void
    {
        $customerName = $order->user ? $order->user->name : 'Pembeli';
        ActivityLogger::log(
            'order.created',
            Order::class,
            $order->id,
            "Pesanan baru '{$order->order_number}' dibuat oleh {$customerName}."
        );
    }

    public function updated(Order $order): void
    {
        // Check if status changed
        if ($order->isDirty('status')) {
            $oldStatus = $order->getOriginal('status');
            $newStatus = $order->status;
            
            ActivityLogger::log(
                "order.status_{$newStatus}",
                Order::class,
                $order->id,
                "Status pesanan '{$order->order_number}' berubah dari '{$oldStatus}' menjadi '{$newStatus}'."
            );
        }
    }
}
