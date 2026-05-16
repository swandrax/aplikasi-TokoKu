<?php
require_once __DIR__ . '/../includes/db.php';
header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

try {
    if ($action === 'create') {
        $nama_kategori = $_POST['nama_kategori'] ?? '';
        if (empty($nama_kategori)) throw new Exception('Nama kategori kosong');
        
        $stmt = $pdo->prepare("INSERT INTO kategori (nama_kategori, is_active, deleted_at) VALUES (?, 1, NULL)");
        $stmt->execute([$nama_kategori]);
        
        echo json_encode(['success' => true, 'message' => 'Kategori berhasil ditambahkan']);
        
    } elseif ($action === 'update') {
        $id = $_POST['id'] ?? '';
        $nama_kategori = $_POST['nama_kategori'] ?? '';
        if (empty($id) || empty($nama_kategori)) throw new Exception('Data tidak lengkap');
        
        $stmt = $pdo->prepare("UPDATE kategori SET nama_kategori = ? WHERE id = ?");
        $stmt->execute([$nama_kategori, $id]);
        
        echo json_encode(['success' => true, 'message' => 'Kategori berhasil diupdate']);
        
    } elseif ($action === 'delete') {
        $id = $_POST['id'] ?? '';
        if (empty($id)) throw new Exception('ID kosong');
        
        $stmt = $pdo->prepare("UPDATE kategori SET deleted_at = datetime('now'), is_active = 0 WHERE id = ?");
        $stmt->execute([$id]);
        
        echo json_encode(['success' => true, 'message' => 'Kategori berhasil dihapus (soft delete)']);
        
    } else {
        throw new Exception('Aksi tidak dikenal');
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
