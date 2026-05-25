<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Exports\TransactionExport;
use App\Exports\ReportPdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Display report listings and filters.
     */
    public function index(Request $request)
    {
        $cashiers = User::query()->whereIn('role', ['admin', 'kasir'])->get();

        $query = Order::query()->with(['user', 'kasir'])->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('kasir_id')) {
            $query->where('kasir_id', $request->kasir_id);
        }

        $orders = $query->paginate(20)->withQueryString();

        return view('admin.reports.index', compact('orders', 'cashiers'));
    }

    /**
     * Export transaction reports in Excel (.xlsx) format.
     */
    public function exportExcel(Request $request, TransactionExport $exporter)
    {
        $filters = $request->only(['start_date', 'end_date', 'status', 'kasir_id']);
        $filePath = $exporter->export($filters);

        return response()->download($filePath, 'Laporan-Transaksi-TokoKu.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Export executive summaries in PDF format.
     */
    public function exportPdf(Request $request, ReportPdf $exporter)
    {
        $filters = $request->only(['start_date', 'end_date', 'status']);
        $filePath = $exporter->export($filters);

        return response()->download($filePath, 'Laporan-Penjualan-Eksekutif.pdf', [
            'Content-Type' => 'application/pdf',
        ])->deleteFileAfterSend(true);
    }
}
