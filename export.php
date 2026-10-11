<?php
session_start();require_once __DIR__.'/_auth.php';require_once __DIR__.'/../config/database.php';
function e($v){return htmlspecialchars((string)($v??''),ENT_QUOTES,'UTF-8');}
function fotoUrl($foto){$foto=trim((string)$foto);if($foto===''||preg_match('~^(https?:)?//~i',$foto)||strpos(str_replace('\\','/',$foto),'..')!==false)return '';return '../'.ltrim(str_replace('\\','/',$foto),'/');}
$sql="SELECT l.id,u.nama AS nama_pelapor,u.username,l.kategori,l.lokasi,l.deskripsi,l.foto,l.status,l.tanggal FROM laporan l LEFT JOIN users u ON l.user_id=u.id ORDER BY l.tanggal DESC,l.id DESC";
$result=mysqli_query($conn,$sql);if(!$result){http_response_code(500);exit('Gagal mengambil data laporan.');}
$filename='rekap_laporfasilitas_'.date('Ymd_His').'.xls';
header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
header('Content-Disposition: attachment; filename="'.$filename.'"');
header('Cache-Control: no-store, no-cache, must-revalidate');
echo "\xEF\xBB\xBF";
?>
<!doctype html><html lang="id"><head><meta charset="UTF-8"><style>body{font-family:Arial,sans-serif}table{border-collapse:collapse}th,td{border:1px solid #0b1f44;padding:7px;vertical-align:top}th{background:#0b1f44;color:white}.foto{width:100px;height:auto;max-height:90px}</style></head><body>
<h2>REKAP LAPORAN LAPORFASILITAS</h2><p>Tanggal ekspor: <?=date('d-m-Y H:i:s')?></p>
<table><thead><tr><th>No.</th><th>Pelapor</th><th>Username</th><th>Fasilitas</th><th>Lokasi</th><th>Deskripsi</th><th>Foto Kerusakan</th><th>Status</th><th>Tanggal</th></tr></thead><tbody>
<?php $no=1;while($r=mysqli_fetch_assoc($result)):$src=fotoUrl($r['foto']);?><tr><td><?=$no++?></td><td><?=e($r['nama_pelapor'])?></td><td><?=e($r['username'])?></td><td><?=e($r['kategori'])?></td><td><?=e($r['lokasi'])?></td><td><?=nl2br(e($r['deskripsi']))?></td><td><?php if($src):?><img class="foto" src="<?=e($src)?>" alt="Foto kerusakan"><br><a href="<?=e($src)?>">Lihat foto</a><?php else:?>Tidak ada foto<?php endif;?></td><td><?=e($r['status'])?></td><td><?=e($r['tanggal'])?></td></tr><?php endwhile;?>
</tbody></table></body></html>
