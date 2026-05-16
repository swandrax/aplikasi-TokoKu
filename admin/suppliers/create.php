<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    die("Akses ditolak.");
}

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_supplier = $_POST['nama_supplier'] ?? '';
    $kontak = $_POST['kontak'] ?? '';
    $alamat = $_POST['alamat'] ?? '';
    $produk_disupply = $_POST['produk_disupply'] ?? '';
    
    if (empty($nama_supplier)) {
        $error = "Nama supplier wajib diisi!";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO suppliers (nama_supplier, kontak, alamat, produk_disupply, is_active, created_at, updated_at) VALUES (?, ?, ?, ?, 1, datetime('now'), datetime('now'))");
            $stmt->execute([$nama_supplier, $kontak, $alamat, $produk_disupply]);
            header("Location: index.php?msg=success");
            exit;
        } catch (Exception $e) {
            $error = "Gagal menyimpan: " . $e->getMessage();
        }
    }
}
?>

<div class="d-flex">
    <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>

    <div class="flex-grow-1 p-4 bg-light">
        <h2 class="fw-bold mb-4">Tambah Supplier</h2>
        
        <?php if($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form action="" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Nama Supplier</label>
                        <input type="text" name="nama_supplier" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kontak</label>
                        <input type="text" name="kontak" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea name="alamat" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Produk yang disupply</label>
                        <input type="text" name="produk_disupply" class="form-control">
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="index.php" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
