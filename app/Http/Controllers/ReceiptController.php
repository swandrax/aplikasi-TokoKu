<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\ReceiptService;
use Illuminate\Support\Facades\Auth;

class ReceiptController extends Controller
{
    protected ReceiptService $receiptService;

    public function __construct(ReceiptService $receiptService)
    {
        $this->receiptService = $receiptService;
    }

    /**
     * Download the PDF receipt.
     */
    public function downloadPdf(Order $order)
    {
        $user = Auth::user();

        // Security check: Only owner, cashier, or admin can access the receipt
        if (!$user->isAdmin() && !$user->isKasir() && $order->user_id !== $user->id) {
            abort(403, 'Akses Ditolak. Anda tidak berhak melihat struk ini.');
        }

        $pdfPath = $this->receiptService->generatePdf($order);

        return response()->download($pdfPath, "Struk-Belanja-{$order->order_number}.pdf", [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * Print receipt (raw HTML formatted for standard browsers).
     */
    public function printHtml(Order $order)
    {
        $user = Auth::user();

        // Security check
        if (!$user->isAdmin() && !$user->isKasir() && $order->user_id !== $user->id) {
            abort(403, 'Akses Ditolak. Anda tidak berhak melihat struk ini.');
        }

        $html = $this->receiptService->renderHtml($order);

        // Append a script to automatically trigger browser printing window
        $html = str_replace(
            '</body>',
            '<script>window.onload = function() { window.print(); }</script></body>',
            $html
        );

        return response($html)->header('Content-Type', 'text/html');
    }
}
