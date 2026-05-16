<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "<div class='container mt-5'><h3>Produk tidak ditemukan</h3></div>";
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$stmt = $pdo->prepare("SELECT p.*, k.nama_kategori FROM produk p LEFT JOIN kategori k ON p.kategori_id = k.id WHERE p.id = :id AND p.is_active = 1 AND p.deleted_at IS NULL");
$stmt->execute([':id' => $id]);
$produk = $stmt->fetch();

if (!$produk) {
    echo "<div class='container mt-5'><h3>Produk tidak ditemukan atau tidak aktif</h3></div>";
    require_once __DIR__ . '/includes/footer.php';
    exit;
}
?>

<div class="container my-5 flex-grow-1">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Home</a></li>
            <li class="breadcrumb-item"><a href="/products.php">Katalog</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($produk['nama_produk']) ?></li>
        </ol>
    </nav>

    <div class="row mt-4">
        <div class="col-md-5 mb-4">
            <?php $foto = $produk['foto'] ? '/storage/images/' . $produk['foto'] : 'https://via.placeholder.com/600x600?text=No+Image'; ?>
            <img src="<?= htmlspecialchars($foto) ?>" class="img-fluid rounded shadow-sm" alt="<?= htmlspecialchars($produk['nama_produk']) ?>">
        </div>
        <div class="col-md-7">
            <h1 class="fw-bold"><?= htmlspecialchars($produk['nama_produk']) ?></h1>
            <div class="mb-3">
                <span class="badge bg-secondary"><?= htmlspecialchars($produk['nama_kategori']) ?></span>
                <span class="badge bg-success">Stok: <?= $produk['stok'] ?></span>
            </div>
            <h2 class="text-primary fw-bold mb-4">Rp <?= number_format($produk['harga'], 0, ',', '.') ?></h2>
            
            <p class="lead"><?= nl2br(htmlspecialchars($produk['detail'])) ?></p>
            
            <hr class="my-4">
            
            <div class="d-flex align-items-center mb-4">
                <div class="input-group me-3" style="width: 150px;">
                    <button class="btn btn-outline-secondary" type="button" id="btn-minus" onclick="if(document.getElementById('qty').value > 1) document.getElementById('qty').value--">-</button>
                    <input type="number" id="qty" class="form-control text-center" value="1" min="1" max="<?= $produk['stok'] ?>">
                    <button class="btn btn-outline-secondary" type="button" id="btn-plus" onclick="if(document.getElementById('qty').value < <?= $produk['stok'] ?>) document.getElementById('qty').value++">+</button>
                </div>
                <button class="btn btn-primary btn-lg flex-grow-1 px-4 add-to-cart-btn-detail">
                    <i class="fas fa-shopping-cart me-2"></i> Tambah ke Keranjang
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btn = document.querySelector('.add-to-cart-btn-detail');
    if (btn) {
        btn.addEventListener('click', function() {
            const id = '<?= $produk['id'] ?>';
            const name = '<?= addslashes(htmlspecialchars($produk['nama_produk'])) ?>';
            const price = <?= $produk['harga'] ?>;
            const qty = parseInt(document.getElementById('qty').value);
            
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            
            const existingItemIndex = cart.findIndex(item => item.id === id);
            if (existingItemIndex > -1) {
                cart[existingItemIndex].qty += qty;
            } else {
                cart.push({
                    id: id,
                    name: name,
                    price: price,
                    qty: qty
                });
            }
            
            localStorage.setItem('cart', JSON.stringify(cart));
            if(typeof updateCartCounter === 'function') updateCartCounter();
            
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: `${qty} ${name} ditambahkan ke keranjang!`,
                timer: 1500,
                showConfirmButton: false
            });
        });
    }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
