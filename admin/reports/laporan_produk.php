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

$kategori_id = $_GET['kategori'] ?? '';
$supplier_id = $_GET['supplier'] ?? '';

$query = "
    SELECT p.*, c.nama_kategori, s.nama_supplier 
    FROM produk p 
    LEFT JOIN kategori c ON p.kategori_id = c.id
    LEFT JOIN suppliers s ON p.supplier_id = s.id
    WHERE p.deleted_at IS NULL
";
$params = [];
if ($kategori_id) {
    $query .= " AND p.kategori_id = ?";
    $params[] = $kategori_id;
}
if ($supplier_id) {
    $query .= " AND p.supplier_id = ?";
    $params[] = $supplier_id;
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();

$total_produk = count($products);
$total_nilai_stok = 0;
foreach($products as $p) {
    $total_nilai_stok += ($p['stok'] * $p['harga']);
}

// Generate HTML Content
ob_start();
?>
<?php if (!$is_print && !$is_pdf): ?>
    <?php require_once __DIR__ . '/../../includes/header.php'; ?>
    <div class="d-flex">
    <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>
    <div class="flex-grow-1 p-4 bg-light">
        <h2 class="fw-bold mb-4">Laporan Produk</h2>
        
        <div class="card mb-4 border-0 shadow-sm d-print-none">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <select name="kategori" class="form-select">
                            <option value="">Semua Kategori</option>
                            <?php 
                            $kats = $pdo->query("SELECT * FROM kategori WHERE deleted_at IS NULL")->fetchAll();
                            foreach($kats as $k): ?>
                                <option value="<?= $k['id'] ?>" <?= $kategori_id == $k['id'] ? 'selected' : '' ?>><?= $k['nama_kategori'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="supplier" class="form-select">
                            <option value="">Semua Supplier</option>
                            <?php 
                            $sups = $pdo->query("SELECT * FROM suppliers WHERE deleted_at IS NULL")->fetchAll();
                            foreach($sups as $s): ?>
                                <option value="<?= $s['id'] ?>" <?= $supplier_id == $s['id'] ? 'selected' : '' ?>><?= $s['nama_supplier'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="?print=1&kategori=<?= $kategori_id ?>&supplier=<?= $supplier_id ?>" target="_blank" class="btn btn-secondary"><i class="fas fa-print"></i> Print</a>
                        <a href="?pdf=1&kategori=<?= $kategori_id ?>&supplier=<?= $supplier_id ?>" class="btn btn-danger"><i class="fas fa-file-pdf"></i> PDF</a>
                        <button type="button" class="btn btn-info text-white" onclick="generateAISummary()"><i class="fas fa-robot"></i> Ringkasan AI</button>
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
                    <h2 class="text-center">Laporan Produk</h2>
                    <hr>
                <?php endif; ?>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Produk</th>
                            <th>Kategori</th>
                            <th>Supplier</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Total Nilai</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no=1; foreach($products as $p): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($p['nama_produk']) ?></td>
                            <td><?= htmlspecialchars($p['nama_kategori'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($p['nama_supplier'] ?? '-') ?></td>
                            <td>Rp <?= number_format($p['harga'], 0, ',', '.') ?></td>
                            <td><?= $p['stok'] ?></td>
                            <td>Rp <?= number_format($p['harga'] * $p['stok'], 0, ',', '.') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="6" class="text-end">Total Nilai Stok:</th>
                            <th>Rp <?= number_format($total_nilai_stok, 0, ',', '.') ?></th>
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
                type: 'produk',
                summary_data: {
                    total_produk: <?= $total_produk ?>,
                    nilai_stok: <?= $total_nilai_stok ?>
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
    $mpdf->Output('Laporan_Produk.pdf', 'I');
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
