
<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Pastikan pengguna sudah login
if (!is_logged_in()) {
    redirect('../login.php');
    exit;
}

// Pastikan role adalah murid
if ($_SESSION['role'] !== 'murid') {
    redirect(dashboard_url($_SESSION['role']));
    exit;
}

$nama = $_SESSION['nama'] ?? 'Murid';
$username = $_SESSION['username'] ?? '';

// Ambil jumlah pengaduan milik murid
$total_pengaduan = 0;

try {
    $stmt = $pdo->prepare(
        "SELECT COUNT(*)
         FROM pengaduan
         WHERE user_id = ?"
    );

    $stmt->execute([$_SESSION['user_id']]);
    $total_pengaduan = (int) $stmt->fetchColumn();
} catch (PDOException $e) {
    error_log($e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Dashboard Murid - Pengaduan Sarana Sekolah</title>

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
            background: #2563eb;
            color: white;
            padding: 18px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        header h2 {
            margin: 0;
            font-size: 21px;
        }

        .logout {
            background: #dc2626;
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 6px;
        }

        .logout:hover {
            background: #b91c1c;
        }

        .container {
            max-width: 1000px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .welcome {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.06);
            margin-bottom: 25px;
        }

        .welcome h1 {
            color: #1d4ed8;
            margin-top: 0;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
        }

        .card {
            background: white;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.06);
        }

        .card h3 {
            margin-top: 0;
            color: #475569;
        }

        .number {
            font-size: 32px;
            font-weight: bold;
            color: #2563eb;
        }

        .button {
            display: inline-block;
            margin-top: 12px;
            padding: 11px 16px;
            background: #2563eb;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }

        .button:hover {
            background: #1d4ed8;
        }

        footer {
            text-align: center;
            padding: 25px;
            color: #64748b;
            font-size: 14px;
        }

        @media (max-width: 500px) {
            header {
                padding: 16px;
            }

            header h2 {
                font-size: 18px;
            }

            .container {
                margin-top: 20px;
            }
        }
    </style>
</head>

<body>

<header>
    <h2>Pengaduan Sarana Sekolah</h2>
    <a class="logout" href="../logout.php">Logout</a>
</header>

<main class="container">

    <section class="welcome">
        <h1>Selamat Datang, <?= e($nama) ?>!</h1>

        <p>
            Username: <strong><?= e($username) ?></strong>
        </p>

        <p>
            Selamat datang di dashboard murid. Kamu dapat melaporkan
            kerusakan atau masalah sarana dan prasarana sekolah di sini.
        </p>
    </section>

    <section class="cards">

        <div class="card">
            <h3>Total Pengaduan Saya</h3>

            <div class="number">
                <?= $total_pengaduan ?>
            </div>

            <p>Jumlah pengaduan yang telah kamu buat.</p>
        </div>

        <div class="card">
            <h3>Buat Pengaduan</h3>

            <p>
                Laporkan fasilitas sekolah yang rusak atau membutuhkan
                perbaikan.
            </p>

            <a class="button" href="tambah_pengaduan.php">
                Buat Pengaduan
            </a>
        </div>

        <div class="card">
            <h3>Riwayat Pengaduan</h3>

            <p>
                Lihat pengaduan yang sudah kamu kirim dan periksa
                perkembangannya.
            </p>

            <a class="button" href="riwayat_pengaduan.php">
                Lihat Riwayat
            </a>
        </div>

    </section>

</main>

<footer>
    &copy; <?= date('Y') ?> Pengaduan Sarana Sekolah
</footer>

</body>
</html>
