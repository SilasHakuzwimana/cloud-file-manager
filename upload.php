<?php
// Enable error reporting for debugging
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

require __DIR__ . '/vendor/autoload.php';
require 'cloudinary_config.php';
require './config/db.php';

use Cloudinary\Api\Upload\UploadApi;

date_default_timezone_set('Africa/Kigali');



if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $responses = [];

    // Loop through each uploaded file
    foreach ($_FILES['file']['tmp_name'] as $index => $tmpFile) {
        $originalName = $_FILES['file']['name'][$index];

        try {
            $upload = (new UploadApi())->upload($tmpFile);

            if (isset($upload['public_id'])) {
                $originalNameEscaped = $conn->real_escape_string($originalName);
                $publicId           = $conn->real_escape_string($upload['public_id']);
                $url                = $conn->real_escape_string($upload['secure_url']);
                $format             = $conn->real_escape_string($upload['format']);
                $bytes              = (int)$upload['bytes'];
                $createdAt          = date('Y-m-d H:i:s', strtotime($upload['created_at']));

                $userId = $conn->real_escape_string($_SESSION['user_id']);
                $sql = "INSERT INTO files (user_id, original_name, public_id, file_url, format, bytes, created_at)
                        VALUES ('$userId', '$originalNameEscaped', '$publicId', '$url', '$format', $bytes, '$createdAt')";

                if ($conn->query($sql)) {
                    $responses[] = [
                        'success' => true,
                        'file_url' => $url,
                        'original_name' => $originalName
                    ];
                } else {
                    $responses[] = [
                        'success' => false,
                        'error' => 'DB error: ' . $conn->error,
                        'original_name' => $originalName
                    ];
                }
            } else {
                $responses[] = [
                    'success' => false,
                    'error' => 'Upload failed',
                    'original_name' => $originalName
                ];
            }
        } catch (Exception $e) {
            $responses[] = [
                'success' => false,
                'error' => 'Exception: ' . $e->getMessage(),
                'original_name' => $originalName
            ];
        }
    }

    echo json_encode($responses);

} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}
