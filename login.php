<?php
require_once __DIR__ . '/config/auth.php';

if (isLogin()) redirect('index.php');

$error = '';
$id = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = trim($_POST['identitas'] ?? '');
    $pw = $_POST['password'] ?? '';

    if (!csrfCek()) {
        $error = 'Sesi tidak valid, silakan muat ulang halaman.';
    } elseif ($id === '' || $pw === '') {
        $error = 'Username/email dan password wajib diisi.';
    } else {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1');
        $stmt->execute([$id, $id]);
        $row = $stmt->fetch();

        if ($row && password_verify($pw, $row['password'])) {
            session_regenerate_id(true);
            $_SESSION['user'] = [
                'id'    => (int) $row['id_user'],
                'nama'  => $row['nama'],
                'role'  => $row['role'],
                'kelas' => $row['kelas'],
            ];
            redirect('index.php');
        }
        $error = 'Username/email atau password salah.';
    }
}

$judulHalaman = 'Login';
require __DIR__ . '/includes/header.php';
?>
<div class="login-wrap">
  <div class="text-center mb-4">
    <i class="bi bi-building-check text-primary" style="font-size:3rem"></i>
    <h4 class="fw-bold mt-2 mb-0">Pengaduan Sarana Sekolah</h4>
    <div class="text-muted small">Silakan login untuk melanjutkan</div>
  </div>
  <div class="card">
    <div class="card-body p-4">
      <?php if ($error): ?><div class="alert alert-danger py-2"><?= e($error) ?></div><?php endif; ?>
      <form method="post" autocomplete="off">
        <?= csrfField() ?>
        <div class="mb-3">
          <label class="form-label">Username atau Email</label>
          <input type="text" name="identitas" class="form-control" value="<?= e($id) ?>" required autofocus>
        </div>
        <div class="mb-3">
          <label class="form-label">Password</label>
          <input type="password" name="password" class="form-control" required>
        </div>
        <button class="btn btn-primary w-100">Login</button>
      </form>
    </div>
  </div>
  <div class="text-center text-muted small mt-3">
    Demo: <code>admin</code> / <code>admin123</code> &middot; <code>valen</code> / <code>murid123</code>
  </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
