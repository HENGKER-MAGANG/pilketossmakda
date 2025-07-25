<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['admin'])) {
  header("Location: ../auth/login.php");
  exit;
}

$kelas_filter = $_GET['kelas'] ?? '';

$total = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$sudah = $pdo->query("SELECT COUNT(*) FROM users WHERE sudah_memilih = 1")->fetchColumn();
$belum = $total - $sudah;

$kelasList = $pdo->query("SELECT id, nama FROM kelas ORDER BY nama")->fetchAll(PDO::FETCH_ASSOC);

// OSIS
$queryOsis = "SELECT c.nama, COUNT(v.id) as total 
              FROM candidates c 
              LEFT JOIN votes v ON c.id = v.osis_id 
              LEFT JOIN users u ON v.user_id = u.id";
$queryOsis .= $kelas_filter ? " WHERE c.jenis='osis' AND u.kelas_id = ?" : " WHERE c.jenis='osis'";
$queryOsis .= " GROUP BY c.id";
$stmtOsis = $pdo->prepare($queryOsis);
$stmtOsis->execute($kelas_filter ? [$kelas_filter] : []);
$osisVotes = $stmtOsis->fetchAll();

// MPK
$queryMpk = "SELECT c.nama, COUNT(v.id) as total 
             FROM candidates c 
             LEFT JOIN votes v ON c.id = v.mpk_id 
             LEFT JOIN users u ON v.user_id = u.id";
$queryMpk .= $kelas_filter ? " WHERE c.jenis='mpk' AND u.kelas_id = ?" : " WHERE c.jenis='mpk'";
$queryMpk .= " GROUP BY c.id";
$stmtMpk = $pdo->prepare($queryMpk);
$stmtMpk->execute($kelas_filter ? [$kelas_filter] : []);
$mpkVotes = $stmtMpk->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Admin - Pilketos</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>
<body class="flex bg-gray-100 min-h-screen">

  <?php include '../partials/sidebar_admin.php'; ?>

  <main class="flex-1 p-6 ml-64">
    <h2 class="text-2xl font-bold text-blue-800 mb-6 flex items-center gap-2">
      <i class="bi bi-speedometer2 text-blue-700 text-2xl"></i> Dashboard Admin
    </h2>

    <!-- Statistik Voting -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
      <div class="bg-white rounded-lg p-5 shadow border-l-4 border-blue-600">
        <div class="flex items-center gap-4">
          <i class="bi bi-people-fill text-3xl text-blue-600"></i>
          <div>
            <div class="text-sm text-gray-600">Total Siswa</div>
            <div class="text-2xl font-bold text-blue-800"><?= $total ?></div>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg p-5 shadow border-l-4 border-green-600">
        <div class="flex items-center gap-4">
          <i class="bi bi-check-circle-fill text-3xl text-green-600"></i>
          <div>
            <div class="text-sm text-gray-600">Sudah Voting</div>
            <div class="text-2xl font-bold text-green-700"><?= $sudah ?></div>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg p-5 shadow border-l-4 border-red-600">
        <div class="flex items-center gap-4">
          <i class="bi bi-x-circle-fill text-3xl text-red-600"></i>
          <div>
            <div class="text-sm text-gray-600">Belum Voting</div>
            <div class="text-2xl font-bold text-red-700"><?= $belum ?></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Filter Kelas -->
    <form method="GET" class="mb-6">
      <label class="text-sm font-medium text-gray-700 mr-2">Filter Kelas:</label>
      <select name="kelas" onchange="this.form.submit()" class="border px-3 py-2 rounded shadow-sm text-sm">
        <option value="">Semua Kelas</option>
        <?php foreach ($kelasList as $kelas): ?>
          <option value="<?= $kelas['nama'] ?>" <?= $kelas['nama'] === $kelas_filter ? 'selected' : '' ?>>
            <?= htmlspecialchars($kelas['nama']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </form>

    <!-- Hasil Voting -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
      <div class="bg-white rounded-lg p-5 shadow border-t-4 border-blue-400">
        <h3 class="text-xl font-bold text-blue-700 mb-4 flex items-center gap-2">
          <i class="bi bi-bar-chart-fill text-blue-500"></i> Hasil Voting OSIS
        </h3>
        <ul class="space-y-2">
          <?php foreach ($osisVotes as $row): ?>
            <li class="flex justify-between border-b pb-1">
              <span><?= htmlspecialchars($row['nama']) ?></span>
              <strong><?= $row['total'] ?> suara</strong>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>

      <div class="bg-white rounded-lg p-5 shadow border-t-4 border-green-400">
        <h3 class="text-xl font-bold text-green-700 mb-4 flex items-center gap-2">
          <i class="bi bi-bar-chart-fill text-green-500"></i> Hasil Voting MPK
        </h3>
        <ul class="space-y-2">
          <?php foreach ($mpkVotes as $row): ?>
            <li class="flex justify-between border-b pb-1">
              <span><?= htmlspecialchars($row['nama']) ?></span>
              <strong><?= $row['total'] ?> suara</strong>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>

    <!-- Chart Section -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div class="bg-white rounded-lg p-5 shadow">
        <h4 class="text-lg font-semibold mb-4 text-blue-700 flex items-center gap-2">
          <i class="bi bi-graph-up text-blue-500"></i> Grafik OSIS
        </h4>
        <canvas id="osisChart"></canvas>
      </div>

      <div class="bg-white rounded-lg p-5 shadow">
        <h4 class="text-lg font-semibold mb-4 text-green-700 flex items-center gap-2">
          <i class="bi bi-graph-up text-green-500"></i> Grafik MPK
        </h4>
        <canvas id="mpkChart"></canvas>
      </div>
    </div>
  </main>

  <!-- Chart.js Script -->
  <script>
    const osisChart = new Chart(document.getElementById('osisChart'), {
      type: 'bar',
      data: {
        labels: <?= json_encode(array_column($osisVotes, 'nama')) ?>,
        datasets: [{
          label: 'Jumlah Suara',
          data: <?= json_encode(array_column($osisVotes, 'total')) ?>,
          backgroundColor: 'rgba(59, 130, 246, 0.7)'
        }]
      },
      options: {
        scales: { y: { beginAtZero: true } }
      }
    });

    const mpkChart = new Chart(document.getElementById('mpkChart'), {
      type: 'bar',
      data: {
        labels: <?= json_encode(array_column($mpkVotes, 'nama')) ?>,
        datasets: [{
          label: 'Jumlah Suara',
          data: <?= json_encode(array_column($mpkVotes, 'total')) ?>,
          backgroundColor: 'rgba(34, 197, 94, 0.7)'
        }]
      },
      options: {
        scales: { y: { beginAtZero: true } }
      }
    });
  </script>
</body>
</html>
    