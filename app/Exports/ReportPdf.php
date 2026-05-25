<?php

namespace App\Exports;

use App\Models\Order;
use App\Models\Product;
use Mpdf\Mpdf;
use Carbon\Carbon;

class ReportPdf
{
    /**
     * Generate PDF Report and return the absolute file path.
     */
    public function export(array $filters = []): string
    {
        $query = Order::with(['user', 'kasir'])->orderBy('created_at', 'desc');

        if (!empty($filters['start_date'])) {
            $query->whereDate('created_at', '>=', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $query->whereDate('created_at', '<=', $filters['end_date']);
        }
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $orders = $query->get();

        // 1. Calculate metrics
        $totalSales = $orders->where('status', 'paid')->sum('total');
        $totalOrdersCount = $orders->count();
        $paidCount = $orders->where('status', 'paid')->count();
        $pendingCount = $orders->where('status', 'pending')->count();
        $cancelledCount = $orders->where('status', 'cancelled')->count();

        // Assemble HTML
        $html = $this->renderHtml($orders, $filters, $totalSales, $totalOrdersCount, $paidCount, $pendingCount, $cancelledCount);

        // Build PDF
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 20,
            'margin_bottom' => 20,
        ]);

        $mpdf->SetHTMLHeader("<div style='text-align: right; font-size: 8px; color: #999; border-bottom: 1px solid #ddd; padding-bottom: 4px;'>Laporan Penjualan Eksekutif - TokoKu Store</div>");
        $mpdf->SetHTMLFooter("<div style='text-align: right; font-size: 8px; color: #999; border-top: 1px solid #ddd; padding-top: 4px;'>Halaman {PAGENO} dari {nbpg}</div>");

        $mpdf->WriteHTML($html);

        $tempDir = storage_path('app/temp');
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $filePath = $tempDir . '/laporan_penjualan_' . time() . '.pdf';
        $mpdf->Output($filePath, \Mpdf\Output\Destination::FILE);

        return $filePath;
    }

    /**
     * Render the executive PDF report in styled HTML.
     */
    protected function renderHtml($orders, $filters, $totalSales, $totalOrdersCount, $paidCount, $pendingCount, $cancelledCount): string
    {
        $startDate = !empty($filters['start_date']) ? Carbon::parse($filters['start_date'])->format('d M Y') : 'Awal Mulai';
        $endDate = !empty($filters['end_date']) ? Carbon::parse($filters['end_date'])->format('d M Y') : 'Kini';
        $printDate = Carbon::now()->timezone('Asia/Jakarta')->format('d F Y, H:i');

        $rowsHtml = '';
        $no = 1;
        foreach ($orders as $order) {
            $totalStr = number_format((float) $order->total, 0, ',', '.');
            $dateStr = $order->created_at->timezone('Asia/Jakarta')->format('d-m-Y H:i');
            $kasirStr = $order->kasir ? $order->kasir->name : 'Online';
            $statusLabel = match ($order->status) {
                'paid' => "<span style='color: #10b981; font-weight: bold;'>PAID</span>",
                'cancelled' => "<span style='color: #ef4444; font-weight: bold;'>CANCELLED</span>",
                default => "<span style='color: #f59e0b; font-weight: bold;'>PENDING</span>",
            };

            $rowsHtml .= "
            <tr>
                <td style='text-align: center; padding: 8px; border-bottom: 1px solid #e5e7eb;'>{$no}</td>
                <td style='padding: 8px; border-bottom: 1px solid #e5e7eb;'>{$dateStr}</td>
                <td style='padding: 8px; border-bottom: 1px solid #e5e7eb; font-weight: bold;'>{$order->order_number}</td>
                <td style='padding: 8px; border-bottom: 1px solid #e5e7eb;'>{$order->user->name}</td>
                <td style='padding: 8px; border-bottom: 1px solid #e5e7eb;'>{$kasirStr}</td>
                <td style='text-align: right; padding: 8px; border-bottom: 1px solid #e5e7eb;'>Rp {$totalStr}</td>
                <td style='text-align: center; padding: 8px; border-bottom: 1px solid #e5e7eb;'>{$statusLabel}</td>
            </tr>
            ";
            $no++;
        }

        $totalSalesStr = number_format((float) $totalSales, 0, ',', '.');

        return "
        <html>
        <head>
            <style>
                body {
                    font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
                    color: #1f2937;
                    font-size: 11px;
                }
                .header-table { width: 100%; margin-bottom: 20px; border-bottom: 2px solid #4f46e5; padding-bottom: 10px; }
                .report-title { font-size: 20px; font-weight: bold; color: #4f46e5; }
                .meta-text { font-size: 9px; color: #6b7280; }
                
                .stats-container { width: 100%; margin-bottom: 25px; margin-top: 10px; }
                .stat-box {
                    background-color: #f3f4f6;
                    border: 1px solid #e5e7eb;
                    border-radius: 8px;
                    padding: 12px;
                    text-align: center;
                }
                .stat-title { font-size: 8px; text-transform: uppercase; color: #6b7280; margin-bottom: 4px; font-weight: bold; }
                .stat-value { font-size: 16px; font-weight: bold; color: #1f2937; }
                
                .data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
                .data-table th {
                    background-color: #4f46e5;
                    color: #ffffff;
                    text-align: left;
                    padding: 8px;
                    font-weight: bold;
                    font-size: 10px;
                }
                
                .total-row {
                    background-color: #f3f4f6;
                    font-weight: bold;
                    font-size: 12px;
                }
            </style>
        </head>
        <body>
            <!-- Header Block -->
            <table class='header-table'>
                <tr>
                    <td>
                        <span class='report-title'>TOKOKU E-COMMERCE</span><br>
                        <span style='font-size: 12px; color: #374151; font-weight: 500;'>Laporan Ringkasan Eksekutif Penjualan</span>
                    </td>
                    <td style='text-align: right; vertical-align: bottom;'>
                        <span class='meta-text'>Periode: {$startDate} s/d {$endDate}</span><br>
                        <span class='meta-text'>Dicetak Pada: {$printDate} WIB</span>
                    </td>
                </tr>
            </table>

            <!-- Stats Block -->
            <table class='stats-container' cellspacing='10'>
                <tr>
                    <td width='25%' class='stat-box'>
                        <div class='stat-title'>Total Pendapatan</div>
                        <div class='stat-value' style='color: #10b981;'>Rp {$totalSalesStr}</div>
                    </td>
                    <td width='25%' class='stat-box'>
                        <div class='stat-title'>Total Transaksi</div>
                        <div class='stat-value'>{$totalOrdersCount}</div>
                    </td>
                    <td width='25%' class='stat-box'>
                        <div class='stat-title'>Sukses / Paid</div>
                        <div class='stat-value' style='color: #10b981;'>{$paidCount}</div>
                    </td>
                    <td width='25%' class='stat-box'>
                        <div class='stat-title'>Pending & Cancel</div>
                        <div class='stat-value' style='color: #f59e0b;'>{$pendingCount} <span style='font-size:10px; color:#ef4444;'>/ {$cancelledCount}</span></div>
                    </td>
                </tr>
            </table>

            <!-- Detailed Transactions Table -->
            <h3 style='color: #374151; font-size: 12px; border-left: 3px solid #4f46e5; padding-left: 6px; margin-bottom: 8px;'>Rincian Transaksi Penjualan</h3>
            <table class='data-table'>
                <thead>
                    <tr>
                        <th style='width: 5%; text-align: center;'>No</th>
                        <th style='width: 15%;'>Tanggal</th>
                        <th style='width: 20%;'>No. Transaksi</th>
                        <th style='width: 20%;'>Pelanggan</th>
                        <th style='width: 15%;'>Kasir</th>
                        <th style='width: 15%; text-align: right;'>Total</th>
                        <th style='width: 10%; text-align: center;'>Status</th>
                    </tr>
                </thead>
                <tbody>
                    {$rowsHtml}
                    <tr class='total-row'>
                        <td colspan='5' style='text-align: right; padding: 10px; border-top: 2px solid #374151;'>TOTAL PENDAPATAN LUNAS (PAID)</td>
                        <td style='text-align: right; padding: 10px; border-top: 2px solid #374151; color: #10b981;'>Rp {$totalSalesStr}</td>
                        <td style='border-top: 2px solid #374151;'></td>
                    </tr>
                </tbody>
            </table>
        </body>
        </html>
        ";
    }
}
