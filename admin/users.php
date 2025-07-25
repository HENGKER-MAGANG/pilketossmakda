<?php
session_start();
require '../config/db.php';
require '../vendor/autoload.php'; // PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\IOFactory;

if (!isset($_SESSION['admin'])) {
  header("Location: ../auth/login.php");
  exit;
}

$success = $error = '';
$kelasList = $pdo->query("SELECT * FROM kelas ORDER BY nama")->fetchAll();

// Tambah manual
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nis'])) {
  $nis = $_POST['nis'];
  $nama = $_POST['nama'];
  $kelas_id = $_POST['kelas_id'];
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

  try {
    $stmt = $pdo->prepare("INSERT INTO users (nis, nama, kelas_id, password) VALUES (?, ?, ?, ?)");
    $stmt->execute([$nis, $nama, $kelas_id, $password]);
    $success = "Siswa berhasil ditambahkan.";
  } catch (PDOException $e) {
    $error = "Gagal menambahkan siswa.";
  }
}

// Import Excel
if (isset($_POST['import']) && isset($_FILES['file_excel'])) {
  $kelas_id = $_POST['kelas_id'];
  $file = $_FILES['file_excel']['tmp_name'];

  try {
    $spreadsheet = IOFactory::load($file);
    $sheet = $spreadsheet->getActiveSheet();
    $data = $sheet->toArray();

    $pdo->beginTransaction();
    foreach ($data as $i => $row) {
      if ($i === 0) continue;
      $nis = $row[0];
      $nama = $row[1];
      $password = password_hash($row[2], PASSWORD_DEFAULT);
      $stmt = $pdo->prepare("INSERT INTO users (nis, nama, kelas_id, password) VALUES (?, ?, ?, ?)");
      $stmt->execute([$nis, $nama, $kelas_id, $password]);
    }
    $pdo->commit();
    $success = "Import siswa berhasil.";
  } catch (Exception $e) {
    $pdo->rollBack();
    $error = "Gagal import: " . $e->getMessage();
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Input Siswa - Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: '#1e3a8a',
          }
        }
      }
    }
  </script>
</head>
<body class="bg-gray-100 min-h-screen flex">

  <?php include '../partials/sidebar_admin.php'; ?>

  <!-- Konten Utama -->
  <main class="flex-1 ml-64 p-8">
    <div class="flex items-center mb-6">
      <i class="bi bi-person-plus text-primary text-2xl mr-2"></i>
      <h1 class="text-2xl font-bold text-primary">Manajemen Siswa</h1>
    </div>

    <?php if ($success): ?>
      <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded mb-4"><?= $success ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
      <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded mb-4"><?= $error ?></div>
    <?php endif; ?>

    <div class="grid md:grid-cols-2 gap-8">
      <!-- Tambah Manual -->
      <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex items-center mb-4">
          <i class="bi bi-pencil-square text-blue-600 text-xl mr-2"></i>
          <h2 class="text-lg font-semibold text-gray-700">Tambah Siswa Manual</h2>
        </div>
        <form method="POST" class="grid gap-4">
          <!-- NIS -->
          <div class="relative">
            <i class="bi bi-credit-card-2-front-fill absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="text" name="nis" placeholder="NIS" required
              class="pl-10 pr-3 py-2 w-full border rounded focus:outline-none focus:ring focus:ring-blue-300">
          </div>

          <!-- Nama -->
          <div class="relative">
            <i class="bi bi-person-fill absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="text" name="nama" placeholder="Nama Lengkap" required
              class="pl-10 pr-3 py-2 w-full border rounded focus:outline-none focus:ring focus:ring-blue-300">
          </div>

          <!-- Kelas -->
          <div class="relative">
            <i class="bi bi-building-fill absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <select name="kelas_id" required
              class="pl-10 pr-3 py-2 w-full border rounded bg-white text-gray-700 focus:outline-none focus:ring focus:ring-blue-300">
              <option value="">-- Pilih Kelas --</option>
              <?php foreach ($kelasList as $k): ?>
                <option value="<?= $k['id'] ?>"><?= $k['nama'] ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Password -->
          <div class="relative">
            <i class="bi bi-lock-fill absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="password" name="password" placeholder="Password" required
              class="pl-10 pr-3 py-2 w-full border rounded focus:outline-none focus:ring focus:ring-blue-300">
          </div>

          <!-- Tombol -->
          <button type="submit"
            class="bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition flex items-center justify-center gap-2">
            <i class="bi bi-plus-circle-fill"></i> Tambah
          </button>
        </form>
      </div>

      <!-- Import Excel -->
      <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex items-center mb-4">
          <i class="bi bi-file-earmark-spreadsheet text-green-600 text-xl mr-2"></i>
          <h2 class="text-lg font-semibold text-gray-700">Import Siswa dari Excel</h2>
        </div>
        <form method="POST" enctype="multipart/form-data" class="grid gap-4">
          <!-- Pilih Kelas -->
          <div class="relative">
            <i class="bi bi-diagram-3-fill absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <select name="kelas_id" required
              class="pl-10 pr-3 py-2 w-full border rounded bg-white text-gray-700 focus:outline-none focus:ring focus:ring-green-300">
              <option value="">-- Pilih Kelas Untuk Semua Siswa --</option>
              <?php foreach ($kelasList as $k): ?>
                <option value="<?= $k['id'] ?>"><?= $k['nama'] ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Upload File -->
          <div class="relative">
            <i class="bi bi-upload absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="file" name="file_excel" accept=".xls,.xlsx" required
              class="pl-10 pr-3 py-2 w-full border rounded bg-white focus:outline-none focus:ring focus:ring-green-300">
          </div>

          <!-- Tombol -->
          <button type="submit" name="import"
            class="bg-green-600 text-white py-2 rounded hover:bg-green-700 transition flex items-center justify-center gap-2">
            <i class="bi bi-file-earmark-arrow-up-fill"></i> Import Excel
          </button>

          <a href="../assets/template_import_siswa.xlsx" class="text-sm text-blue-600 hover:underline flex items-center gap-1">
            <i class="bi bi-download"></i> Unduh Template Excel
          </a>
        </form>
      </div>
    </div>
  </main>
</body>
</html>
