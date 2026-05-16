<?php
// cron script to run daily at 23:59
// Example: 59 23 * * * php /path/to/aplikasi-toko-online/scripts/daily_report.php

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../vendor/autoload.php';

$date = date('Y-m-d');
$report_dir = __DIR__ . '/../admin/reports/archive/';

if (!is_dir($report_dir)) {
    mkdir($report_dir, 0777, true);
}

// Laporan Penjualan Hari Ini
$query = "
    SELECT o.*, c.nama as customer_name
    FROM orders o
    INNER JOIN customers c ON o.customer_id = c.id
    WHERE DATE(o.created_at) = ? AND o.status != 'cancelled'
";
$stmt = $pdo->prepare($query);
$stmt->execute([$date]);
$orders = $stmt->fetchAll();

$total_revenue = array_sum(array_column($orders, 'total_harga'));
$total_order = count($orders);

$html = '
<div style="text-align: center; border-bottom: 2px solid #2C3E50; padding-bottom: 10px; margin-bottom: 20px;">
    <h1 style="color: #2C3E50; margin: 0; font-family: sans-serif;">ZAVORA LIFE</h1>
    <p style="margin: 5px 0 0 0; color: #7f8c8d; font-family: sans-serif;">Laporan Penjualan Harian</p>
    <p style="margin: 0; color: #7f8c8d; font-size: 12px; font-family: sans-serif;">Tanggal: ' . $date . '</p>
</div>
<style>
    body { font-family: sans-serif; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #2C3E50; color: white; }
    tr:nth-child(even) { background-color: #f2f2f2; }
</style>
';

$html .= '<h3>Ringkasan</h3>';
$html .= '<p>Total Order: ' . $total_order . '</p>';
$html .= '<p>Total Revenue: Rp ' . number_format($total_revenue, 0, ',', '.') . '</p>';

$html .= '<h3>Detail Transaksi</h3>';
$html .= '<table><thead><tr><th>No Order</th><th>Waktu</th><th>Customer</th><th>Status</th><th>Total Belanja</th></tr></thead><tbody>';

foreach($orders as $o) {
    $html .= '<tr>';
    $html .= '<td>#' . $o['id'] . '</td>';
    $html .= '<td>' . date('H:i', strtotime($o['created_at'])) . '</td>';
    $html .= '<td>' . htmlspecialchars($o['customer_name']) . '</td>';
    $html .= '<td>' . ucfirst($o['status']) . '</td>';
    $html .= '<td>Rp ' . number_format($o['total_harga'], 0, ',', '.') . '</td>';
    $html .= '</tr>';
}

$html .= '</tbody></table>';

$mpdf = new \Mpdf\Mpdf();
$mpdf->WriteHTML($html);
$filename = $report_dir . 'Daily_Report_' . $date . '.pdf';
$mpdf->Output($filename, \Mpdf\Output\Destination::FILE);

echo "Report generated at: " . $filename . "\n";
