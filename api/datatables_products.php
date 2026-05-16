<?php
require_once __DIR__ . '/../includes/db.php';

$draw = $_POST['draw'] ?? 1;
$start = $_POST['start'] ?? 0;
$rowperpage = $_POST['length'] ?? 10; 
$searchValue = $_POST['search']['value'] ?? '';

// Search Query
$searchQuery = " ";
$searchArray = [];
if ($searchValue != '') {
   $searchQuery = " AND (nama_produk LIKE :name OR k.nama_kategori LIKE :name ) ";
   $searchArray = [':name' => "%$searchValue%"];
}

// Total records
$stmt = $pdo->query("SELECT COUNT(*) FROM produk WHERE deleted_at IS NULL");
$totalRecords = $stmt->fetchColumn();

// Total records with filter
$stmt = $pdo->prepare("SELECT COUNT(*) FROM produk p LEFT JOIN kategori k ON p.kategori_id = k.id WHERE p.deleted_at IS NULL" . $searchQuery);
$stmt->execute($searchArray);
$totalRecordwithFilter = $stmt->fetchColumn();

// Fetch records
$query = "SELECT p.*, k.nama_kategori FROM produk p LEFT JOIN kategori k ON p.kategori_id = k.id WHERE p.deleted_at IS NULL " . $searchQuery . " ORDER BY p.id DESC LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($query);

// Bind values
foreach ($searchArray as $key => $search) {
   $stmt->bindValue($key, $search, PDO::PARAM_STR);
}
$stmt->bindValue(':limit', (int)$rowperpage, PDO::PARAM_INT);
$stmt->bindValue(':offset', (int)$start, PDO::PARAM_INT);
$stmt->execute();
$records = $stmt->fetchAll();

$data = [];
foreach ($records as $row) {
    $action = "
        <button class='btn btn-sm btn-info text-white' onclick='editProduct(".$row['id'].")'><i class='fas fa-edit'></i></button>
        <button class='btn btn-sm btn-danger' onclick='deleteProduct(".$row['id'].")'><i class='fas fa-trash'></i></button>
    ";
    
    $foto = $row['foto'] ? "<img src='/storage/images/{$row['foto']}' width='50' class='rounded'>" : "No Image";
    $status = $row['is_active'] ? "<span class='badge bg-success'>Aktif</span>" : "<span class='badge bg-secondary'>Non-Aktif</span>";

    $data[] = [
        "foto" => $foto,
        "nama_produk" => htmlspecialchars($row['nama_produk']),
        "kategori" => htmlspecialchars($row['nama_kategori']),
        "harga" => "Rp " . number_format($row['harga'], 0, ',', '.'),
        "stok" => $row['stok'],
        "status" => $status,
        "action" => $action
    ];
}

$response = [
  "draw" => intval($draw),
  "iTotalRecords" => $totalRecords,
  "iTotalDisplayRecords" => $totalRecordwithFilter,
  "aaData" => $data
];

header('Content-Type: application/json');
echo json_encode($response);
