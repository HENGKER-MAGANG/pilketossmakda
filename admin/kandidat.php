<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['admin'])) {
  header("Location: ../auth/login.php");
  exit;
}

$success = $error = "";

// Proses tambah kandidat
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nama = $_POST['nama'];
  $kelas = $_POST['kelas'];
  $jenis = $_POST['jenis'];
  $visi = $_POST['visi'];
  $misi = $_POST['misi'];

  $foto_name = basename($_FILES['foto']['name']);
  $foto_tmp = $_FILES['foto']['tmp_name'];
  $foto_dest = '../assets/img/kandidat/' . $foto_name;

  if (move_uploaded_file($foto_tmp, $foto_dest)) {
    $stmt = $pdo->prepare("INSERT INTO candidates (nama, kelas, jenis, visi, misi, foto) VALUES (?, ?, ?, ?, ?, ?)");
    try {
      $stmt->execute([$nama, $kelas, $jenis, $visi, $misi, $foto_name]);
      $success = "Kandidat berhasil ditambahkan.";
    } catch (PDOException $e) {
      $error = "Gagal menyimpan kandidat.";
    }
  } else {
    $error = "Upload foto gagal.";
  }
}

$candidates = $pdo->query("SELECT * FROM candidates ORDER BY jenis, nama")->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Manajemen Kandidat - Pilketos</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen flex">

  <?php include '../partials/sidebar_admin.php'; ?>

  <main class="flex-1 ml-64 p-8">
    <div class="flex items-center mb-6">
      <i class="bi bi-person-lines-fill text-blue-700 text-xl mr-2"></i>
      <h1 class="text-2xl font-bold text-blue-800">Manajemen Kandidat</h1>
    </div>

    <?php if ($success): ?>
      <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded mb-4"><?= $success ?></div>
    <?php elseif ($error): ?>
      <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded mb-4"><?= $error ?></div>
    <?php endif; ?>

    <!-- Form Tambah Kandidat -->
      <form method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded shadow mb-8 grid grid-cols-1 md:grid-cols-2 gap-4">

        <!-- Nama -->
        <div class="relative">
          <i class="bi bi-person-fill absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
          <input type="text" name="nama" placeholder="Nama Kandidat" required
            class="pl-10 pr-3 py-2 w-full border rounded focus:outline-none focus:ring focus:ring-blue-300">
        </div>

        <!-- Kelas -->
        <div class="relative">
          <i class="bi bi-building-fill absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
          <input type="text" name="kelas" placeholder="Kelas" required
            class="pl-10 pr-3 py-2 w-full border rounded focus:outline-none focus:ring focus:ring-blue-300">
        </div>

        <!-- Jenis -->
        <div class="relative">
          <i class="bi bi-diagram-3-fill absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
          <select name="jenis" required
            class="appearance-none pl-10 pr-10 py-2 w-full border rounded bg-white text-gray-700 focus:outline-none focus:ring focus:ring-blue-300">
            <option value="">-- Pilih Jenis --</option>
            <option value="osis">Ketua OSIS</option>
            <option value="mpk">Ketua MPK</option>
          </select>
          <i class="bi bi-caret-down-fill absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 pointer-events-none"></i>
        </div>

        <!-- Foto -->
        <div class="relative">
          <i class="bi bi-image-fill absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
          <input type="file" name="foto" accept="image/*" required
            class="pl-10 pr-3 py-2 w-full border rounded bg-white focus:outline-none focus:ring focus:ring-blue-300">
        </div>

        <!-- Visi -->
        <div class="relative md:col-span-2">
          <i class="bi bi-eye-fill absolute left-3 top-3 text-gray-400"></i>
          <textarea name="visi" placeholder="Visi Kandidat" required
            class="pl-10 pr-3 pt-3 pb-2 w-full border rounded focus:outline-none focus:ring focus:ring-blue-300"></textarea>
        </div>

        <!-- Misi -->
        <div class="relative md:col-span-2">
          <i class="bi bi-list-check absolute left-3 top-3 text-gray-400"></i>
          <textarea name="misi" placeholder="Misi Kandidat" required
            class="pl-10 pr-3 pt-3 pb-2 w-full border rounded focus:outline-none focus:ring focus:ring-blue-300"></textarea>
        </div>

        <!-- Tombol -->
        <button type="submit"
          class="md:col-span-2 bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition flex items-center justify-center gap-2">
          <i class="bi bi-person-plus-fill"></i> Tambah Kandidat
        </button>
      </form>


    <!-- Daftar Kandidat -->
    <div class="bg-white p-6 rounded shadow">
      <h2 class="text-lg font-semibold text-gray-700 mb-4"><i class="bi bi-list-ul mr-2 text-primary"></i>Daftar Kandidat</h2>

      <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($candidates as $c): ?>
          <div class="bg-gray-50 border border-gray-200 rounded shadow-sm p-4 hover:shadow-md transition">
            <img src="../assets/img/kandidat/<?= $c['foto'] ?>" alt="<?= $c['nama'] ?>" class="w-full h-40 object-cover rounded mb-3">
            <h3 class="text-lg font-bold text-gray-800"><?= htmlspecialchars($c['nama']) ?> <span class="text-sm text-gray-500">(<?= htmlspecialchars($c['kelas']) ?>)</span></h3>
            <p class="text-sm text-blue-600 font-semibold uppercase mt-1"><i class="bi bi-award-fill mr-1"></i><?= $c['jenis'] ?></p>
            <div class="mt-2 text-sm text-gray-700"><strong>Visi:</strong> <?= htmlspecialchars($c['visi']) ?></div>
            <div class="text-sm text-gray-700"><strong>Misi:</strong> <?= htmlspecialchars($c['misi']) ?></div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </main>
</body>
</html>
