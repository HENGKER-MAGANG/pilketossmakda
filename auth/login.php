<?php
session_start();
require '../config/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'];
  $password = $_POST['password'];

  // Cek Admin
  $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = ? AND password = MD5(?)");
  $stmt->execute([$username, $password]);
  $admin = $stmt->fetch();

  if ($admin) {
    $_SESSION['admin'] = $admin['username'];
    header("Location: ../admin/index.php");
    exit;
  }

  // Cek Siswa
  $stmt = $pdo->prepare("SELECT * FROM users WHERE nis = ?");
  $stmt->execute([$username]);
  $user = $stmt->fetch();

  if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['nama'] = $user['nama'];
    $_SESSION['kelas'] = $user['kelas'];
    $_SESSION['sudah_memilih'] = $user['sudah_memilih'];
    header("Location: ../siswa/index.php");
    exit;
  } else {
    $error = "NIS atau Password salah.";
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login - E-Voting Pilketos</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(to bottom right, #dbeafe, #bbf7d0);
    }
  </style>
</head>
<body class="min-h-screen flex flex-col justify-center items-center px-4 py-6">

  <!-- Login Card -->
  <div class="w-full max-w-md bg-white p-6 rounded-xl shadow-md">
    <div class="text-center mb-4">
      <img src="../assets/logo-smk2.png" alt="Logo SMKN 2 Pinrang" class="w-24 h-24 mx-auto mb-2 rounded-full shadow">
      <h1 class="text-2xl font-bold text-blue-700">E-Voting Pilketos</h1>
      <p class="text-sm text-gray-500">SMKN 2 Pinrang</p>
    </div>

    <?php if ($error): ?>
      <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm flex items-center gap-2">
        <i class="bi bi-exclamation-circle-fill"></i> <?= $error ?>
      </div>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
      <div>
        <label class="block text-gray-700 font-medium mb-1">Nomor Induk Sekolah (NIS)</label>
        <input type="text" name="username" required autofocus
               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
      </div>

      <div>
        <label class="block text-gray-700 font-medium mb-1">Password</label>
        <input type="password" name="password" required
               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
      </div>

      <button type="submit"
              class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-semibold transition">
        <i class="bi bi-box-arrow-in-right mr-2"></i> Login Sekarang
      </button>
    </form>
  </div>

  <!-- Footer -->
  <footer class="mt-6 w-full text-center text-gray-600 text-sm px-4">
    &copy; <?= date('Y') ?> COM SMKN2PINRANG - E-Voting Pilketos. Hak Cipta Dilindungi.
  </footer>

</body>
</html>
