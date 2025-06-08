<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start(); // Required to access $_SESSION
header('Content-Type: application/json');

require './config/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'error'   => 'User not logged in'
    ]);
    exit;
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM files WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $conn->error
    ]);
    exit;
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$files = [];
while ($row = $result->fetch_assoc()) {
    $files[] = [
        'id'            => $row['id'],
        'original_name' => $row['original_name'],
        'public_id'     => $row['public_id'],
        'file_url'      => $row['file_url'],
        'format'        => $row['format'],
        'bytes'         => $row['bytes'],
        'created_at'    => $row['created_at']
    ];
}

echo json_encode(['success' => true, 'files' => $files]);
