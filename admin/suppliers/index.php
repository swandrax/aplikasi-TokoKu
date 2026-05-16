<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    die("Akses ditolak.");
}

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

$stmt = $pdo->query("SELECT * FROM suppliers WHERE deleted_at IS NULL ORDER BY id DESC");
$suppliers = $stmt->fetchAll();
?>

<div class="d-flex">
    <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>

    <div class="flex-grow-1 p-4 bg-light">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold m-0">Master Supplier</h2>
            <a href="create.php" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Supplier</a>
        </div>

        <?php if(isset($_GET['msg'])): ?>
            <div class="alert alert-success">Operasi berhasil!</div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <table id="suppliersTable" class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Nama Supplier</th>
                            <th>Kontak</th>
                            <th>Alamat</th>
                            <th>Produk Disupply</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($suppliers as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['nama_supplier']) ?></td>
                            <td><?= htmlspecialchars($row['kontak'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($row['alamat'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($row['produk_disupply'] ?? '-') ?></td>
                            <td>
                                <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning text-white" title="Edit"><i class="fas fa-edit"></i></a>
                                <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger btn-delete" title="Hapus"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    $('#suppliersTable').DataTable();
});
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
