<?php
session_start();require_once __DIR__.'/_auth.php';require_once __DIR__.'/../config/database.php';
if($_SERVER['REQUEST_METHOD']!=='POST'){header('Location: index.php');exit;}
if(!hash_equals($_SESSION['csrf_token']??'',$_POST['csrf_token']??'')){http_response_code(403);exit('Permintaan tidak valid.');}
$id=filter_input(INPUT_POST,'id',FILTER_VALIDATE_INT);if(!$id||$id<1){http_response_code(400);exit('ID tidak valid.');}
$stmt=mysqli_prepare($conn,"DELETE FROM laporan WHERE id=?");mysqli_stmt_bind_param($stmt,'i',$id);mysqli_stmt_execute($stmt);mysqli_stmt_close($stmt);
header('Location: index.php');exit;
