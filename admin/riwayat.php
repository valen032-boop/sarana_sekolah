
<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Pastikan sudah login
if (!is_logged_in()) {
    redirect('../login.php');
    exit;
}

// Khusus admin
if (($_SESSION['role'] ?? '') !== 'admin') {
    redirect(dashboard_url($_SESSION['role']));
    exit;
}

$error = '';
$success = '';

// Proses perubahan status
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $status = $_POST['status'] ?? '';

    $status_valid = [
        'menunggu',
        'diproses',
        'selesai',
        'ditolak'
    ];

    if (!$id || !in_array($status, $status_valid, true)) {
        $error = 'Data perubahan status tidak valid.';
    } else {
        try {
            $stmt = $pdo->prepare(
                "UPDATE pengaduan
                 SET status = :status
                 WHERE id = :id"
            );

            $stmt->execute([
                'status' => $status,
                'id' => $id
            ]);

            $success = 'Status pengaduan berhasil diperbarui.';
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $error = 'Status gagal diperbarui.';
        }
    }
}

// Ambil semua pengaduan dan nama murid
$pengaduan = [];

try {
    $stmt = $pdo->query(
        "SELECT p.*, u.nama AS nama_murid, u.username
         FROM pengaduan p
         LEFT JOIN users u ON p.user_id = u.id
         ORDER BY p.id DESC"
    );

    $pengaduan = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log($e->getMessage());
    $error = 'Data pengaduan gagal dimuat.';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Riwayat Pengaduan Admin</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            color: #1e293b;
        }

        header {
            background: #1d4ed8;
            color: white;
            padding: 18px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        header h2 {
            margin: 0;
            font-size: 21px;
        }

        .container {
            max-width: 1400px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .panel {
            background: white;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.06);
        }

        .button {
            display: inline-block;
            padding: 10px 14px;
            margin-bottom: 18px;
            border-radius: 6px;
            background: #2563eb;
            color: white;
            text-decoration: none;
        }

        .button:hover {
            background: #1d4ed8;
        }

        .table-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            min-width: 1050px;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid #e2e8f0;
            text-align: left;
            vertical-align: top;
        }

        th {
            background: #eff6ff;
            color: #1d4ed8;
        }

        select {
            padding: 8px;
            border: 1px solid #cbd5e1;
            border-radius: 5px;
            background: white;
        }

        .update-button {
            display: inline-block;
            margin-top: 7px;
            padding: 8px 10px;
            background: #2563eb;
            color: white;
            border: 0;
            border-radius: 5px;
            cursor: pointer;
        }

        .update-button:hover {
            background: #1d4ed8;
        }

        .status {
            display: inline-block;
            padding: 5px 9px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .menunggu {
            background: #fef3c7;
            color: #92400e;
        }

        .diproses {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .selesai {
            background: #dcfce7;
            color: #166534;
        }

        .ditolak {
            background: #fee2e2;
            color: #991b1b;
        }

        .message {
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 16px;
        }

        .success {
            background: #dcfce7;
            color: #166534;
        }

        .error {
            background: #fee2e2;
            color: #991b1b;
        }

        .empty {
            padding: 25px;
            text-align: center;
            color: #64748b;
        }

        footer {
            text-align: center;
            padding: 25px;
            color: #64748b;
            font-size: 14px;
        }
    </style>
</head>

<body>

<header>
    <h2>Pengaduan Sarana Sekolah - Admin</h2>
    <span>Riwayat Semua Pengaduan</span>
</header>

<main class="container">
    <section class="panel">

        <a href="index.php" class="button">
            &larr; Kembali ke Dashboard
        </a>

        <h2>Riwayat Pengaduan Murid</h2>

        <?php if ($success !== ''): ?>
            <div class="message success">
                <?= e($success) ?>
            </div>
        <?php endif; ?>

        <?php if ($error !== ''): ?>
            <div class="message error">
                <?= e($error) ?>
            </div>
        <?php endif; ?>

        <?php if (empty($pengaduan)): ?>
            <div class="empty">
                Belum ada pengaduan dari murid.
            </div>
        <?php else: ?>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Pelapor</th>
                            <th>Tanggal</th>
                            <th>Judul</th>
                            <th>Kategori</th>
                            <th>Lokasi</th>
                            <th>Deskripsi</th>
                            <th>Status</th>
                            <th>Ubah Status</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php foreach ($pengaduan as $i => $item): ?>
                        <?php
                        $status = strtolower(
                            trim($item['status'] ?? 'menunggu')
                        );

                        $kelas_status = in_array(
                            $status,
                            ['menunggu', 'diproses', 'selesai', 'ditolak'],
                            true
                        ) ? $status : '';

                        $tanggal = $item['created_at']
                            ?? $item['tanggal']
                            ?? null;
                        ?>

                        <tr>
                            <td><?= $i + 1 ?></td>

                            <td>
                                <?= e($item['nama_murid'] ?? 'Pengguna tidak ditemukan') ?>
                                <br>
                                <small>
                                    <?= e($item['username'] ?? '') ?>
                                </small>
                            </td>

                            <td>
                                <?= $tanggal
                                    ? e(date('d-m-Y H:i', strtotime($tanggal)))
                                    : '-' ?>
                            </td>

                            <td>
                                <?= e($item['judul'] ?? '-') ?>
                            </td>

                            <td>
                                <?= e($item['kategori'] ?? '-') ?>
                            </td>

                            <td>
                                <?= e($item['lokasi'] ?? '-') ?>
                            </td>

                            <td>
                                <?= nl2br(e($item['deskripsi'] ?? '-')) ?>
                            </td>

                            <td>
                                <span class="status <?= e($kelas_status) ?>">
                                    <?= e(ucfirst($status)) ?>
                                </span>
                            </td>

                            <td>
                                <form method="post">
                                    <input
                                        type="hidden"
                                        name="csrf_token"
                                        value="<?= e(csrf_token()) ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= (int) $item['id'] ?>"
                                    >

                                    <select name="status" required>
                                        <?php foreach ([
                                            'menunggu' => 'Menunggu',
                                            'diproses' => 'Diproses',
                                            'selesai' => 'Selesai',
                                            'ditolak' => 'Ditolak'
                                        ] as $value => $label): ?>
                                            <option
                                                value="<?= e($value) ?>"
                                                <?= $status === $value ? 'selected' : '' ?>
                                            >
                                                <?= e($label) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>

                                    <br>

                                    <button
                                        type="submit"
                                        class="update-button"
                                    >
                                        Simpan
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php endif; ?>
    </section>
</main>

<footer>
    &copy; <?= date('Y') ?> Pengaduan Sarana Sekolah
</footer>

</body>
</html>
