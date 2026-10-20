<?php
// Membutuhkan: $p (pengaduan), $riwayat (array), $umpan (array)
?>
<div class="card mb-3">
  <div class="card-body">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
      <h5 class="fw-bold mb-0"><?= e($p['judul']) ?></h5>
      <?= badgeStatus($p['status']) ?>
    </div>
    <hr>
    <div class="row g-3 small">
      <div class="col-sm-6"><div class="text-muted">Pelapor</div><?= e($p['nama']) ?> (<?= e($p['kelas'] ?? '-') ?>)</div>
      <div class="col-sm-6"><div class="text-muted">Tanggal Pengaduan</div><?= tgl($p['tanggal_pengaduan']) ?></div>
      <div class="col-sm-6"><div class="text-muted">Kategori</div><?= e($p['nama_kategori']) ?></div>
      <div class="col-sm-6"><div class="text-muted">Lokasi</div><?= e($p['lokasi']) ?></div>
      <div class="col-12"><div class="text-muted">Deskripsi</div><?= nl2br(e($p['deskripsi'])) ?></div>
      <?php if ($p['foto']): ?>
        <div class="col-12"><div class="text-muted mb-1">Foto Bukti</div>
          <a href="<?= BASE_URL ?>/uploads/<?= e($p['foto']) ?>" target="_blank">
            <img src="<?= BASE_URL ?>/uploads/<?= e($p['foto']) ?>" class="foto-bukti" alt="Foto bukti"></a>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<div class="row g-3">
  <div class="col-md-6">
    <div class="card h-100"><div class="card-body">
      <h6 class="fw-bold mb-3"><i class="bi bi-clock-history"></i> Riwayat Status</h6>
      <ul class="timeline small mb-0">
        <?php foreach ($riwayat as $r): ?>
          <li>
            <div><?= $r['status_lama'] ? e($r['status_lama']) . ' &rarr; ' : '' ?><strong><?= e($r['status_baru']) ?></strong></div>
            <?php if ($r['catatan']): ?><div class="text-muted"><?= e($r['catatan']) ?></div><?php endif; ?>
            <div class="text-muted"><?= tgl($r['created_at'], true) ?></div>
          </li>
        <?php endforeach; ?>
      </ul>
    </div></div>
  </div>
  <div class="col-md-6">
    <div class="card h-100"><div class="card-body">
      <h6 class="fw-bold mb-3"><i class="bi bi-chat-left-text"></i> Umpan Balik Admin</h6>
      <?php if (!$umpan): ?>
        <div class="text-muted small">Belum ada umpan balik.</div>
      <?php endif; ?>
      <?php foreach ($umpan as $f): ?>
        <div class="border rounded p-2 mb-2 small bg-light">
          <?= nl2br(e($f['isi'])) ?>
          <div class="text-muted mt-1"><?= e($f['nama_admin']) ?> &middot; <?= tgl($f['created_at'], true) ?></div>
        </div>
      <?php endforeach; ?>
    </div></div>
  </div>
</div>
