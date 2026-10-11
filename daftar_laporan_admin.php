<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../config/database.php';

$sql = "SELECT laporan.id, users.nama, laporan.kategori,
               laporan.lokasi, laporan.deskripsi,
               laporan.status, laporan.tanggal
        FROM laporan
        JOIN users ON laporan.user_id = users.id
        ORDER BY laporan.tanggal DESC";

$result = mysqli_query($conn, $sql);

if (!$result) {
    echo json_encode([
        "status" => false,
        "message" => "Gagal mengambil daftar laporan"
    ]);
    exit;
}

$laporan = [];

while ($row = mysqli_fetch_assoc($result)) {
    $laporan[] = $row;
}

echo json_encode([
    "status" => true,
    "data" => $laporan
]);
?>