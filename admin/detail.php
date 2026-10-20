<?php
require_once __DIR__ . '/../config/auth.php';
wajibRole('admin');

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    setFlash('danger', 'ID pengaduan tidak valid.');
    redirect('admin/pengaduan.php');
}

$ambil = $pdo->prepare('SELECT p.*, k.nama_kategori, u.nama, u.kelas
    FROM pengaduan p
    JOIN kategori k ON k.id_kategori = p.id_kategori
    JOIN users u ON u.id_user = p.id_user
    WHERE p.id_pengaduan = ?');
$ambil->execute([$id]);
$p = $ambil->fetch();
if (!$p) {
    setFlash('danger', 'Pengaduan tidak ditemukan.');
    redirect('admin/pengaduan.php');
}

// Proses pembaruan status & umpan balik
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $statusBaru = $_POST['status'] ?? '';
    $isi = trim($_POST['umpan_balik'] ?? '');

    if (!csrfCek()) {
        setFlash('danger', 'Sesi tidak valid, silakan coba lagi.');
    } elseif (!in_array($statusBaru, STATUS_LIST, true)) {
        setFlash('danger', 'Status tidak valid.');
    } elseif ($statusBaru === $p['status'] && $isi === '') {
        setFlash('warning', 'Tidak ada perubahan: ubah status atau isi umpan balik.');
    } else {
        try {
            $pdo->beginTransaction();
            if ($statusBaru !== $p['status']) {
                $pdo->prepare('UPDATE pengaduan SET status = ? WHERE id_pengaduan = ?')->execute([$statusBaru, $id]);
                $catatan = $isi !== '' ? mb_substr($isi, 0, 255) : 'Status diperbarui oleh admin';
                $pdo->prepare('INSERT INTO riwayat_status (id_pengaduan, id_admin, status_lama, status_baru, catatan)
                    VALUES (?, ?, ?, ?, ?)')->execute([$id, user()['id'], $p['status'], $statusBaru, $catatan]);
            }
            if ($isi !== '') {
                $pdo->prepare('INSERT INTO umpan_balik (id_pengaduan, id_admin, isi) VALUES (?, ?, ?)')
                    ->execute([$id, user()['id'], $isi]);
            }
            $pdo->commit();
            setFlash('success', 'Pengaduan berhasil diperbarui.');
        } catch (Throwable $ex) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            setFlash('danger', 'Gagal menyimpan perubahan.');
        }
    }
    redirect('admin/detail.php?id=' . $id);
}

$s = $pdo->prepare('SELECT * FROM riwayat_status WHERE id_pengaduan = ? ORDER BY id_riwayat ASC');
$s->execute([$id]);
$riwayat = $s->fetchAll();

$s = $pdo->prepare('SELECT f.*, a.nama AS nama_admin FROM umpan_balik f
    JOIN users a ON a.id_user = f.id_admin WHERE f.id_pengaduan = ? ORDER BY f.id_umpan_balik ASC');
$s->execute([$id]);
$umpan = $s->fetchAll();

$judulHalaman = 'Detail Pengaduan';
require __DIR__ . '/../includes/header.php';
?>
<div class="mb-3"><a href="pengaduan.php" class="text-decoration-none"><i class="bi bi-arrow-left"></i> Kembali ke data pengaduan</a></div>

<div class="card mb-3 border-start border-4 border-primary">
  <div class="card-body">
    <h6 class="fw-bold mb-3"><i class="bi bi-pencil-square"></i> Perbarui Status &amp; Umpan Balik</h6>
    <form method="post" class="row g-3">
      <?= csrfField() ?>
      <div class="col-md-3">
        <label class="form-label small">Status</label>
        <select name="status" class="form-select">
          <?php foreach (STATUS_LIST as $st): ?>
            <option <?= $st === $p['status'] ? 'selected' : '' ?>><?= e($st) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-9">
        <label class="form-label small">Umpan balik / catatan penanganan <span class="text-muted">(opsional)</span></label>
        <textarea name="umpan_balik" rows="2" class="form-control" placeholder="Tulis tanggapan untuk murid..."></textarea>
      </div>
      <div class="col-12"><button class="btn btn-primary"><i class="bi bi-save"></i> Simpan</button></div>
    </form>
  </div>
</div>

<?php require __DIR__ . '/../includes/detail_pengaduan.php'; ?>
<?php require __DIR__ . '/../includes/footer.php'; ?>
