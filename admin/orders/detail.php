<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    die("Akses ditolak.");
}

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

$id = $_GET['id'] ?? null;
if (!$id) die("ID Order tidak valid");

// Update status if POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $new_status = $_POST['status'];
    $stmt = $pdo->prepare("UPDATE orders SET status = ?, updated_at = datetime('now') WHERE id = ?");
    $stmt->execute([$new_status, $id]);
    
    // Will handle AI notification via AJAX
    $status_updated = true;
}

// Fetch Order & Customer
$stmt = $pdo->prepare("
    SELECT o.*, c.nama as customer_name, c.telepon, c.kota, c.alamat
    FROM orders o
    INNER JOIN customers c ON o.customer_id = c.id
    WHERE o.id = ?
");
$stmt->execute([$id]);
$order = $stmt->fetch();

if (!$order) die("Order tidak ditemukan");

// Fetch Order Items
$stmt = $pdo->prepare("
    SELECT oi.*, p.nama_produk, p.harga as harga_asli
    FROM order_items oi
    INNER JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ?
");
$stmt->execute([$id]);
$items = $stmt->fetchAll();

$statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
?>

<div class="d-flex">
    <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>

    <div class="flex-grow-1 p-4 bg-light">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold m-0">Detail Order #<?= htmlspecialchars($order['id']) ?></h2>
            <a href="index.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
        </div>

        <?php if(isset($status_updated)): ?>
            <div class="alert alert-success">Status berhasil diupdate!</div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-8">
                <!-- Data Items -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white fw-bold">Item Pesanan</div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>Harga Satuan</th>
                                    <th>Qty</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($items as $item): ?>
                                <tr>
                                    <td><?= htmlspecialchars($item['nama_produk']) ?></td>
                                    <td>Rp <?= number_format($item['harga_satuan'], 0, ',', '.') ?></td>
                                    <td><?= htmlspecialchars($item['quantity']) ?></td>
                                    <td class="text-end">Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-end">Total Item:</th>
                                    <th class="text-end">Rp <?= number_format($order['total_harga'], 0, ',', '.') ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Info Customer & Status -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white fw-bold">Informasi Customer</div>
                    <div class="card-body">
                        <p class="mb-1"><strong>Nama:</strong> <?= htmlspecialchars($order['customer_name']) ?></p>
                        <p class="mb-1"><strong>Telepon:</strong> <?= htmlspecialchars($order['telepon']) ?></p>
                        <p class="mb-1"><strong>Kota:</strong> <?= htmlspecialchars($order['kota']) ?></p>
                        <p class="mb-1"><strong>Alamat:</strong> <?= nl2br(htmlspecialchars($order['alamat'])) ?></p>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white fw-bold">Informasi Pesanan</div>
                    <div class="card-body">
                        <p class="mb-1"><strong>Tanggal:</strong> <?= date('d M Y H:i', strtotime($order['created_at'])) ?></p>
                        <p class="mb-1"><strong>Pengiriman:</strong> <?= htmlspecialchars($order['pengiriman']) ?></p>
                        <p class="mb-1"><strong>Pembayaran:</strong> <?= htmlspecialchars($order['metode_pembayaran']) ?></p>
                        <hr>
                        <form method="POST" id="statusForm">
                            <input type="hidden" name="update_status" value="1">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Status Order</label>
                                <select name="status" class="form-select" id="orderStatus">
                                    <?php foreach($statuses as $st): ?>
                                        <option value="<?= $st ?>" <?= $order['status'] === $st ? 'selected' : '' ?>>
                                            <?= ucfirst($st) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 mb-2">Update Status</button>
                            <button type="button" class="btn btn-outline-success w-100" onclick="generateNotification()">
                                <i class="fab fa-whatsapp"></i> Generate WA Notif AI
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Notifikasi -->
<div class="modal fade" id="notifModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Generate Pesan WA</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="notifResult" class="p-3 bg-light rounded" style="white-space: pre-wrap;">Generating...</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-success" onclick="copyNotif()">Copy ke Clipboard</button>
            </div>
        </div>
    </div>
</div>

<script>
function generateNotification() {
    const status = document.getElementById('orderStatus').value;
    const customer = <?= json_encode($order['customer_name']) ?>;
    const orderId = <?= json_encode($order['id']) ?>;
    
    var modal = new bootstrap.Modal(document.getElementById('notifModal'));
    modal.show();
    
    document.getElementById('notifResult').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating message with AI...';
    
    fetch('../../api/order_notification.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            nama: customer,
            no_order: orderId,
            status: status
        })
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            document.getElementById('notifResult').textContent = data.message;
        } else {
            document.getElementById('notifResult').textContent = "Error: " + data.message;
        }
    })
    .catch(err => {
        document.getElementById('notifResult').textContent = "Connection error.";
    });
}

function copyNotif() {
    const text = document.getElementById('notifResult').textContent;
    navigator.clipboard.writeText(text).then(() => {
        alert("Copied to clipboard!");
    });
}
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
