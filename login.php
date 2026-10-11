<?php

header("Content-Type: application/json");

include "../config/database.php";

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username == '' || $password == '') {
    echo json_encode([
        "status" => false,
        "message" => "Username dan password wajib diisi"
    ]);
    exit;
}

$stmt = mysqli_prepare(
    $conn,
    "SELECT id, username, password, nama, role
     FROM users
     WHERE username = ?"
);

mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if ($user) {
    $storedPassword = $user['password'];

    $validPassword = password_verify($password, $storedPassword)
        || hash_equals($storedPassword, $password);

    if ($validPassword) {
        echo json_encode([
            "status" => true,
            "message" => "Login berhasil",
            "data" => [
                "id" => $user['id'],
                "username" => $user['username'],
                "nama" => $user['nama'],
                "role" => $user['role']
            ]
        ]);
        exit;
    }
}

echo json_encode([
    "status" => false,
    "message" => "Username atau password salah"
]);

?>