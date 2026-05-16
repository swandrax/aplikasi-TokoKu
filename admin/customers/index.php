<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    die("Akses ditolak.");
}

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

// Soft delete functionality (for delete action)
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("UPDATE customers SET deleted_at = datetime('now'), is_active = 0 WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: /admin/customers/index.php?msg=deleted");
    exit;
}

$stmt = $pdo->query("SELECT * FROM customers WHERE deleted_at IS NULL ORDER BY id DESC");
$customers = $stmt->fetchAll();
?>

<div class="d-flex">
    <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>

    <div class="flex-grow-1 p-4 bg-light">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold m-0">Master Customer</h2>
            <a href="create.php" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Customer</a>
        </div>

        <?php if(isset($_GET['msg'])): ?>
            <div class="alert alert-success">Operasi berhasil!</div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <table id="customersTable" class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Telepon</th>
                            <th>Kota</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($customers as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['nama']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['telepon'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($row['kota'] ?? '-') ?></td>
                            <td>
                                <?= $row['is_active'] ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-secondary">Non-Aktif</span>' ?>
                            </td>
                            <td>
                                <a href="detail.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info text-white" title="Detail"><i class="fas fa-eye"></i></a>
                                <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning text-white" title="Edit"><i class="fas fa-edit"></i></a>
                                <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger btn-delete" title="Hapus"><i class="fas fa-trash"></i></a>
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
    $('#customersTable').DataTable({
        dom: 'Bfrtip',
        buttons: ['excel', 'pdf', 'print']
    });
});
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
