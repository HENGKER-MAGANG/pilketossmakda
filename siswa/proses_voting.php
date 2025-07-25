<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: ../auth/login.php");
  exit;
}

$user_id = $_SESSION['user_id'];

// Cek apakah sudah voting
$stmt = $pdo->prepare("SELECT sudah_memilih FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if ($user['sudah_memilih']) {
  header("Location: index.php");
  exit;
}

// Validasi input
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['osis_id'], $_POST['mpk_id'])) {
  $osis_id = $_POST['osis_id'];
  $mpk_id = $_POST['mpk_id'];

  // Simpan vote
  $stmt = $pdo->prepare("INSERT INTO votes (user_id, osis_id, mpk_id) VALUES (?, ?, ?)");
  $stmt->execute([$user_id, $osis_id, $mpk_id]);

  // Update status user
  $stmt = $pdo->prepare("UPDATE users SET sudah_memilih = 1 WHERE id = ?");
  $stmt->execute([$user_id]);

  // Update session
  $_SESSION['sudah_memilih'] = 1;

  // Redirect
  header("Location: index.php");
  exit;
} else {
  echo "Form tidak lengkap.";
}
