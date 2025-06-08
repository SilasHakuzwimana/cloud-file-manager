<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start(); // Start session to access user_id
header('Content-Type: application/json');
date_default_timezone_set('Africa/Kigali');

require './config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'error' => 'Unauthorized access']);
        exit;
    }

    if (!isset($_POST['id'], $_POST['original_name'], $_POST['format'])) {
        echo json_encode(['success' => false, 'error' => 'Missing required fields']);
        exit;
    }

    $id = (int)$_POST['id'];
    $user_id = $_SESSION['user_id'];
    $original_name = trim($_POST['original_name']);
    $format = trim($_POST['format']);

    // Ensure the file belongs to the logged-in user
    $check = $conn->prepare("SELECT id FROM files WHERE id = ? AND user_id = ?");
    $check->bind_param("ii", $id, $user_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'error' => 'File not found or permission denied']);
        exit;
    }

    $stmt = $conn->prepare("UPDATE files SET original_name = ?, format = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ssii", $original_name, $format, $id, $user_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'File updated successfully']);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }

    $stmt->close();
    $check->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
