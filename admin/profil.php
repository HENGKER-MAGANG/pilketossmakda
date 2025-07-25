<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['admin'])) {
  header("Location: ../auth/login.php");
  exit;
}

$username = $_SESSION['admin'];
$success = '';
$error = '';

// Ambil data admin saat ini
$stmt = $pdo->prepare("SELECT * FROM admin WHERE username = ?");
$stmt->execute([$username]);
$admin = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $newUsername = $_POST['username'];
  $newPassword = $_POST['password'];
  $confirmPassword = $_POST['confirm_password'];

  if ($newPassword !== $confirmPassword) {
    $error = "Password dan konfirmasi tidak cocok.";
  } else {
    $query = "UPDATE admin SET username = ?";
    $params = [$newUsername];

    if (!empty($newPassword)) {
      $query .= ", password = MD5(?)";
      $params[] = $newPassword;
    }

    $query .= " WHERE username = ?";
    $params[] = $username;

    $update = $pdo->prepare($query);
    if ($update->execute($params)) {
      $_SESSION['admin'] = $newUsername;
      $success = "Data berhasil diperbarui.";
    } else {
      $error = "Gagal memperbarui data.";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Profil Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen flex flex-col md:flex-row">

  <?php include '../partials/sidebar_admin.php'; ?>

  <main class="flex-1 flex items-center justify-center p-6 md:ml-64">
    <div class="w-full max-w-lg bg-white p-8 rounded-lg shadow-md border border-gray-200">
      <!-- Judul -->
      <div class="flex items-center gap-3 mb-6">
        <i class="bi bi-person-circle text-3xl text-blue-600"></i>
        <h1 class="text-2xl font-bold text-blue-800">Profil Admin</h1>
      </div>

      <!-- Notifikasi -->
      <?php if ($success): ?>
        <div class="bg-green-50 border border-green-200 text-green-700 p-3 rounded mb-4 flex items-center gap-2 shadow-sm">
          <i class="bi bi-check-circle-fill text-green-500 text-lg"></i>
          <span><?= $success ?></span>
        </div>
      <?php elseif ($error): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 p-3 rounded mb-4 flex items-center gap-2 shadow-sm">
          <i class="bi bi-x-circle-fill text-red-500 text-lg"></i>
          <span><?= $error ?></span>
        </div>
      <?php endif; ?>

      <!-- Form -->
      <form method="POST" class="space-y-5">
        <div>
          <label class="block mb-1 font-medium text-gray-700 flex items-center gap-1">
            <i class="bi bi-person-badge-fill text-blue-500"></i> Username
          </label>
          <input type="text" name="username" value="<?= htmlspecialchars($admin['username']) ?>" required
            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm text-sm">
        </div>

        <div>
          <label class="block mb-1 font-medium text-gray-700 flex items-center gap-1">
            <i class="bi bi-lock-fill text-blue-500"></i> Password Baru <span class="text-sm text-gray-400">(Opsional)</span>
          </label>
          <input type="password" name="password"
            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm text-sm"
            placeholder="Kosongkan jika tidak ingin mengubah">
        </div>

        <div>
          <label class="block mb-1 font-medium text-gray-700 flex items-center gap-1">
            <i class="bi bi-lock-check-fill text-blue-500"></i> Konfirmasi Password
          </label>
          <input type="password" name="confirm_password"
            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm text-sm"
            placeholder="Ulangi password baru">
        </div>

        <div>
          <button type="submit"
            class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-semibold transition-all duration-300 flex items-center justify-center gap-2 text-sm">
            <i class="bi bi-save2-fill"></i> Simpan Perubahan
          </button>
        </div>
      </form>
    </div>
  </main>
</body>
</html>
