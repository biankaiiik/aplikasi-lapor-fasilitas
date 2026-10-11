<?php
session_start();require_once __DIR__.'/_auth.php';require_once __DIR__.'/../config/database.php';
if($_SERVER['REQUEST_METHOD']!=='POST'){header('Location: index.php');exit;}
if(!hash_equals($_SESSION['csrf_token']??'',$_POST['csrf_token']??'')){http_response_code(403);exit('Permintaan tidak valid.');}
$id=filter_input(INPUT_POST,'id',FILTER_VALIDATE_INT);$status=$_POST['status']??'';
if(!$id||!in_array($status,['Menunggu','Diproses','Selesai'],true)){http_response_code(400);exit('Data tidak valid.');}
$stmt=mysqli_prepare($conn,"UPDATE laporan SET status=? WHERE id=?");mysqli_stmt_bind_param($stmt,'si',$status,$id);mysqli_stmt_execute($stmt);mysqli_stmt_close($stmt);
header('Location: detail.php?id='.$id);exit;
