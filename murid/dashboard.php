<?php
require_once __DIR__ . '/../config/auth.php';
wajibRole('murid');
$uid = user()['id'];

// Ringkasan jumlah pengaduan per status
$stmt = $pdo->prepare('SELECT status, COUNT(*) AS jml FROM pengaduan WHERE id_user = ? GROUP BY status');
$stmt->execute([$uid]);
$ringkas = array_fill_keys(STATUS_LIST, 0);
foreach ($stmt as $r) $ringkas[$r['status']] = (int) $r['jml'];

$stmt = $pdo->prepare('SELECT p.*, k.nama_kategori FROM pengaduan p
    JOIN kategori k ON k.id_kategori = p.id_kategori
    WHERE p.id_user = ? ORDER BY p.id_pengaduan DESC LIMIT 5');
$stmt->execute([$uid]);
$terbaru = $stmt->fetchAll();

$ikon = ['Menunggu' => 'hourglass-split', 'Diproses' => 'gear', 'Selesai' => 'check-circle', 'Ditolak' => 'x-circle'];
$warna = ['Menunggu' => 'warning', 'Diproses' => 'info', 'Selesai' => 'success', 'Ditolak' => 'danger'];

$judulHalaman = 'Dashboard';
require __DIR__ . '/../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
  <div>
    <h4 class="fw-bold mb-0">Halo, <?= e(user()['nama']) ?> 👋</h4>
    <div class="text-muted small">Kelas <?= e(user()['kelas'] ?? '-') ?></div>
  </div>
  <a href="buat.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Buat Pengaduan</a>
</div>

<div class="row g-3 mb-4">
  <?php foreach ($ringkas as $s => $n): ?>
    <div class="col-6 col-md-3">
      <div class="card stat-card border-start border-4 border-<?= $warna[$s] ?>">
        <div class="card-body d-flex justify-content-between align-items-center">
          <div><div class="text-muted small"><?= e($s) ?></div><div class="angka"><?= $n ?></div></div>
          <i class="bi bi-<?= $ikon[$s] ?>"></i>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<div class="card">
  <div class="card-body">
    <div class="d-flex justify-content-between mb-2">
      <h6 class="fw-bold mb-0">Pengaduan Terbaru</h6>
      <a href="riwayat.php" class="small">Lihat semua</a>
    </div>
    <?php if (!$terbaru): ?>
      <div class="text-muted py-3 text-center">Belum ada pengaduan.</div>
    <?php else: ?>
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light"><tr><th>Judul</th><th>Kategori</th><th>Tanggal</th><th>Status</th></tr></thead>
        <tbody>
        <?php foreach ($terbaru as $r): ?>
          <tr>
            <td><a href="detail.php?id=<?= $r['id_pengaduan'] ?>" class="text-decoration-none"><?= e($r['judul']) ?></a></td>
            <td><?= e($r['nama_kategori']) ?></td>
            <td><?= tgl($r['tanggal_pengaduan']) ?></td>
            <td><?= badgeStatus($r['status']) ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
