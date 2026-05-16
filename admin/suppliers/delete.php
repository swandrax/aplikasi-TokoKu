<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    die("Akses ditolak.");
}

require_once __DIR__ . '/../../includes/db.php';

$id = $_GET['id'] ?? null;
if ($id) {
    // Soft delete
    $stmt = $pdo->prepare("UPDATE suppliers SET deleted_at = datetime('now'), is_active = 0 WHERE id = ?");
    $stmt->execute([$id]);
}

header("Location: index.php?msg=deleted");
exit;
