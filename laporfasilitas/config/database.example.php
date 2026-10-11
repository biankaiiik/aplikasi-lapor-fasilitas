<?php

$host = "localhost";
$user = "YOUR_DB_USERNAME";
$password = "YOUR_DB_PASSWORD";
$database = "laporfasilitas";
$port = 3306;

$conn = mysqli_connect(
    $host,
    $user,
    $password,
    $database,
    $port
);

if (!$conn) {
    die("Koneksi database gagal.");
}

mysqli_set_charset($conn, "utf8mb4");