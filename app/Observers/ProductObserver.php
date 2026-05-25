<?php

namespace App\Observers;

use App\Models\Product;
use App\Services\ActivityLogger;

class ProductObserver
{
    public function created(Product $product): void
    {
        $creatorName = $product->creator ? $product->creator->name : 'System';
        ActivityLogger::log(
            'product.created',
            Product::class,
            $product->id,
            "Produk '{$product->name}' berhasil dibuat oleh {$creatorName}."
        );
    }

    public function updated(Product $product): void
    {
        ActivityLogger::log(
            'product.updated',
            Product::class,
            $product->id,
            "Produk '{$product->name}' berhasil diperbarui."
        );
    }

    public function deleted(Product $product): void
    {
        ActivityLogger::log(
            'product.deleted',
            Product::class,
            $product->id,
            "Produk '{$product->name}' berhasil dihapus (soft delete)."
        );
    }
}
