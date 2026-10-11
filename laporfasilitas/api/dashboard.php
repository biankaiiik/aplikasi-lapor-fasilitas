<?php
header("Content-Type: application/json");
include "../config/database.php";

$user_id = intval($_GET['user_id'] ?? 0);

if ($user_id <= 0) {
    echo json_encode([
        "status" => false,
        "message" => "User ID tidak valid"
    ]);
    exit;
}

$stmt = mysqli_prepare(
    $conn,
    "SELECT
        COUNT(*) AS total,
        SUM(status = 'Menunggu') AS menunggu,
        SUM(status = 'Diproses') AS diproses,
        SUM(status = 'Selesai') AS selesai
     FROM laporan
     WHERE user_id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

echo json_encode([
    "status" => true,
    "data" => [
        "total" => (int)$data['total'],
        "menunggu" => (int)$data['menunggu'],
        "diproses" => (int)$data['diproses'],
        "selesai" => (int)$data['selesai']
    ]
]);
?>