<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    die("Akses ditolak.");
}

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Revenue 6 Bulan');

$sheet->setCellValue('A1', 'Bulan');
$sheet->setCellValue('B1', 'Revenue');
$sheet->getStyle('A1:B1')->getFont()->setBold(true);

$row = 2;
for ($i = 5; $i >= 0; $i--) {
    $m = date('m', strtotime("-$i month"));
    $y = date('Y', strtotime("-$i month"));
    $month_str = date('M Y', strtotime("-$i month"));
    
    $stmt = $pdo->prepare("SELECT SUM(total_harga) as rev FROM orders WHERE status = 'delivered' AND strftime('%m', created_at) = ? AND strftime('%Y', created_at) = ?");
    $stmt->execute([$m, $y]);
    $rev = $stmt->fetchColumn() ?? 0;
    
    $sheet->setCellValue('A' . $row, $month_str);
    $sheet->setCellValue('B' . $row, $rev);
    $row++;
}

// Top Products Sheet
$spreadsheet->createSheet();
$sheet2 = $spreadsheet->getSheet(1);
$sheet2->setTitle('Top Produk');

$sheet2->setCellValue('A1', 'Nama Produk');
$sheet2->setCellValue('B1', 'Total Terjual');
$sheet2->getStyle('A1:B1')->getFont()->setBold(true);

$stmt_top = $pdo->query("
    SELECT p.nama_produk, SUM(oi.quantity) as total_terjual
    FROM order_items oi
    JOIN produk p ON oi.product_id = p.id
    JOIN orders o ON oi.order_id = o.id
    WHERE o.status != 'cancelled'
    GROUP BY p.id
    ORDER BY total_terjual DESC
    LIMIT 10
");
$top_products = $stmt_top->fetchAll();

$row = 2;
foreach ($top_products as $prod) {
    $sheet2->setCellValue('A' . $row, $prod['nama_produk']);
    $sheet2->setCellValue('B' . $row, $prod['total_terjual']);
    $row++;
}

$spreadsheet->setActiveSheetIndex(0);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Analytics_Export.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
