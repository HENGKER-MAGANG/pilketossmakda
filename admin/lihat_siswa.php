<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['admin'])) {
  header("Location: ../auth/login.php");
  exit;
}

$kelasList = $pdo->query("SELECT * FROM kelas ORDER BY nama")->fetchAll();
$kelas_filter = $_GET['kelas'] ?? '';

$query = "SELECT u.nis, u.nama, k.nama as kelas, u.sudah_memilih 
          FROM users u 
          JOIN kelas k ON u.kelas_id = k.id";

if ($kelas_filter) {
  $query .= " WHERE u.kelas_id = ?";
  $stmt = $pdo->prepare($query . " ORDER BY k.nama, u.nama");
  $stmt->execute([$kelas_filter]);
  $data = $stmt->fetchAll();
} else {
  $data = $pdo->query($query . " ORDER BY k.nama, u.nama")->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Data Siswa</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen flex">

  <?php include '../partials/sidebar_admin.php'; ?>

  <main class="flex-1 ml-64 p-8">
    <div class="flex items-center mb-6">
      <i class="bi bi-people-fill text-blue-600 text-xl mr-2"></i>
      <h1 class="text-2xl font-bold text-blue-800">Daftar Siswa</h1>
    </div>

    <!-- Notifikasi -->
    <?php if (isset($_GET['success'])): ?>
      <div class="mb-4 p-3 bg-green-100 border border-green-300 text-green-800 rounded">
        Siswa berhasil dihapus.
      </div>
    <?php elseif (isset($_GET['error'])): ?>
      <div class="mb-4 p-3 bg-red-100 border border-red-300 text-red-800 rounded">
        Terjadi kesalahan saat menghapus siswa.
      </div>
    <?php endif; ?>

    <!-- Filter kelas dengan ikon -->
    <form method="GET" class="mb-6 flex items-center gap-2">
      <label for="kelas" class="font-medium text-gray-700 flex items-center">
        <i class="bi bi-filter-circle text-blue-600 mr-2"></i>Filter Kelas:
      </label>
      <div class="relative">
        <select name="kelas" id="kelas" onchange="this.form.submit()" class="appearance-none pl-10 pr-10 py-2 border rounded-md bg-white text-gray-700 focus:outline-none focus:ring focus:ring-blue-300">
          <option value="">Semua Kelas</option>
          <?php foreach ($kelasList as $k): ?>
            <option value="<?= $k['id'] ?>" <?= $kelas_filter == $k['id'] ? 'selected' : '' ?>>
              <?= $k['nama'] ?>
            </option>
          <?php endforeach; ?>
        </select>
        <i class="bi bi-caret-down-fill absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 pointer-events-none"></i>
        <i class="bi bi-diagram-3-fill absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
      </div>
    </form>

    <!-- Tabel data siswa -->
    <div class="overflow-x-auto bg-white rounded shadow">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-100 text-gray-700">
          <tr>
            <th class="px-4 py-3 text-left"><i class="bi bi-person-badge-fill mr-2"></i>NIS</th>
            <th class="px-4 py-3 text-left"><i class="bi bi-person-fill mr-2"></i>Nama</th>
            <th class="px-4 py-3 text-left"><i class="bi bi-buildings-fill mr-2"></i>Kelas</th>
            <th class="px-4 py-3 text-left"><i class="bi bi-check2-circle mr-2"></i>Status Voting</th>
            <th class="px-4 py-3 text-left"><i class="bi bi-tools mr-2"></i>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (count($data) > 0): ?>
            <?php foreach ($data as $row): ?>
              <tr class="border-t hover:bg-gray-50">
                <td class="px-4 py-2"><?= $row['nis'] ?></td>
                <td class="px-4 py-2"><?= $row['nama'] ?></td>
                <td class="px-4 py-2"><?= $row['kelas'] ?></td>
                <td class="px-4 py-2">
                  <?php if ($row['sudah_memilih']): ?>
                    <span class="text-green-600 font-semibold"><i class="bi bi-check-circle-fill mr-1"></i>Sudah</span>
                  <?php else: ?>
                    <span class="text-red-600 font-semibold"><i class="bi bi-x-circle-fill mr-1"></i>Belum</span>
                  <?php endif; ?>
                </td>
                <td class="px-4 py-2">
                  <form action="hapus_siswa.php" method="POST" onsubmit="return confirm('Yakin ingin menghapus siswa ini?');">
                    <input type="hidden" name="nis" value="<?= $row['nis'] ?>">
                    <button type="submit" class="text-red-600 hover:text-red-800 flex items-center gap-1">
                      <i class="bi bi-trash-fill"></i> Hapus
                    </button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="5" class="px-4 py-4 text-center text-gray-500">Tidak ada data siswa.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </main>
</body>
</html>
