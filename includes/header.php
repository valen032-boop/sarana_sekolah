<?php
// Variabel: $judulHalaman. Menu mengikuti role pengguna.
$u = user();
$menu = [];
if ($u && $u['role'] === 'murid') {
    $menu = [
        'murid/dashboard.php' => 'Dashboard',
        'murid/buat.php'      => 'Buat Pengaduan',
        'murid/riwayat.php'   => 'Riwayat Pengaduan',
    ];
} elseif ($u && $u['role'] === 'admin') {
    $menu = [
        'admin/dashboard.php' => 'Dashboard',
        'admin/pengaduan.php' => 'Data Pengaduan',
    ];
}
$aktif = basename($_SERVER['SCRIPT_NAME']);
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($judulHalaman ?? 'Pengaduan Sekolah') ?> - Pengaduan Sarana Sekolah</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-md navbar-dark bg-primary shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-semibold" href="<?= BASE_URL ?>/">
      <i class="bi bi-building-check me-1"></i> Pengaduan Sekolah
    </a>
    <?php if ($u): ?>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav me-auto">
        <?php foreach ($menu as $path => $label): ?>
          <li class="nav-item">
            <a class="nav-link <?= basename($path) === $aktif ? 'active' : '' ?>"
               href="<?= BASE_URL . '/' . $path ?>"><?= e($label) ?></a>
          </li>
        <?php endforeach; ?>
      </ul>
      <div class="d-flex align-items-center text-white gap-3">
        <span class="small"><i class="bi bi-person-circle"></i> <?= e($u['nama']) ?>
          <span class="badge bg-light text-primary ms-1"><?= e(ucfirst($u['role'])) ?></span></span>
        <a class="btn btn-sm btn-light" href="<?= BASE_URL ?>/logout.php">Logout</a>
      </div>
    </div>
    <?php endif; ?>
  </div>
</nav>
<main class="container py-4">
<?php if ($flash): ?>
  <div class="alert alert-<?= e($flash[0]) ?> alert-dismissible fade show" role="alert">
    <?= e($flash[1]) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php endif; ?>
