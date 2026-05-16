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

$kota = $_GET['kota'] ?? '';

$query = "
    SELECT c.*, COUNT(o.id) as total_order, SUM(o.total_harga) as total_belanja, MAX(o.created_at) as last_order
    FROM customers c
    LEFT JOIN orders o ON c.id = o.customer_id AND o.status != 'cancelled'
    WHERE c.deleted_at IS NULL
";
$params = [];

if ($kota) {
    $query .= " AND c.kota = ?";
    $params[] = $kota;
}
$query .= " GROUP BY c.id ORDER BY total_belanja DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$customers = $stmt->fetchAll();

ob_start();
?>
<?php if (!$is_print && !$is_pdf): ?>
    <?php require_once __DIR__ . '/../../includes/header.php'; ?>
    <div class="d-flex">
    <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>
    <div class="flex-grow-1 p-4 bg-light">
        <h2 class="fw-bold mb-4">Laporan Customer</h2>
        
        <div class="card mb-4 border-0 shadow-sm d-print-none">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <select name="kota" class="form-select">
                            <option value="">Semua Kota</option>
                            <?php 
                            $cities = $pdo->query("SELECT DISTINCT kota FROM customers WHERE kota != '' AND deleted_at IS NULL")->fetchAll();
                            foreach($cities as $c): ?>
                                <option value="<?= htmlspecialchars($c['kota']) ?>" <?= $kota == $c['kota'] ? 'selected' : '' ?>><?= htmlspecialchars($c['kota']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="?print=1&kota=<?= urlencode($kota) ?>" target="_blank" class="btn btn-secondary"><i class="fas fa-print"></i> Print</a>
                        <a href="?pdf=1&kota=<?= urlencode($kota) ?>" class="btn btn-danger"><i class="fas fa-file-pdf"></i> PDF</a>
                    </div>
                </form>
            </div>
        </div>
<?php endif; ?>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <?php if($is_print): ?>
                    <h2 class="text-center">Laporan Customer</h2>
                    <hr>
                <?php endif; ?>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Kota</th>
                            <th>Total Order</th>
                            <th>Total Belanja</th>
                            <th>Last Order</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no=1; foreach($customers as $c): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($c['nama']) ?></td>
                            <td><?= htmlspecialchars($c['email']) ?></td>
                            <td><?= htmlspecialchars($c['kota'] ?? '-') ?></td>
                            <td><?= $c['total_order'] ?></td>
                            <td>Rp <?= number_format($c['total_belanja'], 0, ',', '.') ?></td>
                            <td><?= $c['last_order'] ? date('d M Y', strtotime($c['last_order'])) : '-' ?></td>
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
    $mpdf->Output('Laporan_Customer.pdf', 'I');
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
