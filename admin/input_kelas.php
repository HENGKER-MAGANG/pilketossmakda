<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['admin'])) {
  header('Location: ../auth/login.php');
  exit;
}

$success = $error = '';

// Hapus kelas dan siswa terkait
if (isset($_POST['hapus_kelas']) && isset($_POST['id_kelas'])) {
  $id_kelas = (int)$_POST['id_kelas'];
  try {
    // Hapus semua siswa dalam kelas ini terlebih dahulu
    $stmt = $pdo->prepare("DELETE FROM users WHERE kelas_id = ?");
    $stmt->execute([$id_kelas]);

    // Lalu hapus kelasnya
    $stmt = $pdo->prepare("DELETE FROM kelas WHERE id = ?");
    $stmt->execute([$id_kelas]);

    $success = "Kelas dan semua siswa di dalamnya berhasil dihapus.";
  } catch (PDOException $e) {
    $error = "Gagal menghapus kelas dan siswa: " . $e->getMessage();
  }
}

// Tambah kelas
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nama']) && !isset($_POST['hapus_kelas'])) {
  $nama = trim($_POST['nama']);

  if (!empty($nama)) {
    try {
      $stmt = $pdo->prepare("INSERT INTO kelas (nama) VALUES (?)");
      $stmt->execute([$nama]);
      $success = "Kelas berhasil ditambahkan.";
    } catch (PDOException $e) {
      $error = "Gagal menambahkan kelas.";
    }
  } else {
    $error = "Nama kelas tidak boleh kosong.";
  }
}

$kelas = $pdo->query("SELECT * FROM kelas ORDER BY nama")->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Manajemen Kelas</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>
<body class="bg-gray-100 min-h-screen flex font-sans">

  <?php include '../partials/sidebar_admin.php'; ?>

  <main class="flex-1 ml-64 p-8">
    <!-- Judul -->
    <div class="flex items-center gap-3 mb-6">
      <i class="bi bi-journal-text text-blue-700 text-2xl"></i>
      <h1 class="text-2xl font-bold text-blue-800">Manajemen Kelas</h1>
    </div>

    <!-- Notifikasi -->
    <?php if ($success): ?>
      <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md mb-4 flex items-center gap-2 shadow-sm">
        <i class="bi bi-check-circle-fill text-green-500 text-lg"></i>
        <span><?= $success ?></span>
      </div>
    <?php elseif ($error): ?>
      <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md mb-4 flex items-center gap-2 shadow-sm">
        <i class="bi bi-x-circle-fill text-red-500 text-lg"></i>
        <span><?= $error ?></span>
      </div>
    <?php endif; ?>

    <!-- Form Tambah Kelas -->
    <div class="bg-white p-6 rounded-lg shadow-md max-w-xl border border-gray-200">
      <form method="POST" class="space-y-4">
        <div>
          <label for="nama" class="block font-medium text-gray-700 mb-1 flex items-center gap-1">
            <i class="bi bi-door-open-fill text-blue-500"></i> Nama Kelas
          </label>
          <input type="text" id="nama" name="nama" placeholder="Contoh: XII-RPL 1" required
            class="w-full border border-gray-300 p-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400 text-sm">
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition flex items-center gap-2 text-sm">
          <i class="bi bi-plus-lg"></i>
          Tambah Kelas
        </button>
      </form>
    </div>

    <!-- Daftar Kelas -->
    <div class="mt-10">
      <div class="flex items-center gap-2 mb-4">
        <i class="bi bi-list-ul text-gray-600"></i>
        <h2 class="text-lg font-semibold text-gray-800">Daftar Kelas</h2>
      </div>
      <?php if (count($kelas) > 0): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <?php foreach ($kelas as $k): ?>
            <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm flex justify-between items-center hover:shadow-md transition">
              <div class="flex items-center gap-3">
                <i class="bi bi-door-closed-fill text-blue-600 text-lg"></i>
                <span class="text-gray-800 font-medium"><?= htmlspecialchars($k['nama']) ?></span>
              </div>
              <form method="POST" onsubmit="return confirm('Yakin ingin menghapus kelas ini?');">
                <input type="hidden" name="id_kelas" value="<?= $k['id'] ?>">
                <button type="submit" name="hapus_kelas" class="text-red-600 hover:text-red-800 transition text-lg" title="Hapus">
                  <i class="bi bi-trash-fill"></i>
                </button>
              </form>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <p class="text-sm text-gray-500 italic mt-4">Belum ada kelas terdaftar.</p>
      <?php endif; ?>
    </div>
  </main>
</body>
</html>
