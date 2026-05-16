<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    die("Akses ditolak.");
}

require_once __DIR__ . '/../../includes/db.php';

$is_print = isset($_GET['print']);
$is_pdf = isset($_GET['pdf']);

if ($is_pdf) {
    require_once __DIR__ . '/../../vendor/autoload.php';
    $mpdf = new \Mpdf\Mpdf();
    $html = require __DIR__ . '/../../includes/pdf_header.php';
}

$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-t');
$status = $_GET['status'] ?? '';

$query = "
    SELECT o.*, c.nama as customer_name,
    (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as total_items
    FROM orders o
    INNER JOIN customers c ON o.customer_id = c.id
    WHERE DATE(o.created_at) >= ? AND DATE(o.created_at) <= ?
";
$params = [$start_date, $end_date];

if ($status) {
    $query .= " AND o.status = ?";
    $params[] = $status;
}
$query .= " ORDER BY o.created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$orders = $stmt->fetchAll();

$total_order = count($orders);
$total_revenue = 0;
$total_cancelled = 0;
foreach($orders as $o) {
    if ($o['status'] !== 'cancelled') {
        $total_revenue += $o['total_harga'];
    } else {
        $total_cancelled++;
    }
}

// Get top product in this period
$stmt_top = $pdo->prepare("
    SELECT p.nama_produk FROM order_items oi
    JOIN produk p ON oi.product_id = p.id
    JOIN orders o ON oi.order_id = o.id
    WHERE DATE(o.created_at) >= ? AND DATE(o.created_at) <= ? AND o.status != 'cancelled'
    GROUP BY p.id ORDER BY SUM(oi.quantity) DESC LIMIT 1
");
$stmt_top->execute([$start_date, $end_date]);
$top_produk = $stmt_top->fetchColumn() ?: '-';

// Get top customer in this period
$stmt_cust = $pdo->prepare("
    SELECT c.nama FROM orders o
    JOIN customers c ON o.customer_id = c.id
    WHERE DATE(o.created_at) >= ? AND DATE(o.created_at) <= ? AND o.status != 'cancelled'
    GROUP BY c.id ORDER BY COUNT(o.id) DESC LIMIT 1
");
$stmt_cust->execute([$start_date, $end_date]);
$top_customer = $stmt_cust->fetchColumn() ?: '-';


ob_start();
?>
<?php if (!$is_print && !$is_pdf): ?>
    <?php require_once __DIR__ . '/../../includes/header.php'; ?>
    <div class="d-flex">
    <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>
    <div class="flex-grow-1 p-4 bg-light">
        <h2 class="fw-bold mb-4">Laporan Penjualan</h2>
        
        <div class="card mb-4 border-0 shadow-sm d-print-none">
            <div class="card-body">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" name="start_date" class="form-control" value="<?= htmlspecialchars($start_date) ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tanggal Akhir</label>
                        <input type="date" name="end_date" class="form-control" value="<?= htmlspecialchars($end_date) ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="pending" <?= $status == 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="processing" <?= $status == 'processing' ? 'selected' : '' ?>>Processing</option>
                            <option value="shipped" <?= $status == 'shipped' ? 'selected' : '' ?>>Shipped</option>
                            <option value="delivered" <?= $status == 'delivered' ? 'selected' : '' ?>>Delivered</option>
                            <option value="cancelled" <?= $status == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="?print=1&start_date=<?= $start_date ?>&end_date=<?= $end_date ?>&status=<?= $status ?>" target="_blank" class="btn btn-secondary"><i class="fas fa-print"></i></a>
                        <a href="?pdf=1&start_date=<?= $start_date ?>&end_date=<?= $end_date ?>&status=<?= $status ?>" class="btn btn-danger"><i class="fas fa-file-pdf"></i></a>
                        <button type="button" class="btn btn-info text-white" onclick="generateAISummary()"><i class="fas fa-robot"></i> AI</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4" id="aiSummaryCard" style="display:none;">
            <div class="card-body">
                <h5 class="fw-bold text-primary"><i class="fas fa-robot"></i> Ringkasan Eksekutif AI</h5>
                <div id="aiSummaryResult" style="white-space: pre-wrap;"></div>
            </div>
        </div>
<?php endif; ?>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <?php if($is_print): ?>
                    <h2 class="text-center">Laporan Penjualan</h2>
                    <p class="text-center">Periode: <?= $start_date ?> s/d <?= $end_date ?></p>
                    <hr>
                <?php endif; ?>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No Order</th>
                            <th>Tanggal</th>
                            <th>Customer</th>
                            <th>Total Item</th>
                            <th>Status</th>
                            <th>Total Belanja</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($orders)): ?>
                            <tr><td colspan="6" class="text-center">Tidak ada data</td></tr>
                        <?php endif; ?>
                        <?php foreach($orders as $o): ?>
                        <tr>
                            <td>#<?= $o['id'] ?></td>
                            <td><?= date('d M Y', strtotime($o['created_at'])) ?></td>
                            <td><?= htmlspecialchars($o['customer_name']) ?></td>
                            <td><?= $o['total_items'] ?></td>
                            <td><?= ucfirst($o['status']) ?></td>
                            <td>Rp <?= number_format($o['total_harga'], 0, ',', '.') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="5" class="text-end">Total Revenue (diluar Cancelled):</th>
                            <th>Rp <?= number_format($total_revenue, 0, ',', '.') ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

<?php if (!$is_print && !$is_pdf): ?>
    </div>
    </div>
    <script>
    function generateAISummary() {
        document.getElementById('aiSummaryCard').style.display = 'block';
        document.getElementById('aiSummaryResult').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating summary...';
        
        fetch('../../api/report_summary.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                type: 'penjualan',
                summary_data: {
                    periode: '<?= $start_date ?> sampai <?= $end_date ?>',
                    total_order: <?= $total_order ?>,
                    revenue: <?= $total_revenue ?>,
                    cancelled: <?= $total_cancelled ?>,
                    top_produk: '<?= htmlspecialchars($top_produk) ?>',
                    top_customer: '<?= htmlspecialchars($top_customer) ?>'
                }
            })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                document.getElementById('aiSummaryResult').innerHTML = data.message;
            } else {
                document.getElementById('aiSummaryResult').innerHTML = "Error: " + data.message;
            }
        });
    }
    </script>
    <?php require_once __DIR__ . '/../../includes/footer.php'; ?>
<?php endif; ?>

<?php
$htmlContent = ob_get_clean();

if ($is_pdf) {
    $mpdf->WriteHTML($html . $htmlContent);
    $mpdf->Output('Laporan_Penjualan.pdf', 'I');
    exit;
} elseif ($is_print) {
    echo '<!DOCTYPE html><html><head><title>Print</title><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"></head><body onload="window.print()">';
    echo $htmlContent;
    echo '</body></html>';
    exit;
} else {
    echo $htmlContent;
}
?>
