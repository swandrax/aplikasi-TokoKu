<?php

namespace App\Services;

use App\Models\Order;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\Log;

class ReceiptService
{
    /**
     * Generate PDF Receipt for a completed order.
     * Returns the absolute path to the saved PDF file.
     */
    public function generatePdf(Order $order): string
    {
        $dir = storage_path('app/receipts');
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        $filePath = $dir . "/struk_{$order->order_number}.pdf";

        // If file already exists, return it (caching)
        if (file_exists($filePath)) {
            // Check if older than 24 hours, delete and recreate
            if (filemtime($filePath) < (time() - 86400)) {
                @unlink($filePath);
            } else {
                return $filePath;
            }
        }

        // Clean up other old receipts (> 24 hours) as requested
        $this->cleanupReceipts();

        // Render HTML content for PDF
        $html = $this->renderHtml($order);

        // Configure mPDF for narrow thermal paper (80mm width, dynamic height)
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => [80, 220], // 80mm width, 220mm height (mPDF handles auto-page breaking if needed)
            'margin_left' => 4,
            'margin_right' => 4,
            'margin_top' => 6,
            'margin_bottom' => 6,
        ]);

        $mpdf->WriteHTML($html);
        $mpdf->Output($filePath, \Mpdf\Output\Destination::FILE);

        return $filePath;
    }

    /**
     * Clean up receipt files older than 24 hours.
     */
    protected function cleanupReceipts()
    {
        try {
            $dir = storage_path('app/receipts');
            if (file_exists($dir)) {
                $files = glob($dir . '/*.pdf');
                foreach ($files as $file) {
                    if (filemtime($file) < (time() - 86400)) {
                        @unlink($file);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error("Failed to clean up old receipts: " . $e->getMessage());
        }
    }

    /**
     * Render the HTML structure of the thermal receipt.
     */
    public function renderHtml(Order $order): string
    {
        $orderDate = $order->created_at->timezone('Asia/Jakarta')->format('d F Y, H:i');
        $kasirName = $order->kasir ? $order->kasir->name : 'Self Checkout (Online)';
        
        $itemsHtml = '';
        foreach ($order->orderItems as $item) {
            $subtotalStr = number_format((float) $item->subtotal, 0, ',', '.');
            $priceStr = number_format((float) $item->price, 0, ',', '.');
            $itemsHtml .= "
            <tr>
                <td colspan='2' style='font-size: 11px; font-weight: bold;'>{$item->product_name}</td>
            </tr>
            <tr>
                <td style='font-size: 10px; color: #555;'>{$item->quantity} x Rp {$priceStr}</td>
                <td style='text-align: right; font-size: 10px; font-weight: bold;'>Rp {$subtotalStr}</td>
            </tr>
            ";
        }

        $subtotal = number_format((float) $order->subtotal, 0, ',', '.');
        $tax = number_format((float) $order->tax_amount, 0, ',', '.');
        $discount = number_format((float) $order->discount_amount, 0, ',', '.');
        $total = number_format((float) $order->total, 0, ',', '.');

        return "
        <html>
        <head>
            <style>
                body {
                    font-family: 'Courier New', Courier, monospace;
                    font-size: 11px;
                    color: #000;
                    margin: 0;
                    padding: 0;
                }
                .text-center { text-align: center; }
                .divider { border-top: 1px dashed #000; margin: 6px 0; }
                .double-divider { border-top: 2px double #000; margin: 6px 0; }
                table { width: 100%; border-collapse: collapse; }
                .label { font-size: 9px; color: #333; }
                .val { font-size: 9px; font-weight: bold; }
                .barcode-container {
                    margin-top: 12px;
                    text-align: center;
                }
            </style>
        </head>
        <body>
            <div class='text-center'>
                <strong style='font-size: 16px;'>TokoKu Store</strong><br>
                <span style='font-size: 9px;'>Jl. Merdeka Raya No. 45, Jakarta</span><br>
                <span style='font-size: 9px;'>Telp: (021) 8888-9999</span>
            </div>
            
            <div class='double-divider'></div>
            
            <table>
                <tr>
                    <td class='label'>No. Struk:</td>
                    <td class='val' style='text-align: right;'>{$order->order_number}</td>
                </tr>
                <tr>
                    <td class='label'>Tanggal:</td>
                    <td class='val' style='text-align: right;'>{$orderDate} WIB</td>
                </tr>
                <tr>
                    <td class='label'>Kasir:</td>
                    <td class='val' style='text-align: right;'>{$kasirName}</td>
                </tr>
                <tr>
                    <td class='label'>Pelanggan:</td>
                    <td class='val' style='text-align: right;'>{$order->user->name}</td>
                </tr>
            </table>
            
            <div class='divider'></div>
            
            <table>
                {$itemsHtml}
            </table>
            
            <div class='divider'></div>
            
            <table>
                <tr>
                    <td style='font-size: 10px;'>Subtotal:</td>
                    <td style='text-align: right; font-size: 10px;'>Rp {$subtotal}</td>
                </tr>
                <tr>
                    <td style='font-size: 10px;'>Pajak (11%):</td>
                    <td style='text-align: right; font-size: 10px;'>Rp {$tax}</td>
                </tr>
                <tr>
                    <td style='font-size: 10px;'>Diskon:</td>
                    <td style='text-align: right; font-size: 10px;'>-Rp {$discount}</td>
                </tr>
                <tr style='font-weight: bold;'>
                    <td style='font-size: 11px;'>TOTAL:</td>
                    <td style='text-align: right; font-size: 11px;'>Rp {$total}</td>
                </tr>
            </table>
            
            <div class='double-divider'></div>
            
            <div class='text-center' style='font-size: 10px;'>
                Metode Bayar: <strong>{$order->payment_method}</strong><br>
                <span style='font-size: 9px; color: #555;'>Status: Lunas</span><br><br>
                Terima kasih telah berbelanja!<br>
                Barang yang sudah dibeli tidak dapat ditukar/dikembalikan.
            </div>

            <div class='barcode-container'>
                <barcode code='{$order->order_number}' type='C39' height='0.8' class='barcode' />
                <br>
                <span style='font-size: 8px; font-weight: bold;'>* {$order->order_number} *</span>
            </div>
        </body>
        </html>
        ";
    }
}
