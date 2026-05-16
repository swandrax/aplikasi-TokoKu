<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    die("Akses ditolak.");
}

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    die("ID tidak valid");
}

$stmt = $pdo->prepare("SELECT * FROM customers WHERE id = ? AND deleted_at IS NULL");
$stmt->execute([$id]);
$customer = $stmt->fetch();

if (!$customer) {
    die("Customer tidak ditemukan");
}

// Get order history
$orderStmt = $pdo->prepare("SELECT COUNT(*) as total_orders, SUM(total_harga) as total_spent FROM orders WHERE customer_id = ? AND deleted_at IS NULL");
$orderStmt->execute([$id]);
$orderStats = $orderStmt->fetch();

$totalOrders = $orderStats['total_orders'] ?: 0;
$totalSpent = $orderStats['total_spent'] ?: 0;

// Favorite product (simplified logic: product bought most times by this customer)
$favStmt = $pdo->prepare("
    SELECT p.nama_produk, SUM(oi.jumlah) as total_qty 
    FROM order_items oi 
    JOIN orders o ON oi.order_id = o.id 
    JOIN produk p ON oi.produk_id = p.id 
    WHERE o.customer_id = ? 
    GROUP BY oi.produk_id 
    ORDER BY total_qty DESC LIMIT 1
");
$favStmt->execute([$id]);
$favProduct = $favStmt->fetch();
$favoriteProduct = $favProduct ? $favProduct['nama_produk'] : 'Belum ada';

?>

<div class="d-flex">
    <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>

    <div class="flex-grow-1 p-4 bg-light">
        <h2 class="fw-bold mb-4">Detail Customer</h2>

        <div class="row">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h4 class="card-title fw-bold border-bottom pb-2 mb-3">Informasi Profil</h4>
                        <table class="table table-borderless">
                            <tr><th width="150">Nama</th><td>: <?= htmlspecialchars($customer['nama']) ?></td></tr>
                            <tr><th>Email</th><td>: <?= htmlspecialchars($customer['email']) ?></td></tr>
                            <tr><th>Telepon</th><td>: <?= htmlspecialchars($customer['telepon'] ?? '-') ?></td></tr>
                            <tr><th>Alamat</th><td>: <?= htmlspecialchars($customer['alamat'] ?? '-') ?></td></tr>
                            <tr><th>Kota</th><td>: <?= htmlspecialchars($customer['kota'] ?? '-') ?></td></tr>
                            <tr><th>Bergabung Sejak</th><td>: <?= date('d M Y', strtotime($customer['created_at'])) ?></td></tr>
                            <tr><th>Status</th><td>: <?= $customer['is_active'] ? 'Aktif' : 'Non-Aktif' ?></td></tr>
                        </table>
                        <a href="index.php" class="btn btn-secondary mt-3">Kembali</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm mb-4 bg-primary text-white">
                    <div class="card-body">
                        <h5 class="fw-bold"><i class="fas fa-shopping-bag me-2"></i> Ringkasan Belanja</h5>
                        <hr>
                        <p class="mb-1">Total Order: <strong><?= $totalOrders ?></strong></p>
                        <p class="mb-1">Total Belanja: <strong>Rp <?= number_format($totalSpent, 0, ',', '.') ?></strong></p>
                        <p class="mb-0">Produk Favorit: <strong><?= htmlspecialchars($favoriteProduct) ?></strong></p>
                    </div>
                </div>

                <!-- AI Customer Insight -->
                <div class="card border-0 shadow-sm border-start border-4 border-info">
                    <div class="card-body">
                        <h5 class="fw-bold text-info"><i class="fas fa-brain me-2"></i> AI Insight</h5>
                        <div id="ai-insight-content" class="mt-3">
                            <div class="text-center">
                                <div class="spinner-border text-info spinner-border-sm" role="status"></div>
                                <small class="text-muted ms-2">Menganalisis profil...</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const payload = {
        nama: '<?= addslashes(htmlspecialchars($customer['nama'])) ?>',
        n: <?= $totalOrders ?>,
        total: <?= $totalSpent ?>,
        produk: '<?= addslashes(htmlspecialchars($favoriteProduct)) ?>'
    };

    fetch('/api/customer_insight.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // parse bullet points to HTML
            const html = data.insight.replace(/\n/g, '<br>');
            document.getElementById('ai-insight-content').innerHTML = `<div class="small">${html}</div>`;
        } else {
            document.getElementById('ai-insight-content').innerHTML = '<span class="text-danger">Gagal load insight.</span>';
        }
    })
    .catch(err => {
        document.getElementById('ai-insight-content').innerHTML = '<span class="text-danger">Error load insight.</span>';
    });
});
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
