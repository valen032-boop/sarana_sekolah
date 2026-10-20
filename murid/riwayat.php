<?php
require_once __DIR__ . '/../config/auth.php';
wajibRole('murid');

// Hanya pengaduan milik murid yang login (ID dari session)
$stmt = $pdo->prepare('SELECT p.*, k.nama_kategori,
    (SELECT COUNT(*) FROM umpan_balik u WHERE u.id_pengaduan = p.id_pengaduan) AS jml_umpan
    FROM pengaduan p JOIN kategori k ON k.id_kategori = p.id_kategori
    WHERE p.id_user = ? ORDER BY p.id_pengaduan DESC');
$stmt->execute([user()['id']]);
$data = $stmt->fetchAll();

$judulHalaman = 'Riwayat Pengaduan';
require __DIR__ . '/../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h4 class="fw-bold mb-0">Riwayat Pengaduan</h4>
  <a href="buat.php" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Buat Pengaduan</a>
</div>
<div class="card"><div class="card-body">
<?php if (!$data): ?>
  <div class="text-center text-muted py-4">
    <i class="bi bi-inbox" style="font-size:2.5rem"></i>
    <div class="mt-2">Anda belum membuat pengaduan.</div>
  </div>
<?php else: ?>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr><th>#</th><th>Judul</th><th>Kategori</th><th>Tanggal</th><th>Status</th><th>Umpan Balik</th><th></th></tr>
      </thead>
      <tbody>
      <?php foreach ($data as $i => $r): ?>
        <tr>
          <td><?= $i + 1 ?></td>
          <td><?= e($r['judul']) ?></td>
          <td><?= e($r['nama_kategori']) ?></td>
          <td><?= tgl($r['tanggal_pengaduan']) ?></td>
          <td><?= badgeStatus($r['status']) ?></td>
          <td><?= $r['jml_umpan'] > 0 ? '<span class="text-success small"><i class="bi bi-chat-dots"></i> Ada</span>' : '<span class="text-muted small">-</span>' ?></td>
          <td class="text-end"><a href="detail.php?id=<?= $r['id_pengaduan'] ?>" class="btn btn-sm btn-outline-primary">Detail</a></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>
</div></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
