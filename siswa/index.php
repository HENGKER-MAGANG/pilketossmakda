<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: ../auth/login.php");
  exit;
}

$user_id = $_SESSION['user_id'];
$nama = $_SESSION['nama'];
$kelas = $_SESSION['kelas'];
$sudah_memilih = $_SESSION['sudah_memilih'];

$osis = $pdo->query("SELECT * FROM candidates WHERE jenis = 'osis'")->fetchAll();
$mpk  = $pdo->query("SELECT * FROM candidates WHERE jenis = 'mpk'")->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Voting Pilketos - SMKN 2 Pinrang</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    input[type="radio"] {
      transform: scale(1.2);
    }
  </style>
</head>
<body class="bg-blue-50 min-h-screen flex flex-col justify-between">

  <main class="flex-grow p-4 md:p-8">
    <div class="max-w-6xl mx-auto bg-white p-6 md:p-10 rounded-lg shadow-lg">
      <!-- Header -->
      <div class="text-center mb-8">
        <img src="../assets/logo-smk2.png" alt="Logo Sekolah" class="w-14 h-14 mx-auto mb-2">
        <h1 class="text-2xl md:text-3xl font-bold text-blue-800">E-Voting Pilketos</h1>
        <p class="text-sm text-gray-600 mb-2">SMKN 2 Pinrang</p>
        <h2 class="text-lg font-medium text-green-600">Selamat datang, <?= htmlspecialchars($nama); ?> (<?= $kelas; ?>)</h2>
        <p class="text-sm text-gray-600">Silakan pilih Ketua OSIS dan MPK pilihanmu</p>
      </div>

      <?php if ($sudah_memilih): ?>
        <div class="bg-green-100 border border-green-300 p-4 rounded text-center text-green-700 font-semibold shadow-sm">
          <i class="bi bi-check-circle-fill mr-2"></i>
          Kamu sudah melakukan voting. Terima kasih atas partisipasimu!
        </div>
      <?php else: ?>
        <form method="POST" action="proses_voting.php">
          <!-- OSIS -->
          <h2 class="text-xl font-semibold text-blue-700 mb-4">Pilih Ketua OSIS</h2>
          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 mb-10">
            <?php foreach ($osis as $o): ?>
              <label class="border-2 border-transparent hover:border-blue-400 p-4 rounded-xl shadow hover:shadow-md transition-all cursor-pointer flex flex-col items-center text-center bg-white">
                <input type="radio" name="osis_id" value="<?= $o['id']; ?>" required class="mb-3">
                <img src="../assets/img/kandidat/<?= $o['foto']; ?>" alt="<?= $o['nama']; ?>" class="w-full h-40 object-cover rounded-lg mb-3">
                <h3 class="font-bold text-blue-800 text-lg"><?= $o['nama']; ?></h3>
                <p class="text-sm text-gray-600 mb-1"><?= $o['kelas']; ?></p>
                <p class="text-sm text-gray-700"><strong>Visi:</strong> <?= $o['visi']; ?></p>
                <p class="text-sm text-gray-700 mt-1"><strong>Misi:</strong> <?= $o['misi']; ?></p>
              </label>
            <?php endforeach; ?>
          </div>

          <!-- MPK -->
          <h2 class="text-xl font-semibold text-green-700 mb-4">Pilih Ketua MPK</h2>
          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 mb-10">
            <?php foreach ($mpk as $m): ?>
              <label class="border-2 border-transparent hover:border-green-400 p-4 rounded-xl shadow hover:shadow-md transition-all cursor-pointer flex flex-col items-center text-center bg-white">
                <input type="radio" name="mpk_id" value="<?= $m['id']; ?>" required class="mb-3">
                <img src="../assets/img/kandidat/<?= $m['foto']; ?>" alt="<?= $m['nama']; ?>" class="w-full h-40 object-cover rounded-lg mb-3">
                <h3 class="font-bold text-green-800 text-lg"><?= $m['nama']; ?></h3>
                <p class="text-sm text-gray-600 mb-1"><?= $m['kelas']; ?></p>
                <p class="text-sm text-gray-700"><strong>Visi:</strong> <?= $m['visi']; ?></p>
                <p class="text-sm text-gray-700 mt-1"><strong>Misi:</strong> <?= $m['misi']; ?></p>
              </label>
            <?php endforeach; ?>
          </div>

          <button type="submit" class="w-full bg-green-600 text-white font-semibold py-3 rounded-lg hover:bg-green-700 transition duration-300">
            <i class="bi bi-send-fill mr-2"></i> Kirim Suara
          </button>
        </form>
      <?php endif; ?>
    </div>
  </main>

  <!-- Footer -->
  <footer class="text-center text-sm text-gray-500 py-4 mt-6">
    &copy; <?= date('Y'); ?> COM SMKN2PINRANG - E-Voting Pilketos. All rights reserved.
  </footer>

</body>
</html>
