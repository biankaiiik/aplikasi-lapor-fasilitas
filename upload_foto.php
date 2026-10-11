<?php
header('Content-Type: application/json; charset=utf-8');

function balas($status, $message, $foto = null) {
    echo json_encode([
        'status' => $status,
        'message' => $message,
        'foto' => $foto
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    balas(false, 'Gunakan metode POST.');
}

$tmp = null;
$ukuran = 0;
$perluHapusTmp = false;

/* Cara 1: upload multipart */
if (isset($_FILES['file']) &&
    $_FILES['file']['error'] === UPLOAD_ERR_OK) {
    $tmp = $_FILES['file']['tmp_name'];
    $ukuran = (int) $_FILES['file']['size'];
}

/* Cara 2: isi file mentah dari Web.PostFile */
if ($tmp === null) {
    $raw = file_get_contents('php://input');

    if ($raw === false || strlen($raw) === 0) {
        balas(false, 'Data foto kosong. Periksa Web_Upload.PostFile.');
    }

    $ukuran = strlen($raw);
    $tmp = tempnam(sys_get_temp_dir(), 'foto_');

    if ($tmp === false || file_put_contents($tmp, $raw) === false) {
        balas(false, 'Gagal membaca data foto.');
    }

    $perluHapusTmp = true;
}

if ($ukuran > 5 * 1024 * 1024) {
    if ($perluHapusTmp) @unlink($tmp);
    balas(false, 'Ukuran foto maksimal 5 MB.');
}

$info = @getimagesize($tmp);

if ($info === false) {
    if ($perluHapusTmp) @unlink($tmp);
    balas(false, 'Data yang diterima bukan gambar yang valid.');
}

$allowed = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/webp' => 'webp'
];

$mime = $info['mime'] ?? '';

if (!isset($allowed[$mime])) {
    if ($perluHapusTmp) @unlink($tmp);
    balas(false, 'Format foto harus JPG, PNG, atau WEBP.');
}

$folder = dirname(__DIR__) . '/uploads/laporan/';
if (!is_dir($folder)) {
    if (!mkdir($folder, 0755, true) && !is_dir($folder)) {
        balas(false, 'Folder penyimpanan gagal dibuat: ' . $folder);
    }
}

$namaFile = bin2hex(random_bytes(16)) . '.' . $allowed[$mime];
$tujuan = $folder . $namaFile;

if ($perluHapusTmp) {
    $tersimpan = copy($tmp, $tujuan);
    @unlink($tmp);
} else {
    $tersimpan = move_uploaded_file($tmp, $tujuan);
}

if (!$tersimpan) {
    balas(false, 'Foto gagal disimpan.');
}

balas(true, 'Foto berhasil diunggah.', 'uploads/laporan/' . $namaFile);
?>