<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

// Fetch categories for filter
$categoriesStmt = $pdo->query("SELECT * FROM kategori WHERE deleted_at IS NULL");
$categories = $categoriesStmt->fetchAll();

$kategori_id = $_GET['kategori_id'] ?? '';
$search = $_GET['search'] ?? '';

$query = "SELECT * FROM produk WHERE is_active = 1 AND deleted_at IS NULL";
$params = [];

if ($kategori_id) {
    $query .= " AND kategori_id = :kategori_id";
    $params[':kategori_id'] = $kategori_id;
}

if ($search) {
    $query .= " AND nama_produk LIKE :search";
    $params[':search'] = "%$search%";
}

$query .= " ORDER BY id DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();
?>

<div class="container my-5 flex-grow-1">
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h2 class="fw-bold">Katalog Produk</h2>
        </div>
    </div>
    
    <div class="row">
        <!-- Sidebar Filters -->
        <div class="col-lg-3 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title fw-bold mb-3">AI Smart Search</h5>
                    <form id="ai-search-form" class="mb-4">
                        <div class="input-group">
                            <input type="text" id="ai-query" class="form-control" placeholder="Cari baju merah murah..." required>
                            <button class="btn btn-accent" type="submit" id="btn-ai-search">
                                <i class="fas fa-magic"></i>
                            </button>
                        </div>
                        <small class="text-muted mt-2 d-block">Gunakan bahasa natural untuk pencarian pintar.</small>
                    </form>

                    <h5 class="card-title fw-bold mb-3">Filter Kategori</h5>
                    <form action="/products.php" method="GET">
                        <?php if($search): ?>
                            <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                        <?php endif; ?>
                        <div class="mb-3">
                            <select name="kategori_id" class="form-select select2" onchange="this.form.submit()">
                                <option value="">Semua Kategori</option>
                                <?php foreach($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= $kategori_id == $cat['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['nama_kategori']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Product Grid -->
        <div class="col-lg-9">
            <div id="ai-search-result" class="alert alert-info d-none"></div>
            
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4" id="product-list">
                <?php if(count($products) > 0): ?>
                    <?php foreach($products as $produk): ?>
                    <div class="col product-item">
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
                <?php else: ?>
                    <div class="col-12 text-center py-5">
                        <h4 class="text-muted">Tidak ada produk ditemukan.</h4>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const aiForm = document.getElementById('ai-search-form');
    if (aiForm) {
        aiForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const query = document.getElementById('ai-query').value;
            const btn = document.getElementById('btn-ai-search');
            const resultBox = document.getElementById('ai-search-result');
            
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            btn.disabled = true;
            
            fetch('/api/smart_search.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ query: query })
            })
            .then(response => response.json())
            .then(data => {
                btn.innerHTML = '<i class="fas fa-magic"></i>';
                btn.disabled = false;
                
                if (data.success) {
                    resultBox.classList.remove('d-none');
                    resultBox.innerHTML = `<strong>Pencarian AI:</strong> Kata kunci: ${data.data.keywords.join(', ')}. ` +
                        (data.data.category ? `Kategori: ${data.data.category}. ` : '') +
                        (data.data.max_price ? `Harga maks: Rp ${data.data.max_price}.` : '');
                    
                    // Simple client-side filtering based on AI keywords (in real world, this might query the DB again)
                    const items = document.querySelectorAll('.product-item');
                    items.forEach(item => {
                        const title = item.querySelector('.card-title').textContent.toLowerCase();
                        const priceText = item.querySelector('.card-text').textContent.replace(/\D/g, '');
                        const price = parseInt(priceText);
                        
                        let matchKeyword = data.data.keywords.length === 0 || data.data.keywords.some(kw => title.includes(kw.toLowerCase()));
                        let matchPrice = !data.data.max_price || price <= data.data.max_price;
                        
                        if (matchKeyword && matchPrice) {
                            item.style.display = 'block';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                } else {
                    alert('Gagal memproses AI search.');
                }
            })
            .catch(error => {
                console.error(error);
                btn.innerHTML = '<i class="fas fa-magic"></i>';
                btn.disabled = false;
            });
        });
    }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
