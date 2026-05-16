<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    die("Akses ditolak.");
}

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/header.php';

// Revenue bulan ini vs bulan lalu
$current_month = date('m');
$current_year = date('Y');
$last_month = date('m', strtotime('-1 month'));
$last_month_year = date('Y', strtotime('-1 month'));

$stmt_current = $pdo->prepare("SELECT SUM(total_harga) as rev, COUNT(id) as ord FROM orders WHERE status = 'delivered' AND strftime('%m', created_at) = ? AND strftime('%Y', created_at) = ?");
$stmt_current->execute([$current_month, $current_year]);
$curr_data = $stmt_current->fetch();
$rev_curr = $curr_data['rev'] ?? 0;
$ord_curr = $curr_data['ord'] ?? 0;

$stmt_last = $pdo->prepare("SELECT SUM(total_harga) as rev, COUNT(id) as ord FROM orders WHERE status = 'delivered' AND strftime('%m', created_at) = ? AND strftime('%Y', created_at) = ?");
$stmt_last->execute([$last_month, $last_month_year]);
$last_data = $stmt_last->fetch();
$rev_last = $last_data['rev'] ?? 0;

$growth = 0;
if ($rev_last > 0) {
    $growth = (($rev_curr - $rev_last) / $rev_last) * 100;
} elseif ($rev_curr > 0) {
    $growth = 100;
}

// Revenue 6 bulan terakhir (Line Chart)
$months = [];
$revenues = [];
for ($i = 5; $i >= 0; $i--) {
    $m = date('m', strtotime("-$i month"));
    $y = date('Y', strtotime("-$i month"));
    $months[] = date('M Y', strtotime("-$i month"));
    
    $stmt = $pdo->prepare("SELECT SUM(total_harga) as rev FROM orders WHERE status = 'delivered' AND strftime('%m', created_at) = ? AND strftime('%Y', created_at) = ?");
    $stmt->execute([$m, $y]);
    $revenues[] = $stmt->fetchColumn() ?? 0;
}

// Top 5 produk terlaris
$stmt_top = $pdo->query("
    SELECT p.nama_produk, SUM(oi.quantity) as total_terjual
    FROM order_items oi
    JOIN produk p ON oi.product_id = p.id
    JOIN orders o ON oi.order_id = o.id
    WHERE o.status != 'cancelled'
    GROUP BY p.id
    ORDER BY total_terjual DESC
    LIMIT 5
");
$top_products = $stmt_top->fetchAll();
$top_prod_labels = array_column($top_products, 'nama_produk');
$top_prod_data = array_column($top_products, 'total_terjual');

// Distribusi status order (Doughnut)
$stmt_status = $pdo->query("SELECT status, COUNT(*) as cnt FROM orders GROUP BY status");
$order_statuses = $stmt_status->fetchAll();
$status_labels = array_column($order_statuses, 'status');
$status_data = array_column($order_statuses, 'cnt');

// Top 10 customer
$stmt_top_cust = $pdo->query("
    SELECT c.nama, COUNT(o.id) as total_order, SUM(o.total_harga) as total_belanja
    FROM customers c
    LEFT JOIN orders o ON c.id = o.customer_id
    GROUP BY c.id
    ORDER BY total_belanja DESC
    LIMIT 10
");
$top_customers = $stmt_top_cust->fetchAll();

$top_product_name = $top_prod_labels[0] ?? 'N/A';
?>

<div class="d-flex">
    <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>

    <div class="flex-grow-1 p-4 bg-light">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold m-0">Dashboard Analytics</h2>
            <div>
                <a href="../api/export_analytics.php" class="btn btn-success me-2"><i class="fas fa-file-excel"></i> Export Excel</a>
                <button class="btn btn-primary" onclick="analyzeAI()"><i class="fas fa-robot"></i> Analisis AI</button>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h6 class="text-muted">Revenue Bulan Ini</h6>
                        <h3 class="fw-bold">Rp <?= number_format($rev_curr, 0, ',', '.') ?></h3>
                        <?php if($growth >= 0): ?>
                            <span class="text-success"><i class="fas fa-arrow-up"></i> <?= number_format($growth, 1) ?>%</span> dari bulan lalu
                        <?php else: ?>
                            <span class="text-danger"><i class="fas fa-arrow-down"></i> <?= number_format(abs($growth), 1) ?>%</span> dari bulan lalu
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h6 class="text-muted">Total Order Delivered Bulan Ini</h6>
                        <h3 class="fw-bold"><?= $ord_curr ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="fw-bold">Revenue 6 Bulan Terakhir</h5>
                        <canvas id="revChart" height="100"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="fw-bold">Distribusi Order</h5>
                        <canvas id="statusChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="fw-bold">Top 5 Produk Terlaris</h5>
                        <canvas id="topProdChart" height="150"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="fw-bold">Top 10 Customer</h5>
                        <table class="table table-sm">
                            <thead><tr><th>Nama</th><th>Orders</th><th>Total Belanja</th></tr></thead>
                            <tbody>
                                <?php foreach($top_customers as $c): ?>
                                <tr>
                                    <td><?= htmlspecialchars($c['nama']) ?></td>
                                    <td><?= $c['total_order'] ?></td>
                                    <td>Rp <?= number_format($c['total_belanja'], 0, ',', '.') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal AI Analyst -->
<div class="modal fade" id="aiModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-robot text-primary"></i> AI Sales Analyst</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="aiResult" class="p-3 bg-light rounded" style="white-space: pre-wrap;"></div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    new Chart(document.getElementById('revChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: <?= json_encode($months) ?>,
            datasets: [{
                label: 'Revenue (Rp)',
                data: <?= json_encode($revenues) ?>,
                borderColor: 'rgba(54, 162, 235, 1)',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                tension: 0.3,
                fill: true
            }]
        }
    });

    new Chart(document.getElementById('statusChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($status_labels) ?>,
            datasets: [{
                data: <?= json_encode($status_data) ?>,
                backgroundColor: ['#ffcd56', '#36a2eb', '#4bc0c0', '#ff6384', '#9966ff']
            }]
        }
    });

    new Chart(document.getElementById('topProdChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($top_prod_labels) ?>,
            datasets: [{
                label: 'Qty Terjual',
                data: <?= json_encode($top_prod_data) ?>,
                backgroundColor: 'rgba(75, 192, 192, 0.6)'
            }]
        }
    });
});

function analyzeAI() {
    var modal = new bootstrap.Modal(document.getElementById('aiModal'));
    modal.show();
    
    document.getElementById('aiResult').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sedang menganalisis data penjualan...';
    
    fetch('../api/sales_analyst.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            revenue: <?= json_encode($rev_curr) ?>,
            growth: <?= json_encode($growth) ?>,
            produk: <?= json_encode($top_product_name) ?>,
            n_order: <?= json_encode($ord_curr) ?>
        })
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            typeEffect(document.getElementById('aiResult'), data.message);
        } else {
            document.getElementById('aiResult').textContent = "Error: " + data.message;
        }
    })
    .catch(err => {
        document.getElementById('aiResult').textContent = "Connection error.";
    });
}

function typeEffect(element, text) {
    element.innerHTML = '';
    let i = 0;
    let timer = setInterval(function() {
        if (i < text.length) {
            element.innerHTML += text.charAt(i);
            i++;
        } else {
            clearInterval(timer);
        }
    }, 15);
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
