<?php
require_once __DIR__ . '/../config/auth.php';
wajibRole('murid');

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

// Filter id_user dari session: murid tidak bisa melihat laporan milik murid lain
$stmt = $pdo->prepare('SELECT p.*, k.nama_kategori, u.nama, u.kelas
    FROM pengaduan p
    JOIN kategori k ON k.id_kategori = p.id_kategori
    JOIN users u ON u.id_user = p.id_user
    WHERE p.id_pengaduan = ? AND p.id_user = ?');
$stmt->execute([$id ?: 0, user()['id']]);
$p = $stmt->fetch();

if (!$p) {
    setFlash('danger', 'Pengaduan tidak ditemukan.');
    redirect('murid/riwayat.php');
}

$s = $pdo->prepare('SELECT * FROM riwayat_status WHERE id_pengaduan = ? ORDER BY id_riwayat ASC');
$s->execute([$p['id_pengaduan']]);
$riwayat = $s->fetchAll();

$s = $pdo->prepare('SELECT f.*, a.nama AS nama_admin FROM umpan_balik f
    JOIN users a ON a.id_user = f.id_admin WHERE f.id_pengaduan = ? ORDER BY f.id_umpan_balik ASC');
$s->execute([$p['id_pengaduan']]);
$umpan = $s->fetchAll();

$judulHalaman = 'Detail Pengaduan';
require __DIR__ . '/../includes/header.php';
?>
<div class="mb-3"><a href="riwayat.php" class="text-decoration-none"><i class="bi bi-arrow-left"></i> Kembali ke riwayat</a></div>
<?php require __DIR__ . '/../includes/detail_pengaduan.php'; ?>
<?php require __DIR__ . '/../includes/footer.php'; ?>
