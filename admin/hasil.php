<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['admin'])) {
  header('Location: ../auth/login.php');
  exit;
}

$kelas_filter = $_GET['kelas'] ?? '';
$kelasList = $pdo->query("SELECT DISTINCT nama FROM kelas ORDER BY nama")->fetchAll(PDO::FETCH_COLUMN);

// OSIS
$queryOsis = "SELECT c.nama, COUNT(v.id) as total
              FROM candidates c
              LEFT JOIN votes v ON c.id = v.osis_id
              LEFT JOIN users u ON v.user_id = u.id
              WHERE c.jenis = 'osis'";
$queryOsis .= $kelas_filter ? " AND u.kelas = ?" : "";
$queryOsis .= " GROUP BY c.id";

$stmtOsis = $pdo->prepare($queryOsis);
$stmtOsis->execute($kelas_filter ? [$kelas_filter] : []);
$osisVotes = $stmtOsis->fetchAll();

// MPK
$queryMpk = "SELECT c.nama, COUNT(v.id) as total
             FROM candidates c
             LEFT JOIN votes v ON c.id = v.mpk_id
             LEFT JOIN users u ON v.user_id = u.id
             WHERE c.jenis = 'mpk'";
$queryMpk .= $kelas_filter ? " AND u.kelas = ?" : "";
$queryMpk .= " GROUP BY c.id";

$stmtMpk = $pdo->prepare($queryMpk);
$stmtMpk->execute($kelas_filter ? [$kelas_filter] : []);
$mpkVotes = $stmtMpk->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Hasil Voting</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="flex bg-gray-100 min-h-screen">

  <?php include '../partials/sidebar_admin.php'; ?>

  <main class="flex-1 p-6 md:ml-64">
    <!-- Judul -->
    <h2 class="text-2xl font-bold text-blue-800 mb-6 flex items-center gap-2">
      <i class="bi bi-bar-chart-fill text-blue-700 text-2xl"></i>
      Statistik Hasil Voting Pilketos
    </h2>

    <!-- Filter Kelas -->
    <form method="GET" class="mb-6 flex items-center gap-3">
      <label class="text-sm font-medium text-gray-700 flex items-center gap-2">
        <i class="bi bi-funnel-fill text-blue-500"></i> Filter Kelas:
      </label>
      <div class="relative">
        <select name="kelas" onchange="this.form.submit()" class="border pl-10 pr-8 py-2 rounded shadow-sm text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
          <option value="">Semua Kelas</option>
          <?php foreach ($kelasList as $kelas): ?>
            <option value="<?= $kelas ?>" <?= $kelas === $kelas_filter ? 'selected' : '' ?>>
              <?= htmlspecialchars($kelas) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <i class="bi bi-caret-down-fill absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 pointer-events-none"></i>
        <i class="bi bi-building-fill absolute left-3 top-1/2 -translate-y-1/2 text-blue-400"></i>
      </div>
    </form>

    <!-- Hasil Voting List -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
      <!-- Voting OSIS -->
      <div class="bg-white rounded-lg p-5 shadow border-t-4 border-blue-500">
        <h3 class="text-xl font-bold text-blue-700 mb-4 flex items-center gap-2">
          <i class="bi bi-person-badge-fill"></i> Voting OSIS
        </h3>
        <ul class="space-y-3 text-sm">
          <?php foreach ($osisVotes as $row): ?>
            <li class="flex justify-between items-center border-b pb-2 text-gray-700">
              <span><i class="bi bi-person-fill text-blue-400 mr-2"></i><?= htmlspecialchars($row['nama']) ?></span>
              <strong class="text-blue-700"><?= $row['total'] ?> suara</strong>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>

      <!-- Voting MPK -->
      <div class="bg-white rounded-lg p-5 shadow border-t-4 border-green-500">
        <h3 class="text-xl font-bold text-green-700 mb-4 flex items-center gap-2">
          <i class="bi bi-people-fill"></i> Voting MPK
        </h3>
        <ul class="space-y-3 text-sm">
          <?php foreach ($mpkVotes as $row): ?>
            <li class="flex justify-between items-center border-b pb-2 text-gray-700">
              <span><i class="bi bi-person-fill text-green-400 mr-2"></i><?= htmlspecialchars($row['nama']) ?></span>
              <strong class="text-green-700"><?= $row['total'] ?> suara</strong>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>

    <!-- Grafik -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div class="bg-white p-5 rounded-lg shadow">
        <h4 class="text-lg font-semibold text-blue-600 mb-3 flex items-center gap-2">
          <i class="bi bi-graph-up-arrow"></i> Grafik OSIS
        </h4>
        <canvas id="osisChart"></canvas>
      </div>
      <div class="bg-white p-5 rounded-lg shadow">
        <h4 class="text-lg font-semibold text-green-600 mb-3 flex items-center gap-2">
          <i class="bi bi-graph-up"></i> Grafik MPK
        </h4>
        <canvas id="mpkChart"></canvas>
      </div>
    </div>

    <script>
      const osisChart = new Chart(document.getElementById('osisChart'), {
        type: 'bar',
        data: {
          labels: <?= json_encode(array_column($osisVotes, 'nama')) ?>,
          datasets: [{
            label: 'Suara OSIS',
            data: <?= json_encode(array_column($osisVotes, 'total')) ?>,
            backgroundColor: 'rgba(59, 130, 246, 0.7)',
            borderRadius: 5
          }]
        },
        options: {
          plugins: { legend: { display: false }},
          scales: { y: { beginAtZero: true } }
        }
      });

      const mpkChart = new Chart(document.getElementById('mpkChart'), {
        type: 'bar',
        data: {
          labels: <?= json_encode(array_column($mpkVotes, 'nama')) ?>,
          datasets: [{
            label: 'Suara MPK',
            data: <?= json_encode(array_column($mpkVotes, 'total')) ?>,
            backgroundColor: 'rgba(34, 197, 94, 0.7)',
            borderRadius: 5
          }]
        },
        options: {
          plugins: { legend: { display: false }},
          scales: { y: { beginAtZero: true } }
        }
      });
    </script>
  </main>
</body>
</html>
