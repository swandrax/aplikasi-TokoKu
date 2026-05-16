<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    die("Akses ditolak.");
}

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

// Grouping: tampilkan jumlah produk & total stok per kategori
$stmt = $pdo->query("
    SELECT c.nama_kategori, COUNT(p.id) as jumlah_produk, SUM(p.stok) as total_stok
    FROM kategori c
    LEFT JOIN produk p ON c.id = p.kategori_id AND p.deleted_at IS NULL
    WHERE c.deleted_at IS NULL
    GROUP BY c.id, c.nama_kategori
");
$data = $stmt->fetchAll();

$labels = [];
$jumlah_produk = [];
$total_stok = [];

foreach ($data as $row) {
    $labels[] = $row['nama_kategori'];
    $jumlah_produk[] = $row['jumlah_produk'];
    $total_stok[] = $row['total_stok'] ?? 0;
}
?>

<div class="d-flex">
    <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>

    <div class="flex-grow-1 p-4 bg-light">
        <h2 class="fw-bold mb-4">Laporan Produk per Kategori</h2>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <canvas id="categoryChart" height="100"></canvas>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Kategori</th>
                            <th>Jumlah Produk (Item Unik)</th>
                            <th>Total Stok (Pieces)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($data as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['nama_kategori']) ?></td>
                            <td><?= $row['jumlah_produk'] ?></td>
                            <td><?= $row['total_stok'] ?? 0 ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('categoryChart').getContext('2d');
    var categoryChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($labels) ?>,
            datasets: [
                {
                    label: 'Jumlah Produk',
                    data: <?= json_encode($jumlah_produk) ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Total Stok',
                    data: <?= json_encode($total_stok) ?>,
                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
