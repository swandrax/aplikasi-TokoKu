<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

// Get featured products (for simplicity, we get 4 active products)
$stmt = $pdo->query("SELECT * FROM produk WHERE is_active = 1 AND deleted_at IS NULL ORDER BY id DESC LIMIT 4");
$featuredProducts = $stmt->fetchAll();
?>

<div class="hero-section text-center">
    <div class="container">
        <h1 class="display-4 fw-bold mb-4">Selamat Datang di Zavora Store</h1>
        <p class="lead mb-4">Temukan produk terbaik dengan harga terjangkau.</p>
        <a href="/products.php" class="btn btn-accent btn-lg px-5 rounded-pill shadow-sm">Mulai Belanja</a>
    </div>
</div>

<div class="container mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Produk Unggulan</h2>
        <a href="/products.php" class="text-decoration-none text-accent">Lihat Semua <i class="fas fa-arrow-right"></i></a>
    </div>
    
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
        <?php foreach($featuredProducts as $produk): ?>
        <div class="col">
            <div class="card h-100 product-card">
                <?php $foto = $produk['foto'] ? '/storage/images/' . $produk['foto'] : 'https://via.placeholder.com/300x200?text=No+Image'; ?>
                <img src="<?= htmlspecialchars($foto) ?>" class="card-img-top" alt="<?= htmlspecialchars($produk['nama_produk']) ?>">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title"><?= htmlspecialchars($produk['nama_produk']) ?></h5>
                    <p class="card-text text-primary fw-bold fs-5 mb-3">Rp <?= number_format($produk['harga'], 0, ',', '.') ?></p>
                    <div class="mt-auto">
                        <a href="/product_detail.php?id=<?= $produk['id'] ?>" class="btn btn-outline-primary w-100 mb-2">Detail</a>
                        <button class="btn btn-primary w-100 add-to-cart-btn" 
                            data-id="<?= $produk['id'] ?>" 
                            data-name="<?= htmlspecialchars($produk['nama_produk']) ?>" 
                            data-price="<?= $produk['harga'] ?>">
                            <i class="fas fa-shopping-cart"></i> Tambah
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
