<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "filemanager_db";
$port = 3308;

$conn = new mysqli($host, $user, $pass, $dbname, $port);
if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error);
}
?>
