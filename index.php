<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Selamat Datang - Pilketos SMKN 2 Pinrang</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    @media (prefers-color-scheme: dark) {
      body {
        background: #0f172a;
      }
    }
  </style>
</head>
<body class="bg-gradient-to-br from-blue-100 to-green-100 min-h-screen flex flex-col items-center justify-center px-4 py-10">

  <!-- Card -->
  <main class="w-full max-w-xl bg-white p-6 md:p-10 rounded-xl shadow-lg text-center">
    <img src="assets/logo-smk2.png" alt="Logo Sekolah" class="w-24 mx-auto mb-4 rounded-full shadow">
    
    <h1 class="text-3xl font-bold text-blue-700 mb-2">PEMILIHAN KETUA OSIS & MPK</h1>
    <h2 class="text-lg text-gray-600 mb-4">SMKN 2 Pinrang</h2>
    
    <p class="text-gray-700 mb-6 leading-relaxed">
      Mari kita sukseskan Pilketos tahun ini dengan semangat demokrasi di lingkungan sekolah.
      Pilihlah pemimpin terbaikmu untuk OSIS dan MPK demi masa depan organisasi sekolah yang lebih baik.
    </p>
    
    <a href="auth/login.php" class="inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg text-lg transition-all duration-300 shadow-md">
      <i class="bi bi-box-arrow-in-right"></i>
      <span>Login untuk Voting</span>
    </a>
  </main>

  <!-- Footer -->
  <footer class="mt-10 text-sm text-gray-600 text-center">
    &copy; <?= date('Y') ?> COM SMK2PINRANG - E-Voting Pilketos. All rights reserved.
  </footer>

</body>
</html>
