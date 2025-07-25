<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['admin'])) {
  header("Location: ../auth/login.php");
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nis = $_POST['nis'] ?? '';

  if ($nis) {
    // Hapus file suara jika ada
    $file_path = "../assets/suara/$nis.mp3";
    if (file_exists($file_path)) {
      unlink($file_path);
    }

    // Hapus suara voting siswa
    $pdo->prepare("DELETE FROM votes WHERE user_id = (SELECT id FROM users WHERE nis = ?)")->execute([$nis]);

    // Hapus siswa
    $stmt = $pdo->prepare("DELETE FROM users WHERE nis = ?");
    $stmt->execute([$nis]);

    header("Location: lihat_siswa.php?success=1");
    exit;
  }
}

header("Location: lihat_siswa.php?error=1");
exit;
