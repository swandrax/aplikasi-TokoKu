<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'quantity_initial',
        'quantity_remaining',
        'purchase_price',
        'batch_date',
        'supplier_name',
        'note',
        'created_by',
    ];

    protected $casts = [
        'quantity_initial' => 'integer',
        'quantity_remaining' => 'integer',
        'purchase_price' => 'decimal:2',
        'batch_date' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function stockLogs()
    {
        return $this->hasMany(StockLog::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
