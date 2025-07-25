<?php
// config/db.php

$host = 'localhost';
$db   = 'pilketos';
$user = 'root';
$pass = ''; // Jika pakai XAMPP default kosong

try {
  $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  die("Koneksi database gagal: " . $e->getMessage());
}
?>
