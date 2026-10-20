<?php
require_once __DIR__ . '/../config/auth.php';
wajibRole('admin');

// Ambil filter dari URL
$q        = trim($_GET['q'] ?? '');
$status   = $_GET['status'] ?? '';
$kategori = (int) ($_GET['kategori'] ?? 0);
$murid    = (int) ($_GET['murid'] ?? 0);
$tanggal  = $_GET['tanggal'] ?? '';
$bulan    = $_GET['bulan'] ?? '';   // format YYYY-MM

if (!in_array($status, STATUS_LIST, true)) $status = '';
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) $tanggal = '';
if (!preg_match('/^\d{4}-\d{2}$/', $bulan)) $bulan = '';

// Susun query dinamis dengan prepared statement
$where = [];
$par = [];
if ($q !== '')      { $where[] = '(p.judul LIKE ? OR p.deskripsi LIKE ? OR p.lokasi LIKE ?)'; array_push($par, "%$q%", "%$q%", "%$q%"); }
if ($status !== '') { $where[] = 'p.status = ?';            $par[] = $status; }
if ($kategori)      { $where[] = 'p.id_kategori = ?';       $par[] = $kategori; }
if ($murid)         { $where[] = 'p.id_user = ?';           $par[] = $murid; }
if ($tanggal !== ''){ $where[] = 'p.tanggal_pengaduan = ?'; $par[] = $tanggal; }
if ($bulan !== '')  { $where[] = 'DATE_FORMAT(p.tanggal_pengaduan, "%Y-%m") = ?'; $par[] = $bulan; }

$sql = 'SELECT p.*, k.nama_kategori, u.nama, u.kelas
        FROM pengaduan p
        JOIN kategori k ON k.id_kategori = p.id_kategori
        JOIN users u ON u.id_user = p.id_user'
     . ($where ? ' WHERE ' . implode(' AND ', $where) : '')
     . ' ORDER BY p.tanggal_pengaduan DESC, p.id_pengaduan DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute($par);
$data = $stmt->fetchAll();

$listKategori = $pdo->query('SELECT * FROM kategori ORDER BY nama_kategori')->fetchAll();
$listMurid = $pdo->query('SELECT id_user, nama, kelas FROM users WHERE role = "murid" ORDER BY nama')->fetchAll();

$judulHalaman = 'Data Pengaduan';
require __DIR__ . '/../includes/header.php';
?>
<h4 class="fw-bold mb-3">Data Pengaduan</h4>

<div class="card mb-3"><div class="card-body">
  <form method="get" class="row g-2 align-items-end">
    <div class="col-md-4">
      <label class="form-label small mb-1">Cari (judul / deskripsi / lokasi)</label>
      <input type="text" name="q" class="form-control" value="<?= e($q) ?>" placeholder="Kata kunci...">
    </div>
    <div class="col-md-2 col-6">
      <label class="form-label small mb-1">Status</label>
      <select name="status" class="form-select">
        <option value="">Semua</option>
        <?php foreach (STATUS_LIST as $s): ?>
          <option <?= $s === $status ? 'selected' : '' ?>><?= e($s) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-3 col-6">
      <label class="form-label small mb-1">Kategori</label>
      <select name="kategori" class="form-select">
        <option value="0">Semua</option>
        <?php foreach ($listKategori as $k): ?>
          <option value="<?= $k['id_kategori'] ?>" <?= $k['id_kategori'] == $kategori ? 'selected' : '' ?>><?= e($k['nama_kategori']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-3">
      <label class="form-label small mb-1">Murid</label>
      <select name="murid" class="form-select">
        <option value="0">Semua</option>
        <?php foreach ($listMurid as $m): ?>
          <option value="<?= $m['id_user'] ?>" <?= $m['id_user'] == $murid ? 'selected' : '' ?>><?= e($m['nama']) ?> (<?= e($m['kelas']) ?>)</option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-3 col-6">
      <label class="form-label small mb-1">Tanggal</label>
      <input type="date" name="tanggal" class="form-control" value="<?= e($tanggal) ?>">
    </div>
    <div class="col-md-3 col-6">
      <label class="form-label small mb-1">Bulan</label>
      <input type="month" name="bulan" class="form-control" value="<?= e($bulan) ?>">
    </div>
    <div class="col-md-6 d-flex gap-2">
      <button class="btn btn-primary"><i class="bi bi-funnel"></i> Terapkan</button>
      <a href="pengaduan.php" class="btn btn-outline-secondary">Reset</a>
    </div>
  </form>
</div></div>

<div class="card"><div class="card-body">
  <div class="text-muted small mb-2">Menampilkan <?= count($data) ?> pengaduan</div>
  <?php if (!$data): ?>
    <div class="text-center text-muted py-4"><i class="bi bi-search" style="font-size:2rem"></i><div class="mt-2">Tidak ada data yang sesuai filter.</div></div>
  <?php else: ?>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr><th>Tanggal</th><th>Pelapor</th><th>Judul</th><th>Kategori</th><th>Lokasi</th><th>Status</th><th></th></tr>
      </thead>
      <tbody>
      <?php foreach ($data as $r): ?>
        <tr>
          <td><?= tgl($r['tanggal_pengaduan']) ?></td>
          <td><?= e($r['nama']) ?><div class="text-muted small"><?= e($r['kelas']) ?></div></td>
          <td><?= e($r['judul']) ?></td>
          <td><?= e($r['nama_kategori']) ?></td>
          <td><?= e($r['lokasi']) ?></td>
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
