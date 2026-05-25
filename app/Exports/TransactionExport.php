<?php

namespace App\Exports;

use App\Models\Order;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class TransactionExport
{
    /**
     * Generate an Excel spreadsheet and save it temporarily.
     * Returns the absolute path of the generated Excel file.
     */
    public function export(array $filters = []): string
    {
        $query = Order::with(['user', 'kasir'])->orderBy('created_at', 'desc');

        // Apply filters
        if (!empty($filters['start_date'])) {
            $query->whereDate('created_at', '>=', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $query->whereDate('created_at', '<=', $filters['end_date']);
        }
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['kasir_id'])) {
            $query->where('kasir_id', $filters['kasir_id']);
        }

        $orders = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Transaksi');

        // 1. Set Title Block
        $sheet->mergeCells('A1:G1');
        $sheet->setCellValue('A1', 'LAPORAN TRANSAKSI PENJUALAN TOKOKU');
        $sheet->getStyle('A1')->getFont()->setSize(16)->setBold(true);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A2:G2');
        $dateStr = 'Periode: Semua Waktu';
        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $dateStr = "Periode: {$filters['start_date']} s/d {$filters['end_date']}";
        }
        $sheet->setCellValue('A2', $dateStr);
        $sheet->getStyle('A2')->getFont()->setSize(10)->setItalic(true);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // 2. Set Headers
        $headers = ['No', 'Tanggal', 'No. Order', 'Kasir', 'Pembeli', 'Total Penjualan', 'Status'];
        $sheet->fromArray($headers, null, 'A4');
        
        // Header Styling
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F46E5'], // Beautiful indigo color
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'D1D5DB'],
                ],
            ],
        ];
        $sheet->getStyle('A4:G4')->applyFromArray($headerStyle);
        $sheet->getRowDimension(4)->setRowHeight(28);

        // 3. Populate Data Rows
        $row = 5;
        $no = 1;
        foreach ($orders as $order) {
            $sheet->setCellValue("A{$row}", $no++);
            $sheet->setCellValue("B{$row}", $order->created_at->timezone('Asia/Jakarta')->format('Y-m-d H:i'));
            $sheet->setCellValue("C{$row}", $order->order_number);
            $sheet->setCellValue("D{$row}", $order->kasir ? $order->kasir->name : 'Self Checkout');
            $sheet->setCellValue("E{$row}", $order->user->name);
            $sheet->setCellValue("F{$row}", (float) $order->total);
            $sheet->setCellValue("G{$row}", strtoupper($order->status));

            // Formatting price cell
            $sheet->getStyle("F{$row}")->getNumberFormat()->setFormatCode('"Rp "#,##0');

            // Alignments
            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("C{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle("G{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Alternating Row Colors (Zebra)
            if ($row % 2 === 0) {
                $sheet->getStyle("A{$row}:G{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F9FAFB');
            }

            // Cell borders
            $sheet->getStyle("A{$row}:G{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('E5E7EB');

            $row++;
        }

        // 4. Summary Row (Total Sales)
        $totalRow = $row;
        $sheet->mergeCells("A{$totalRow}:E{$totalRow}");
        $sheet->setCellValue("A{$totalRow}", 'TOTAL PENDAPATAN');
        $sheet->getStyle("A{$totalRow}")->getFont()->setBold(true);
        $sheet->getStyle("A{$totalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        
        $sheet->setCellValue("F{$totalRow}", "=SUM(F5:F" . ($totalRow - 1) . ")");
        $sheet->getStyle("F{$totalRow}")->getFont()->setBold(true);
        $sheet->getStyle("F{$totalRow}")->getNumberFormat()->setFormatCode('"Rp "#,##0');
        
        $sheet->getStyle("A{$totalRow}:G{$totalRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('EEF2F6');
        $sheet->getStyle("A{$totalRow}:G{$totalRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('CBD5E1');

        // 5. Auto size column widths
        foreach (range('A', 'G') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // 6. Write and Save file to storage
        $writer = new Xlsx($spreadsheet);
        $tempDir = storage_path('app/temp');
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $filePath = $tempDir . '/laporan_transaksi_' . time() . '.xlsx';
        $writer->save($filePath);

        return $filePath;
    }
}
