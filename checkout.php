<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

// Fetch cities for shipping
$citiesStmt = $pdo->query("SELECT * FROM cities WHERE is_active = 1 AND deleted_at IS NULL");
$cities = $citiesStmt->fetchAll();

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'] ?? '';
    $telepon = $_POST['telepon'] ?? '';
    $alamat = $_POST['alamat'] ?? '';
    $city_id = $_POST['city_id'] ?? null;
    $metode_pembayaran = $_POST['metode_pembayaran'] ?? '';
    $cartData = json_decode($_POST['cart_data'] ?? '[]', true);
    $total = $_POST['total_harga'] ?? 0;

    if (empty($cartData)) {
        $message = "<div class='alert alert-danger'>Keranjang kosong!</div>";
    } else {
        try {
            $pdo->beginTransaction();
            
            // For guest, customer_id is null. Or find by email if needed.
            // Insert order
            $stmt = $pdo->prepare("INSERT INTO orders (nama_penerima, telepon_penerima, alamat_pengiriman, city_id, metode_pembayaran, total_harga, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, datetime('now'), datetime('now'))");
            $stmt->execute([$nama, $telepon, $alamat, $city_id, $metode_pembayaran, $total]);
            $orderId = $pdo->lastInsertId();
            
            // Insert items
            $itemStmt = $pdo->prepare("INSERT INTO order_items (order_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (?, ?, ?, ?, ?, datetime('now'), datetime('now'))");
            foreach ($cartData as $item) {
                $subtotal = $item['qty'] * $item['price'];
                $itemStmt->execute([$orderId, $item['id'], $item['qty'], $item['price'], $subtotal]);
            }
            
            $pdo->commit();
            $message = "<div class='alert alert-success'>Pesanan berhasil dibuat! Nomor Order: #$orderId</div>";
            echo "<script>localStorage.removeItem('cart');</script>";
        } catch (Exception $e) {
            $pdo->rollBack();
            $message = "<div class='alert alert-danger'>Terjadi kesalahan: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }
}
?>

<div class="container my-5 flex-grow-1">
    <h2 class="fw-bold mb-4">Checkout</h2>
    
    <?= $message ?>
    
    <?php if(strpos($message, 'berhasil') === false): ?>
    <div class="row">
        <!-- Cart Items -->
        <div class="col-md-5 order-md-2 mb-4">
            <h4 class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-primary">Keranjang Anda</span>
                <span class="badge bg-primary rounded-pill" id="checkout-count">0</span>
            </h4>
            <ul class="list-group mb-3" id="checkout-items">
                <!-- Injected via JS -->
            </ul>
            <div class="card p-2 d-none" id="empty-cart-msg">
                <div class="text-center text-muted py-3">Keranjang kosong. <a href="/products.php">Belanja sekarang.</a></div>
            </div>
        </div>
        
        <!-- Form -->
        <div class="col-md-7 order-md-1">
            <h4 class="mb-3">Alamat Pengiriman</h4>
            <form action="/checkout.php" method="POST" id="checkout-form">
                <input type="hidden" name="cart_data" id="cart_data_input">
                <input type="hidden" name="total_harga" id="total_harga_input">
                
                <div class="row g-3">
                    <div class="col-sm-6">
                        <label class="form-label">Nama Penerima</label>
                        <input type="text" class="form-control" name="nama" required>
                    </div>

                    <div class="col-sm-6">
                        <label class="form-label">Nomor Telepon</label>
                        <input type="text" class="form-control" name="telepon" required>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Alamat Lengkap</label>
                        <textarea class="form-control" name="alamat" rows="3" required></textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Kota</label>
                        <select class="form-select select2" name="city_id" required>
                            <option value="">Pilih...</option>
                            <?php foreach($cities as $city): ?>
                                <option value="<?= $city['id'] ?>"><?= htmlspecialchars($city['nama_kota']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Pengiriman (Opsional)</label>
                        <input type="text" class="form-control datepicker" name="tanggal_pengiriman" placeholder="YYYY-MM-DD">
                    </div>

                    <div class="col-12 mt-4">
                        <h4 class="mb-3">Pembayaran</h4>
                        <div class="my-3">
                            <div class="form-check">
                                <input id="credit" name="metode_pembayaran" type="radio" class="form-check-input" value="Transfer Bank" checked required>
                                <label class="form-check-label" for="credit">Transfer Bank</label>
                            </div>
                            <div class="form-check">
                                <input id="debit" name="metode_pembayaran" type="radio" class="form-check-input" value="E-Wallet" required>
                                <label class="form-check-label" for="debit">E-Wallet (OVO/GoPay/Dana)</label>
                            </div>
                            <div class="form-check">
                                <input id="paypal" name="metode_pembayaran" type="radio" class="form-check-input" value="COD" required>
                                <label class="form-check-label" for="paypal">Cash on Delivery (COD)</label>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <button class="w-100 btn btn-primary btn-lg" type="submit" id="btn-submit-order">Buat Pesanan</button>
            </form>
        </div>
    </div>
    <?php else: ?>
        <a href="/products.php" class="btn btn-primary mt-3">Kembali Belanja</a>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const container = document.getElementById('checkout-items');
    const emptyMsg = document.getElementById('empty-cart-msg');
    const form = document.getElementById('checkout-form');
    let total = 0;
    
    if (cart.length === 0) {
        if(container) container.classList.add('d-none');
        if(emptyMsg) emptyMsg.classList.remove('d-none');
        if(form) document.getElementById('btn-submit-order').disabled = true;
    } else {
        if(form) document.getElementById('btn-submit-order').disabled = false;
        let html = '';
        cart.forEach(item => {
            const subtotal = item.price * item.qty;
            total += subtotal;
            html += `
                <li class="list-group-item d-flex justify-content-between lh-sm">
                    <div>
                        <h6 class="my-0">${item.name}</h6>
                        <small class="text-muted">Jumlah: ${item.qty} x Rp ${new Intl.NumberFormat('id-ID').format(item.price)}</small>
                    </div>
                    <span class="text-muted">Rp ${new Intl.NumberFormat('id-ID').format(subtotal)}</span>
                </li>
            `;
        });
        
        html += `
            <li class="list-group-item d-flex justify-content-between bg-light">
                <span>Total (IDR)</span>
                <strong>Rp ${new Intl.NumberFormat('id-ID').format(total)}</strong>
            </li>
        `;
        
        if(container) {
            container.innerHTML = html;
            document.getElementById('checkout-count').textContent = cart.reduce((sum, item) => sum + item.qty, 0);
        }
        
        if(form) {
            document.getElementById('cart_data_input').value = JSON.stringify(cart);
            document.getElementById('total_harga_input').value = total;
        }
    }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
