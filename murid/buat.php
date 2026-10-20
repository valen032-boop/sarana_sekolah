<?php
require_once __DIR__ . '/../config/auth.php';
wajibRole('murid');

$kategori = $pdo->query('SELECT * FROM kategori ORDER BY nama_kategori')->fetchAll();
$errors = [];
$old = ['judul' => '', 'id_kategori' => '', 'lokasi' => '', 'deskripsi' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($old as $k => $_) $old[$k] = trim($_POST[$k] ?? '');

    if (!csrfCek()) $errors[] = 'Sesi tidak valid, silakan muat ulang halaman.';
    if ($old['judul'] === '' || mb_strlen($old['judul']) > 150) $errors[] = 'Judul wajib diisi (maksimal 150 karakter).';
    if ($old['lokasi'] === '' || mb_strlen($old['lokasi']) > 100) $errors[] = 'Lokasi wajib diisi (maksimal 100 karakter).';
    if (mb_strlen($old['deskripsi']) < 10) $errors[] = 'Deskripsi minimal 10 karakter.';

    $cek = $pdo->prepare('SELECT COUNT(*) FROM kategori WHERE id_kategori = ?');
    $cek->execute([(int) $old['id_kategori']]);
    if (!$cek->fetchColumn()) $errors[] = 'Kategori tidak valid.';

    // Validasi foto (opsional)
    $namaFoto = null;
    if (!empty($_FILES['foto']['name'])) {
        $f = $_FILES['foto'];
        $izin = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        if ($f['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Upload foto gagal, coba lagi.';
        } elseif ($f['size'] > 2 * 1024 * 1024) {
            $errors[] = 'Ukuran foto maksimal 2 MB.';
        } else {
            $mime = (new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
            if (!isset($izin[$mime])) {
                $errors[] = 'Foto harus berformat JPG, PNG, atau WEBP.';
            } else {
                $namaFoto = bin2hex(random_bytes(10)) . '.' . $izin[$mime];
            }
        }
    }

    if (!$errors) {
        try {
            if ($namaFoto && !move_uploaded_file($_FILES['foto']['tmp_name'], UPLOAD_DIR . $namaFoto)) {
                throw new RuntimeException('Gagal menyimpan foto.');
            }
            $pdo->beginTransaction();
            $ins = $pdo->prepare('INSERT INTO pengaduan
                (id_user, id_kategori, judul, lokasi, deskripsi, foto, status, tanggal_pengaduan)
                VALUES (?, ?, ?, ?, ?, ?, "Menunggu", CURDATE())');
            $ins->execute([user()['id'], (int) $old['id_kategori'], $old['judul'], $old['lokasi'], $old['deskripsi'], $namaFoto]);
            $idBaru = (int) $pdo->lastInsertId();

            $pdo->prepare('INSERT INTO riwayat_status (id_pengaduan, id_admin, status_lama, status_baru, catatan)
                VALUES (?, NULL, NULL, "Menunggu", "Pengaduan dibuat")')->execute([$idBaru]);
            $pdo->commit();

            setFlash('success', 'Pengaduan berhasil dikirim.');
            redirect('murid/detail.php?id=' . $idBaru);
        } catch (Throwable $ex) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            if ($namaFoto && is_file(UPLOAD_DIR . $namaFoto)) unlink(UPLOAD_DIR . $namaFoto);
            $errors[] = 'Pengaduan gagal disimpan, silakan coba lagi.';
        }
    }
}

$judulHalaman = 'Buat Pengaduan';
require __DIR__ . '/../includes/header.php';
?>
<h4 class="fw-bold mb-3">Buat Pengaduan</h4>
<div class="card">
  <div class="card-body p-4">
    <?php if ($errors): ?>
      <div class="alert alert-danger"><ul class="mb-0">
        <?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?>
      </ul></div>
    <?php endif; ?>
    <form method="post" enctype="multipart/form-data">
      <?= csrfField() ?>
      <div class="row g-3">
        <div class="col-md-8">
          <label class="form-label">Judul Pengaduan</label>
          <input type="text" name="judul" class="form-control" maxlength="150" value="<?= e($old['judul']) ?>" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Kategori Sarana</label>
          <select name="id_kategori" class="form-select" required>
            <option value="">-- Pilih --</option>
            <?php foreach ($kategori as $k): ?>
              <option value="<?= $k['id_kategori'] ?>" <?= (string) $k['id_kategori'] === (string) $old['id_kategori'] ? 'selected' : '' ?>>
                <?= e($k['nama_kategori']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Lokasi</label>
          <input type="text" name="lokasi" class="form-control" maxlength="100" placeholder="Contoh: Lab Komputer 1" value="<?= e($old['lokasi']) ?>" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Foto Bukti <span class="text-muted">(opsional, maks 2 MB)</span></label>
          <input type="file" name="foto" class="form-control" accept="image/jpeg,image/png,image/webp">
        </div>
        <div class="col-12">
          <label class="form-label">Deskripsi Masalah</label>
          <textarea name="deskripsi" rows="5" class="form-control" required><?= e($old['deskripsi']) ?></textarea>
        </div>
      </div>
      <div class="mt-4 d-flex gap-2">
        <button class="btn btn-primary"><i class="bi bi-send"></i> Kirim Pengaduan</button>
        <a href="dashboard.php" class="btn btn-outline-secondary">Batal</a>
      </div>
    </form>
  </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
