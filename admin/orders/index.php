<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    die("Akses ditolak.");
}

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

// Fetch orders
$stmt = $pdo->query("
    SELECT o.*, c.nama_kota 
    FROM orders o 
    LEFT JOIN cities c ON o.city_id = c.id 
    WHERE o.deleted_at IS NULL 
    ORDER BY o.id DESC
");
$orders = $stmt->fetchAll();
?>

<div class="d-flex">
    <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>

    <div class="flex-grow-1 p-4 bg-light">
        <h2 class="fw-bold mb-4">Kelola Pesanan</h2>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <table id="ordersTable" class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID Order</th>
                            <th>Tanggal</th>
                            <th>Penerima</th>
                            <th>Kota</th>
                            <th>Total Harga</th>
                            <th>Metode Pembayaran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($orders as $row): ?>
                        <tr>
                            <td>#<?= $row['id'] ?></td>
                            <td><?= date('d M Y H:i', strtotime($row['created_at'])) ?></td>
                            <td><?= htmlspecialchars($row['nama_penerima']) ?></td>
                            <td><?= htmlspecialchars($row['nama_kota'] ?? '-') ?></td>
                            <td>Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></td>
                            <td><?= htmlspecialchars($row['metode_pembayaran']) ?></td>
                            <td>
                                <button class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></button>
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
    $('#ordersTable').DataTable({
        dom: 'Bfrtip',
        buttons: ['excel', 'pdf', 'print'],
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]]
    });
});
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
