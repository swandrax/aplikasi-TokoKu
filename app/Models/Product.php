<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'image',
        'price_sell',
        'weight',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'price_sell' => 'decimal:2',
        'weight' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function stockBatches()
    {
        return $this->hasMany(StockBatch::class);
    }

    public function stockLogs()
    {
        return $this->hasMany(StockLog::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Helper to calculate active total available stock via FIFO batches
    public function getAvailableStockAttribute(): int
    {
        if (array_key_exists('available_stock', $this->attributes)) {
            return (int) $this->attributes['available_stock'];
        }
        return $this->stockBatches()->where('quantity_remaining', '>', 0)->sum('quantity_remaining');
    }
}
