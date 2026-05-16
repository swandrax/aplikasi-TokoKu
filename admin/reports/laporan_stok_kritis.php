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

$threshold = $_GET['threshold'] ?? 10;

$query = "
    SELECT p.*, c.nama_kategori, 
    (SELECT SUM(quantity) FROM order_items oi JOIN orders o ON oi.order_id = o.id WHERE oi.product_id = p.id AND o.created_at >= date('now', '-30 days')) as sales_30d
    FROM produk p
    LEFT JOIN kategori c ON p.kategori_id = c.id
    WHERE p.stok <= ? AND p.deleted_at IS NULL
    ORDER BY p.stok ASC
";
$stmt = $pdo->prepare($query);
$stmt->execute([$threshold]);
$products = $stmt->fetchAll();

ob_start();
?>
<?php if (!$is_print && !$is_pdf): ?>
    <?php require_once __DIR__ . '/../../includes/header.php'; ?>
    <div class="d-flex">
    <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>
    <div class="flex-grow-1 p-4 bg-light">
        <h2 class="fw-bold mb-4">Laporan Stok Kritis</h2>
        
        <div class="card mb-4 border-0 shadow-sm d-print-none">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label>Batas Stok (Threshold)</label>
                        <input type="number" name="threshold" class="form-control" value="<?= htmlspecialchars($threshold) ?>">
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">Filter</button>
                        <a href="?print=1&threshold=<?= $threshold ?>" target="_blank" class="btn btn-secondary me-2"><i class="fas fa-print"></i> Print</a>
                        <a href="?pdf=1&threshold=<?= $threshold ?>" class="btn btn-danger"><i class="fas fa-file-pdf"></i> PDF</a>
                    </div>
                </form>
            </div>
        </div>
<?php endif; ?>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <?php if($is_print): ?>
                    <h2 class="text-center">Laporan Stok Kritis</h2>
                    <p class="text-center">Threshold: <= <?= $threshold ?></p>
                    <hr>
                <?php endif; ?>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Produk</th>
                            <th>Kategori</th>
                            <th>Stok Tersisa</th>
                            <th>Avg Penjualan / 30 Hari</th>
                            <th>Estimasi Habis</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no=1; foreach($products as $p): 
                            $avg_daily = ($p['sales_30d'] ?? 0) / 30;
                            $estimasi = $avg_daily > 0 ? ceil($p['stok'] / $avg_daily) . ' hari' : 'Belum ada tren';
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($p['nama_produk']) ?></td>
                            <td><?= htmlspecialchars($p['nama_kategori'] ?? '-') ?></td>
                            <td><span class="badge bg-danger"><?= $p['stok'] ?></span></td>
                            <td><?= $p['sales_30d'] ?? 0 ?> items</td>
                            <td><?= $estimasi ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

<?php if (!$is_print && !$is_pdf): ?>
    </div>
    </div>
    <?php require_once __DIR__ . '/../../includes/footer.php'; ?>
<?php endif; ?>

<?php
$htmlContent = ob_get_clean();

if ($is_pdf) {
    $mpdf->WriteHTML($html . $htmlContent);
    $mpdf->Output('Laporan_Stok_Kritis.pdf', 'I');
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
