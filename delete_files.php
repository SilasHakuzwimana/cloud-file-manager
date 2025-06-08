<?php
session_start();
require './config/db.php';
require 'cloudinary_config.php';
use Cloudinary\Api\Admin\AdminApi;
use Cloudinary\Api\Exception\ApiError;

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['public_id'])) {
    $user_id = $_SESSION['user_id'];
    $id = $_POST['id'];

    // Check ownership
    $stmt = $conn->prepare("SELECT * FROM files WHERE id = ? AND user_id = ?");
    $stmt->bind_param("si", $id, $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows === 0) {
        echo json_encode(['success' => false, 'error' => 'File not found or not yours']);
        exit;
    }

    try {
        (new AdminApi())->deleteAssets([$id]);
        $del = $conn->prepare("DELETE FROM files WHERE public_id = ?");
        $del->bind_param("s", $id);
        $del->execute();

        echo json_encode(['success' => true]);
    } catch (ApiError $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?>