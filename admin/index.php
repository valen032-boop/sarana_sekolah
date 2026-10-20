
<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Pastikan hanya admin yang dapat membuka halaman ini
if (!is_logged_in() || $_SESSION['role'] !== 'admin') {
    redirect('../login.php');
}

try {
    // Menghitung seluruh pengaduan
    $total = (int) $pdo->query(
        "SELECT COUNT(*) FROM complaints"
    )->fetchColumn();

    // Menghitung pengaduan berdasarkan status_id
    $menunggu = (int) $pdo->query(
        "SELECT COUNT(*) FROM complaints WHERE status_id = 1"
    )->fetchColumn();

    $diproses = (int) $pdo->query(
        "SELECT COUNT(*) FROM complaints WHERE status_id = 2"
    )->fetchColumn();

    $selesai = (int) $pdo->query(
        "SELECT COUNT(*) FROM complaints WHERE status_id = 3"
    )->fetchColumn();

    $ditolak = (int) $pdo->query(
        "SELECT COUNT(*) FROM complaints WHERE status_id = 4"
    )->fetchColumn();

} catch (PDOException $e) {
    error_log($e->getMessage());
    exit('Gagal mengambil data pengaduan. Periksa struktur database.');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Dashboard Admin - Pengaduan Sarana Sekolah</title>

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
            padding: 20px 30px;
        }

        header h1 {
            margin: 0 0 8px;
            font-size: 24px;
        }

        header p {
            margin: 0;
        }

        nav {
            background: white;
            padding: 15px 30px;
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        nav a {
            text-decoration: none;
            color: #1d4ed8;
            font-weight: bold;
        }

        main {
            max-width: 1100px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 20px;
        }

        .card {
            background: white;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
        }

        .card h3 {
            margin: 0 0 15px;
            font-size: 16px;
            color: #475569;
        }

        .number {
            font-size: 32px;
            font-weight: bold;
            margin: 0;
        }

        .blue {
            color: #2563eb;
        }

        .orange {
            color: #d97706;
        }

        .purple {
            color: #7c3aed;
        }

        .green {
            color: #16a34a;
        }

        .red {
            color: #dc2626;
        }

        .welcome {
            margin-bottom: 25px;
        }

        @media (max-width: 700px) {
            .cards {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            header, nav {
                padding-left: 20px;
                padding-right: 20px;
            }
        }

        @media (max-width: 450px) {
            .cards {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

<header>
    <h1>Pengaduan Sarana Sekolah</h1>
    <p>Dashboard Administrator</p>
</header>

<nav>
    <a href="index.php">Dashboard</a>
    <a href="complaints.php">Data Pengaduan</a>
    <a href="../logout.php">Logout</a>
</nav>

<main>

    <section class="welcome">
        <h2>Selamat datang, <?= e($_SESSION['nama']) ?>!</h2>
        <p>Berikut ringkasan pengaduan sarana sekolah.</p>
    </section>

    <section class="cards">

        <div class="card">
            <h3>Total Pengaduan</h3>
            <p class="number blue"><?= $total ?></p>
        </div>

        <div class="card">
            <h3>Menunggu</h3>
            <p class="number orange"><?= $menunggu ?></p>
        </div>

        <div class="card">
            <h3>Diproses</h3>
            <p class="number purple"><?= $diproses ?></p>
        </div>

        <div class="card">
            <h3>Selesai</h3>
            <p class="number green"><?= $selesai ?></p>
        </div>

        <div class="card">
            <h3>Ditolak</h3>
            <p class="number red"><?= $ditolak ?></p>
        </div>

    </section>

</main>

</body>
</html>
