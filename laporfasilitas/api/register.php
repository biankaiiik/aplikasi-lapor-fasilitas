<?php

header("Content-Type: application/json");

include "../config/database.php";

if (!isset($_POST['nama'], $_POST['username'], $_POST['password'])) {
    echo json_encode([
        "status" => false,
        "message" => "Data POST tidak lengkap",
        "received" => array_keys($_POST)
    ]);
    exit;
}

$nama = trim($_POST['nama'] ?? '');
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($nama == '' || $username == ''||  $password == '') {
    echo json_encode([
        "status" => false,
        "message" => "Semua kolom wajib diisi"
    ]);
    exit;
}

if (strlen($username) < 3 || strlen($password) < 5) {
    echo json_encode([
        "status" => false,
        "message" => "Username minimal 3 karakter dan password minimal 5 karakter"
    ]);
    exit;
}

$check = mysqli_prepare(
    $conn,
    "SELECT id FROM users WHERE username = ?"
);

mysqli_stmt_bind_param($check, "s", $username);
mysqli_stmt_execute($check);
mysqli_stmt_store_result($check);

if (mysqli_stmt_num_rows($check) > 0) {
    echo json_encode([
        "status" => false,
        "message" => "Username sudah digunakan"
    ]);
    exit;
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$stmt = mysqli_prepare(
    $conn,
    "INSERT INTO users (nama, username, password, role)
     VALUES (?, ?, ?, 'user')"
);

mysqli_stmt_bind_param($stmt, "sss", $nama, $username, $hashedPassword);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode([
        "status" => true,
        "message" => "Pendaftaran berhasil"
    ]);
} else {
    echo json_encode([
        "status" => false,
        "message" => "Pendaftaran gagal"
    ]);
}

?>