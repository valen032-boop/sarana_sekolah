<?php
require_once __DIR__ . '/../config/auth.php';
wajibRole('admin');

$ringkas = array_fill_keys(STATUS_LIST, 0);
foreach ($pdo->query('SELECT status, COUNT(*) AS jml FROM pengaduan GROUP BY status') as $r) {
    $ringkas[$r['status']] = (int) $r['jml'];
}
$total = array_sum($ringkas);

$terbaru = $pdo->query('SELECT p.*, k.nama_kategori, u.nama
    FROM pengaduan p
    JOIN kategori k ON k.id_kategori = p.id_kategori
    JOIN users u ON u.id_user = p.id_user
    ORDER BY p.id_pengaduan DESC LIMIT 8')->fetchAll();

$ikon = ['Menunggu' => 'hourglass-split', 'Diproses' => 'gear', 'Selesai' => 'check-circle', 'Ditolak' => 'x-circle'];
$warna = ['Menunggu' => 'warning', 'Diproses' => 'info', 'Selesai' => 'success', 'Ditolak' => 'danger'];

$judulHalaman = 'Dashboard Admin';
require __DIR__ . '/../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
  <div>
    <h4 class="fw-bold mb-0">Dashboard Admin</h4>
    <div class="text-muted small">Total <?= $total ?> pengaduan masuk</div>
  </div>
  <a href="pengaduan.php" class="btn btn-primary"><i class="bi bi-list-ul"></i> Kelola Pengaduan</a>
</div>

<div class="row g-3 mb-4">
  <?php foreach ($ringkas as $s => $n): ?>
    <div class="col-6 col-md-3">
      <a href="pengaduan.php?status=<?= urlencode($s) ?>" class="text-decoration-none text-body">
        <div class="card stat-card border-start border-4 border-<?= $warna[$s] ?>">
          <div class="card-body d-flex justify-content-between align-items-center">
            <div><div class="text-muted small"><?= e($s) ?></div><div class="angka"><?= $n ?></div></div>
            <i class="bi bi-<?= $ikon[$s] ?>"></i>
          </div>
        </div>
      </a>
    </div>
  <?php endforeach; ?>
</div>

<div class="card"><div class="card-body">
  <h6 class="fw-bold mb-3">Pengaduan Terbaru</h6>
  <?php if (!$terbaru): ?>
    <div class="text-center text-muted py-3">Belum ada pengaduan.</div>
  <?php else: ?>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light"><tr><th>Tanggal</th><th>Pelapor</th><th>Judul</th><th>Kategori</th><th>Status</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($terbaru as $r): ?>
        <tr>
          <td><?= tgl($r['tanggal_pengaduan']) ?></td>
          <td><?= e($r['nama']) ?></td>
          <td><?= e($r['judul']) ?></td>
          <td><?= e($r['nama_kategori']) ?></td>
          <td><?= badgeStatus($r['status']) ?></td>
          <td class="text-end"><a href="detail.php?id=<?= $r['id_pengaduan'] ?>" class="btn btn-sm btn-outline-primary">Detail</a></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
