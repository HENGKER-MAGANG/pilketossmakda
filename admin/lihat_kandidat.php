<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['admin'])) {
  header("Location: ../auth/login.php");
  exit;
}

// Hapus kandidat jika tombol hapus ditekan
if (isset($_GET['hapus'])) {
  $id = $_GET['hapus'];
  $stmt = $pdo->prepare("DELETE FROM candidates WHERE id = ?");
  $stmt->execute([$id]);
  header("Location: kandidat.php");
  exit;
}

// Proses update kandidat dari modal
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
  $id     = $_POST['edit_id'];
  $nama   = $_POST['edit_nama'];
  $kelas  = $_POST['edit_kelas'];
  $jenis  = $_POST['edit_jenis'];
  $visi   = $_POST['edit_visi'];
  $misi   = $_POST['edit_misi'];

  $stmt = $pdo->prepare("UPDATE candidates SET nama = ?, kelas = ?, jenis = ?, visi = ?, misi = ? WHERE id = ?");
  $stmt->execute([$nama, $kelas, $jenis, $visi, $misi, $id]);

  header("Location: kandidat.php");
  exit;
}

// Ambil semua kandidat beserta jumlah suara
$query = "
  SELECT c.*, 
    (SELECT COUNT(*) FROM votes WHERE osis_id = c.id) AS suara_osis,
    (SELECT COUNT(*) FROM votes WHERE mpk_id = c.id) AS suara_mpk
  FROM candidates c
  ORDER BY c.jenis, c.nama
";

$data = $pdo->query($query)->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Data Kandidat</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-gray-50 flex">

  <?php include '../partials/sidebar_admin.php'; ?>

  <main class="flex-1 ml-64 p-8">
    <div class="flex justify-between items-center mb-6">
      <div class="flex items-center">
        <i class="bi bi-people-fill text-blue-700 text-xl mr-2"></i>
        <h1 class="text-2xl font-bold text-blue-800">Daftar Kandidat</h1>
      </div>
      <a href="export_pdf.php" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded text-sm flex items-center gap-2">
        <i class="bi bi-file-earmark-pdf-fill"></i> Export PDF
      </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <?php foreach ($data as $row): 
        $jumlah_suara = $row['jenis'] === 'osis' ? $row['suara_osis'] : $row['suara_mpk'];
      ?>
        <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden hover:shadow-md transition relative">
          <img src="../assets/img/kandidat/<?= htmlspecialchars($row['foto']) ?>" alt="<?= $row['nama'] ?>" class="w-full h-48 object-cover">
          <div class="p-4">
            <div class="font-bold text-xl text-gray-800 mb-1"><?= htmlspecialchars($row['nama']) ?></div>
            <div class="text-sm text-gray-600 mb-2"><?= htmlspecialchars($row['kelas']) ?> | 
              <span class="uppercase text-blue-600 font-medium"><?= $row['jenis'] ?></span>
            </div>
            <div class="mb-2 text-sm text-gray-700"><strong>Visi:</strong> <?= nl2br(htmlspecialchars($row['visi'])) ?></div>
            <div class="mb-2 text-sm text-gray-700"><strong>Misi:</strong> <?= nl2br(htmlspecialchars($row['misi'])) ?></div>

            <div class="mt-3 bg-blue-100 text-blue-800 px-3 py-2 text-sm rounded flex items-center gap-2">
              <i class="bi bi-bar-chart-line-fill"></i>
              <span><strong><?= $jumlah_suara ?></strong> suara</span>
            </div>

            <div class="mt-4 flex gap-2">
              <button onclick="openModal(<?= $row['id'] ?>)" class="text-white bg-yellow-500 hover:bg-yellow-600 px-3 py-1 rounded text-sm flex items-center gap-1">
                <i class="bi bi-pencil-square"></i> Edit
              </button>
              <a href="?hapus=<?= $row['id'] ?>" onclick="return confirm('Yakin ingin menghapus kandidat ini?')" class="text-white bg-red-600 hover:bg-red-700 px-3 py-1 rounded text-sm flex items-center gap-1">
                <i class="bi bi-trash-fill"></i> Hapus
              </a>
            </div>
          </div>
        </div>

        <!-- Modal Edit Kandidat -->
<div id="modal-<?= $row['id'] ?>" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
  <div class="bg-white w-full max-w-lg rounded-xl shadow-lg p-6 border border-gray-200">
    <div class="flex items-center gap-2 mb-4">
      <i class="bi bi-pencil-square text-blue-600 text-xl"></i>
      <h2 class="text-xl font-semibold text-gray-800">Edit Data Kandidat</h2>
    </div>

    <form method="POST" class="space-y-4">
      <input type="hidden" name="edit_id" value="<?= $row['id'] ?>">

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
          <i class="bi bi-person-fill me-1 text-gray-500"></i> Nama Kandidat
        </label>
        <input type="text" name="edit_nama" value="<?= htmlspecialchars($row['nama']) ?>" required
          class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring focus:ring-blue-300">
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
          <i class="bi bi-building-fill me-1 text-gray-500"></i> Kelas
        </label>
        <input type="text" name="edit_kelas" value="<?= htmlspecialchars($row['kelas']) ?>" required
          class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring focus:ring-blue-300">
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
          <i class="bi bi-tags-fill me-1 text-gray-500"></i> Jenis Kandidat
        </label>
        <select name="edit_jenis" required
          class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring focus:ring-blue-300">
          <option value="osis" <?= $row['jenis'] === 'osis' ? 'selected' : '' ?>>OSIS</option>
          <option value="mpk" <?= $row['jenis'] === 'mpk' ? 'selected' : '' ?>>MPK</option>
        </select>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
          <i class="bi bi-eye-fill me-1 text-gray-500"></i> Visi
        </label>
        <textarea name="edit_visi" required rows="2"
          class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring focus:ring-blue-300"><?= htmlspecialchars($row['visi']) ?></textarea>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
          <i class="bi bi-bullseye me-1 text-gray-500"></i> Misi
        </label>
        <textarea name="edit_misi" required rows="3"
          class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring focus:ring-blue-300"><?= htmlspecialchars($row['misi']) ?></textarea>
      </div>

      <div class="flex justify-end gap-2 pt-4">
        <button type="button" onclick="closeModal(<?= $row['id'] ?>)"
          class="bg-gray-300 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-400 flex items-center gap-2">
          <i class="bi bi-x-circle-fill"></i> Batal
        </button>
        <button type="submit"
          class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 flex items-center gap-2">
          <i class="bi bi-save-fill"></i> Simpan Perubahan
        </button>
      </div>
    </form>
  </div>
</div>

      <?php endforeach; ?>
    </div>
  </main>

  <script>
    function openModal(id) {
      document.getElementById('modal-' + id).classList.remove('hidden');
    }
    function closeModal(id) {
      document.getElementById('modal-' + id).classList.add('hidden');
    }
  </script>
</body>
</html>
