<?php
header('Content-Type: application/json; charset=utf-8');

require_once '../config/database.php';

$sql = "SELECT
    COUNT(*) AS total,
    SUM(status = 'Menunggu') AS menunggu,
    SUM(status = 'Diproses') AS diproses,
    SUM(status = 'Selesai') AS selesai
    FROM laporan";

$result = mysqli_query($conn, $sql);

if (!$result) {
    echo json_encode([
        "status" => false,
        "message" => "Gagal mengambil statistik"
    ]);
    exit;
}

$data = mysqli_fetch_assoc($result);

echo json_encode([
    "status" => true,
    "data" => [
        "total" => (int) $data['total'],
        "menunggu" => (int) $data['menunggu'],
        "diproses" => (int) $data['diproses'],
        "selesai" => (int) $data['selesai']
    ]
]);
?>
