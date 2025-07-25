<?php
// config/db.php

$host = 'z8wokk08g8cgg4swc80c4gcc'; // host dari Coolify
$db   = 'pilketos';
$user = 'pilketos';
$pass = 'pilketos123';

try {
  $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  die("Koneksi database gagal: " . $e->getMessage());
}
?>
