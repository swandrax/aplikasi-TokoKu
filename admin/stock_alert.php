<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    die("Akses ditolak.");
}

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/header.php';

$stmt = $pdo->query("
    SELECT p.*, c.nama_kategori 
    FROM produk p 
    JOIN kategori c ON p.kategori_id = c.id
    WHERE p.stok <= 10 AND p.deleted_at IS NULL
    ORDER BY p.stok ASC
");
$alerts = $stmt->fetchAll();
?>

<div class="d-flex">
    <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>

    <div class="flex-grow-1 p-4 bg-light">
        <h2 class="fw-bold mb-4 text-danger"><i class="fas fa-exclamation-triangle"></i> Peringatan Stok Menipis</h2>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Gambar</th>
                            <th>Nama Produk</th>
                            <th>Kategori</th>
                            <th>Stok Tersisa</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($alerts)): ?>
                        <tr><td colspan="6" class="text-center">Semua stok produk aman (> 10)</td></tr>
                        <?php else: ?>
                            <?php foreach($alerts as $row): ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td>
                                    <?php if($row['gambar']): ?>
                                        <img src="../public/storage/images/<?= htmlspecialchars($row['gambar']) ?>" alt="gambar" style="height: 40px; object-fit: cover;">
                                    <?php else: ?>
                                        - 
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($row['nama_produk']) ?></td>
                                <td><?= htmlspecialchars($row['nama_kategori']) ?></td>
                                <td><span class="badge bg-danger"><?= htmlspecialchars($row['stok']) ?></span></td>
                                <td>
                                    <!-- Aksi bisa restock (misal edit produk) -->
                                    <a href="#" class="btn btn-sm btn-primary">Restock</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
