<?php
header("Content-Type: application/json; charset=UTF-8");

include "../config/database.php";

$data = [];

if (!empty($_POST)) {
    $data = $_POST;
} else {
    $raw = file_get_contents("php://input");
    parse_str($raw, $data);
}

$user_id = intval($data["user_id"] ?? 0);
$kategori = trim($data["kategori"] ?? "");
$lokasi = trim($data["lokasi"] ?? "");
$deskripsi = trim($data["deskripsi"] ?? "");
$foto = trim($data["foto"] ?? "");

// Validasi data wajib
if (
    $user_id <= 0 ||
    $kategori === "" ||
    $lokasi === "" ||
    $deskripsi === ""
) {
    echo json_encode([
        "status" => false,
        "message" => "Semua data wajib diisi",
        "received" => [
            "user_id" => $user_id,
            "kategori" => $kategori,
            "lokasi" => $lokasi,
            "deskripsi" => $deskripsi,
            "foto" => $foto
        ]
    ]);
    exit;
}

// Periksa apakah pengguna tersedia
$check = mysqli_prepare(
    $conn,
    "SELECT id FROM users WHERE id = ?"
);

if (!$check) {
    echo json_encode([
        "status" => false,
        "message" => "Gagal memeriksa pengguna"
    ]);
    exit;
}

mysqli_stmt_bind_param($check, "i", $user_id);
mysqli_stmt_execute($check);
mysqli_stmt_store_result($check);

if (mysqli_stmt_num_rows($check) === 0) {
    mysqli_stmt_close($check);

    echo json_encode([
        "status" => false,
        "message" => "Pengguna tidak ditemukan"
    ]);
    exit;
}

mysqli_stmt_close($check);

// Simpan laporan beserta alamat foto
$stmt = mysqli_prepare(
    $conn,
    "INSERT INTO laporan
    (user_id, kategori, lokasi, deskripsi, foto, status)
    VALUES (?, ?, ?, ?, ?, 'Menunggu')"
);

if (!$stmt) {
    echo json_encode([
        "status" => false,
        "message" => "Gagal menyiapkan penyimpanan laporan"
    ]);
    exit;
}

mysqli_stmt_bind_param(
    $stmt,
    "issss",
    $user_id,
    $kategori,
    $lokasi,
    $deskripsi,
    $foto
);

// Kirim hasil penyimpanan
if (mysqli_stmt_execute($stmt)) {
    echo json_encode([
        "status" => true,
        "message" => "Laporan berhasil dikirim",
        "id_laporan" => mysqli_insert_id($conn),
        "foto" => $foto
    ]);
} else {
    echo json_encode([
        "status" => false,
        "message" => "Gagal menyimpan laporan"
    ]);
}

mysqli_stmt_close($stmt);
?>