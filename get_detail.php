<?php
header('Content-Type: application/json');
include 'config/database.php';

$type = $_GET['type'] ?? '';
$id = (int)($_GET['id'] ?? 0);

if (!in_array($type, ['flora', 'fauna']) || $id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Parameter tidak valid']);
    exit;
}

$table = $type === 'flora' ? 'flora' : 'fauna';
$query = "SELECT * FROM $table WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    echo json_encode([
        'success' => true,
        'data' => $row
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Data tidak ditemukan'
    ]);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>