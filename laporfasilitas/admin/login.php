<?php
session_start();
require_once __DIR__ . '/../config/database.php';
if (!empty($_SESSION['user_id']) && ($_SESSION['role'] ?? '') === 'admin') {
    header('Location: index.php'); exit;
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($username === '' || $password === '') {
        $error = 'Username dan password wajib diisi.';
    } else {
        $stmt = mysqli_prepare($conn, "SELECT id,username,password,nama,role FROM users WHERE username=? LIMIT 1");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 's', $username);
            mysqli_stmt_execute($stmt);
            $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
            mysqli_stmt_close($stmt);
            $passwordOk = $user && (password_verify($password, (string)$user['password']) || hash_equals((string)$user['password'], $password));
            if ($user && $user['role'] === 'admin' && $passwordOk) {
                session_regenerate_id(true);
                $_SESSION['user_id']=(int)$user['id'];
                $_SESSION['username']=$user['username'];
                $_SESSION['nama']=$user['nama'];
                $_SESSION['role']=$user['role'];
                $_SESSION['csrf_token']=bin2hex(random_bytes(32));
                header('Location: index.php'); exit;
            }
        }
        $error = 'Login gagal. Periksa username, password, dan role admin.';
    }
}
function e($v){return htmlspecialchars((string)($v??''),ENT_QUOTES,'UTF-8');}
?>
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Login Admin | LAPORFASILITAS</title>
<style>
*{box-sizing:border-box}body{margin:0;min-height:100vh;display:grid;place-items:center;background:#eef2f7;font-family:Arial,sans-serif;color:#14213d;padding:20px}.card{width:100%;max-width:420px;background:white;border:1px solid #dbe3ef;border-radius:18px;padding:30px;box-shadow:0 18px 45px #0b1f4430;border-top:8px solid #0b1f44}.icon{text-align:center;font-size:38px}h1{text-align:center;font-size:23px;margin:10px 0;color:#0b1f44}p{text-align:center;color:#64748b;font-size:14px;margin-bottom:24px}label{display:block;font-size:14px;font-weight:bold;margin:14px 0 7px;color:#0b1f44}input{width:100%;padding:12px;border:1px solid #bdcbe0;border-radius:8px;font-size:15px}button{width:100%;padding:13px;margin-top:22px;border:0;border-radius:8px;background:#0b1f44;color:white;font-size:15px;font-weight:bold;cursor:pointer}button:hover{background:#173b70}.error{background:#fff1f2;color:#9f1239;border-radius:8px;padding:11px;font-size:14px}
</style></head><body><div class="card"><div class="icon">🛠️</div><h1>ADMIN LAPORFASILITAS</h1><p>Login untuk mengelola laporan fasilitas sekolah.</p>
<?php if($error): ?><div class="error"><?= e($error) ?></div><?php endif; ?>
<form method="post"><label for="username">Username Admin</label><input id="username" name="username" required autocomplete="username"><label for="password">Password</label><input id="password" name="password" type="password" required autocomplete="current-password"><button type="submit">LOGIN ADMIN</button></form></div></body></html>
