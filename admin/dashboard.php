<?php
session_start();
// Dummy simulation for admin auth if session isn't set for test
if (!isset($_SESSION['user_role'])) {
    $_SESSION['user_role'] = 'admin';
    $_SESSION['user_name'] = 'Admin Ganteng';
}

if ($_SESSION['user_role'] !== 'admin') {
    die("Akses ditolak.");
}

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/header.php';

// Fetch statistics
$totalProduk = $pdo->query("SELECT COUNT(*) FROM produk WHERE is_active = 1 AND deleted_at IS NULL")->fetchColumn();
$totalCustomer = $pdo->query("SELECT COUNT(*) FROM customers WHERE is_active = 1 AND deleted_at IS NULL")->fetchColumn();
$totalOrder = $pdo->query("SELECT COUNT(*) FROM orders WHERE deleted_at IS NULL")->fetchColumn();
$revenueHariIni = $pdo->query("SELECT SUM(total_harga) FROM orders WHERE date(created_at) = date('now') AND deleted_at IS NULL")->fetchColumn() ?: 0;
?>

<div class="d-flex">
    <!-- Sidebar -->
    <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="flex-grow-1 p-4 bg-light">
        <!-- AI Greeting Widget -->
        <div id="ai-greeting-widget" class="alert alert-primary border-0 shadow-sm mb-4 d-flex align-items-center">
            <div class="spinner-border spinner-border-sm me-3 text-primary" role="status"></div>
            <span id="greeting-text">Loading AI Greeting...</span>
        </div>

        <h2 class="fw-bold mb-4">Dashboard</h2>

        <div class="row g-4">
            <!-- Total Produk -->
            <div class="col-md-3">
                <div class="card border-0 shadow-sm bg-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted text-uppercase mb-1">Total Produk</h6>
                                <h3 class="fw-bold mb-0"><?= number_format($totalProduk) ?></h3>
                            </div>
                            <div class="bg-primary bg-opacity-10 p-3 rounded">
                                <i class="fas fa-box fa-2x text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Order -->
            <div class="col-md-3">
                <div class="card border-0 shadow-sm bg-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted text-uppercase mb-1">Total Order</h6>
                                <h3 class="fw-bold mb-0"><?= number_format($totalOrder) ?></h3>
                            </div>
                            <div class="bg-success bg-opacity-10 p-3 rounded">
                                <i class="fas fa-shopping-cart fa-2x text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Customer -->
            <div class="col-md-3">
                <div class="card border-0 shadow-sm bg-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted text-uppercase mb-1">Total Customer</h6>
                                <h3 class="fw-bold mb-0"><?= number_format($totalCustomer) ?></h3>
                            </div>
                            <div class="bg-warning bg-opacity-10 p-3 rounded">
                                <i class="fas fa-users fa-2x text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Revenue Hari Ini -->
            <div class="col-md-3">
                <div class="card border-0 shadow-sm bg-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted text-uppercase mb-1">Revenue Hari Ini</h6>
                                <h3 class="fw-bold mb-0">Rp <?= number_format($revenueHariIni, 0, ',', '.') ?></h3>
                            </div>
                            <div class="bg-info bg-opacity-10 p-3 rounded">
                                <i class="fas fa-money-bill-wave fa-2x text-info"></i>
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
    const adminName = '<?= htmlspecialchars($_SESSION['user_name']) ?>';
    const orderCount = <?= $totalOrder ?>;
    
    const cacheKey = 'ai_greeting_cache';
    const cacheExpiryKey = 'ai_greeting_expiry';
    const now = new Date().getTime();
    
    let cachedGreeting = localStorage.getItem(cacheKey);
    let cachedExpiry = localStorage.getItem(cacheExpiryKey);
    
    if (cachedGreeting && cachedExpiry && now < parseInt(cachedExpiry)) {
        document.getElementById('greeting-text').innerHTML = `<strong>AI Says:</strong> ${cachedGreeting}`;
        document.querySelector('.spinner-border').classList.add('d-none');
    } else {
        fetch('/api/greeting.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ nama: adminName, n: orderCount })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const text = data.greeting;
                document.getElementById('greeting-text').innerHTML = `<strong>AI Says:</strong> ${text}`;
                document.querySelector('.spinner-border').classList.add('d-none');
                
                // Cache for 1 hour (3600000 ms)
                localStorage.setItem(cacheKey, text);
                localStorage.setItem(cacheExpiryKey, now + 3600000);
            } else {
                document.getElementById('greeting-text').textContent = 'Selamat datang, ' + adminName + '!';
                document.querySelector('.spinner-border').classList.add('d-none');
            }
        })
        .catch(err => {
            console.error(err);
            document.getElementById('greeting-text').textContent = 'Selamat datang, ' + adminName + '!';
            document.querySelector('.spinner-border').classList.add('d-none');
        });
    }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
