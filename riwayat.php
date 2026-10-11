<?php
header("Content-Type: application/json; charset=UTF-8");
include "../config/database.php";

$user_id = intval($_GET["user_id"] ?? 0);

if ($user_id <= 0) {
    echo json_encode([
        "status" => false,
        "message" => "ID pengguna tidak valid",
        "data" => []
    ]);
    exit;
}

$stmt = mysqli_prepare(
    $conn,
    "SELECT id, kategori, lokasi, deskripsi, status, tanggal
     FROM laporan
     WHERE user_id = ?
     ORDER BY tanggal DESC"
);

if (!$stmt) {
    echo json_encode([
        "status" => false,
        "message" => "Gagal mengambil riwayat",
        "data" => []
    ]);
    exit;
}

mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$data = [];

while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

echo json_encode([
    "status" => true,
    "message" => "Riwayat berhasil diambil",
    "data" => $data
]);

mysqli_stmt_close($stmt);
?>