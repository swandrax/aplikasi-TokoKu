<div class="bg-dark text-white p-3 vh-100 sticky-top" style="width: 250px;">
    <h5 class="mb-4 text-center fw-bold text-uppercase border-bottom pb-3">Admin Panel</h5>
    <ul class="nav flex-column gap-2">
        <li class="nav-item">
            <a href="/admin/dashboard.php" class="nav-link text-white <?= strpos($_SERVER['REQUEST_URI'], 'dashboard') !== false ? 'bg-primary rounded' : '' ?>">
                <i class="fas fa-home me-2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a href="/admin/orders/index.php" class="nav-link text-white <?= strpos($_SERVER['REQUEST_URI'], 'orders') !== false ? 'bg-primary rounded' : '' ?>">
                <i class="fas fa-shopping-bag me-2"></i> Orders
            </a>
        </li>
        <li class="nav-item">
            <a href="/admin/customers/index.php" class="nav-link text-white <?= strpos($_SERVER['REQUEST_URI'], 'customers') !== false ? 'bg-primary rounded' : '' ?>">
                <i class="fas fa-users me-2"></i> Customers
            </a>
        </li>
        <li class="nav-item">
            <a href="/admin/suppliers/index.php" class="nav-link text-white <?= strpos($_SERVER['REQUEST_URI'], 'suppliers') !== false ? 'bg-primary rounded' : '' ?>">
                <i class="fas fa-truck me-2"></i> Suppliers
            </a>
        </li>
        <li class="nav-item mt-3">
            <h6 class="text-muted text-uppercase text-xs fw-bold px-3">Data Master</h6>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link text-white" onclick="alert('Master Produk page to be implemented')">
                <i class="fas fa-box me-2"></i> Produk
            </a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link text-white" onclick="alert('Master Kategori page to be implemented')">
                <i class="fas fa-tags me-2"></i> Kategori
            </a>
        </li>
        <li class="nav-item mt-3">
            <h6 class="text-muted text-uppercase text-xs fw-bold px-3">Tools</h6>
        </li>
        <li class="nav-item">
            <a href="/admin/import.php" class="nav-link text-white <?= strpos($_SERVER['REQUEST_URI'], 'import') !== false ? 'bg-primary rounded' : '' ?>">
                <i class="fas fa-file-import me-2"></i> Import Data
            </a>
        </li>
    </ul>
</div>
