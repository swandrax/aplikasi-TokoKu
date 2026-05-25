<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockBatch;
use App\Models\StockLog;
use App\Mail\LowStockAlertMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class StockService
{
    /**
     * Deduct stock using FIFO principle.
     * Returns an array containing the operation status, batches used, and any shortage.
     */
    public function deductStock(int $productId, int $qty, int $orderId): array
    {
        return DB::transaction(function () use ($productId, $qty, $orderId) {
            $product = Product::findOrFail($productId);
            $available = $this->getAvailableStock($productId);

            if ($available < $qty) {
                return [
                    'success' => false,
                    'batches_used' => [],
                    'shortage' => $qty - $available,
                ];
            }

            // Fetch active stock batches sorted by batch_date ASC (oldest first)
            $batches = StockBatch::query()->where('product_id', $productId)
                ->where('quantity_remaining', '>', 0)
                ->orderBy('batch_date', 'asc')
                ->lockForUpdate() // Prevent race conditions
                ->get();

            $remainingToAllocate = $qty;
            $batchesUsed = [];

            foreach ($batches as $batch) {
                if ($remainingToAllocate <= 0) break;

                $allocatedQty = min($batch->quantity_remaining, $remainingToAllocate);
                
                // Deduct from batch
                $batch->quantity_remaining -= $allocatedQty;
                $batch->save();

                $batchesUsed[] = [
                    'stock_batch_id' => $batch->id,
                    'quantity' => $allocatedQty,
                    'price' => $product->price_sell,
                ];

                // Create stock log for 'out'
                StockLog::create([
                    'product_id' => $productId,
                    'stock_batch_id' => $batch->id,
                    'type' => 'out',
                    'quantity' => $allocatedQty,
                    'description' => "Pengurangan stok penjualan (Order ID: #{$orderId})",
                    'reference_id' => $orderId,
                    'created_by' => Auth::id() ?? 1, // Fallback to system / admin
                ]);

                $remainingToAllocate -= $allocatedQty;
            }

            // Check if stock became low after deduction (< 5)
            $newStock = $this->getAvailableStock($productId);
            if ($newStock < 5) {
                $this->triggerLowStockAlert($product, $newStock);
            }

            return [
                'success' => true,
                'batches_used' => $batchesUsed,
                'shortage' => 0,
            ];
        });
    }

    /**
     * Add a new stock batch to a product (FIFO).
     */
    public function addStock(int $productId, array $data): StockBatch
    {
        return DB::transaction(function () use ($productId, $data) {
            $batch = StockBatch::create([
                'product_id' => $productId,
                'quantity_initial' => $data['quantity'],
                'quantity_remaining' => $data['quantity'],
                'purchase_price' => $data['purchase_price'],
                'batch_date' => $data['batch_date'] ?? now(),
                'supplier_name' => $data['supplier_name'] ?? null,
                'note' => $data['note'] ?? null,
                'created_by' => Auth::id() ?? 1,
            ]);

            // Create stock log for 'in'
            StockLog::create([
                'product_id' => $productId,
                'stock_batch_id' => $batch->id,
                'type' => 'in',
                'quantity' => $data['quantity'],
                'description' => $data['description'] ?? 'Pemasukan stok batch baru (FIFO)',
                'reference_id' => $batch->id,
                'created_by' => Auth::id() ?? 1,
            ]);

            return $batch;
        });
    }

    /**
     * Calculate available remaining stock for a product.
     */
    public function getAvailableStock(int $productId): int
    {
        return StockBatch::query()->where('product_id', $productId)
            ->where('quantity_remaining', '>', 0)
            ->sum('quantity_remaining');
    }

    /**
     * Get the stock transaction logs of a product.
     */
    public function getStockHistory(int $productId)
    {
        return StockLog::query()->where('product_id', $productId)
            ->with(['stockBatch', 'creator'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Check products that are low in stock (below threshold).
     */
    public function checkLowStock(int $threshold = 5)
    {
        return Product::query()->whereHas('stockBatches', function ($query) {
            $query->select('product_id')
                ->groupBy('product_id');
        })
        ->get()
        ->filter(function ($product) use ($threshold) {
            return $this->getAvailableStock($product->id) < $threshold;
        });
    }

    /**
     * Triggers a low stock warning email to administrator.
     */
    protected function triggerLowStockAlert(Product $product, int $currentStock)
    {
        try {
            $adminEmail = config('mail.from.address', 'admin@tokoku.com');
            Mail::to($adminEmail)->send(new LowStockAlertMail($product, $currentStock));
        } catch (\Exception $e) {
            Log::error("Failed to send low stock alert for product {$product->name}: " . $e->getMessage());
        }
    }
}
