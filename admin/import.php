<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    die("Akses ditolak.");
}

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/header.php';

$successCount = 0;
$failCount = 0;
$failedRows = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file_csv'])) {
    $file = $_FILES['file_csv'];
    
    if ($file['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        if (strtolower($ext) === 'csv') {
            $handle = fopen($file['tmp_name'], 'r');
            
            // Skip header
            fgetcsv($handle);
            
            $rowNum = 2; // Starting from line 2
            
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                // Format expected: nama_produk, detail, harga, stok, berat, kategori_id
                if (count($data) >= 6) {
                    $nama = trim($data[0]);
                    $detail = trim($data[1]);
                    $harga = floatval($data[2]);
                    $stok = intval($data[3]);
                    $berat = floatval($data[4]);
                    $kategori_id = intval($data[5]);
                    
                    if (empty($nama) || $harga <= 0 || $stok < 0) {
                        $failCount++;
                        $failedRows[] = "Baris $rowNum: Validasi gagal (nama kosong, atau harga/stok tidak valid)";
                    } else {
                        try {
                            $stmt = $pdo->prepare("INSERT INTO produk (nama_produk, detail, harga, stok, berat, kategori_id, user_id, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, 1, 1, datetime('now'), datetime('now'))");
                            $stmt->execute([$nama, $detail, $harga, $stok, $berat, $kategori_id]);
                            $successCount++;
                        } catch (Exception $e) {
                            $failCount++;
                            $failedRows[] = "Baris $rowNum: DB Error - " . $e->getMessage();
                        }
                    }
                } else {
                    $failCount++;
                    $failedRows[] = "Baris $rowNum: Format kolom tidak sesuai";
                }
                $rowNum++;
            }
            fclose($handle);
        } else {
            $failedRows[] = "Format file harus CSV.";
        }
    } else {
        $failedRows[] = "Gagal mengupload file.";
    }
}
?>

<div class="d-flex">
    <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>

    <div class="flex-grow-1 p-4 bg-light">
        <h2 class="fw-bold mb-4">Import Data Produk</h2>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="file_csv" class="form-label">Upload File CSV</label>
                        <input class="form-control" type="file" id="file_csv" name="file_csv" accept=".csv" required>
                        <div class="form-text">Format: nama_produk, detail, harga, stok, berat, kategori_id</div>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-upload"></i> Import</button>
                </form>
            </div>
        </div>

        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <div class="row">
            <div class="col-md-6">
                <div class="alert alert-success">
                    <h5 class="alert-heading"><i class="fas fa-check-circle"></i> Berhasil</h5>
                    <p class="mb-0"><?= $successCount ?> baris data berhasil diimport.</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="alert alert-danger">
                    <h5 class="alert-heading"><i class="fas fa-times-circle"></i> Gagal (<?= $failCount ?>)</h5>
                    <?php if(!empty($failedRows)): ?>
                    <ul class="mb-0 mt-2">
                        <?php foreach($failedRows as $fail): ?>
                            <li><?= htmlspecialchars($fail) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
