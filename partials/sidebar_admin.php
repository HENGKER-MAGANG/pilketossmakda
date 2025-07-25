<?php
$current = basename($_SERVER['PHP_SELF']);
function isActive($page) {
  global $current;
  return $current === $page;
}
?>

<aside class="hidden md:flex flex-col w-64 min-h-screen bg-gradient-to-b from-blue-50 to-white border-r border-blue-200 shadow-sm fixed left-0 top-0 z-40">

  <!-- Logo -->
  <div class="flex items-center justify-center h-24 border-b border-blue-200 bg-blue-50 shadow-inner">
    <div class="text-center">
      <img src="../assets/logo-smk2.png" alt="Logo Sekolah"
           class="w-10 h-10 mx-auto mb-2 rounded-full shadow transition-all duration-700 ease-in-out" />
      <h1 class="text-xl font-bold text-blue-800 transition-all duration-500">E-Voting Pilketos</h1>
      <p class="text-xs text-gray-500">SMKN 2 Pinrang</p>
    </div>
  </div>

  <!-- Navigasi -->
  <nav class="flex-1 px-4 py-6 space-y-2 text-sm">
    <?php
    $navItems = [
      ['index.php',         'bi-house-door-fill',       'Dashboard',       'text-blue-600'],
      ['users.php',         'bi-person-plus-fill',      'Input Siswa',     'text-green-600'],
      ['lihat_siswa.php',   'bi-people-fill',           'Lihat Siswa',     'text-indigo-600'],
      ['kandidat.php',      'bi-person-badge-fill',     'Input Kandidat',  'text-yellow-600'],
      ['lihat_kandidat.php','bi-person-lines-fill',     'Lihat Kandidat',  'text-purple-600'],
      ['input_kelas.php',   'bi-journal-plus',          'Input Kelas',     'text-pink-600'],
      ['hasil.php',         'bi-bar-chart-fill',        'Hasil Voting',    'text-cyan-600'],
      ['profil.php',        'bi-person-circle',         'Profil',          'text-orange-600'],
    ];

    foreach ($navItems as [$href, $icon, $label, $color]) {
      $isActive = isActive($href);
      $classes = $isActive
        ? 'bg-blue-100 text-blue-800 font-semibold border-l-4 border-blue-500 shadow-md animate-fadeIn'
        : 'text-gray-700 hover:bg-blue-50 hover:translate-x-1';

      echo '
      <a href="' . $href . '" class="group flex items-center gap-3 px-4 py-2 rounded-lg transition-all duration-500 ease-in-out ' . $classes . '">
        <i class="bi ' . $icon . ' ' . $color . ' transform transition-all duration-300 ease-in-out group-hover:scale-110 group-hover:-rotate-1"></i>
        <span class="transition-all duration-300 ease-in-out">' . $label . '</span>
      </a>';
    }
    ?>
  </nav>

  <!-- Logout -->
  <div class="px-4 py-4 border-t border-blue-200 bg-blue-50">
    <a href="../auth/logout.php"
       class="flex items-center gap-3 text-red-600 hover:bg-red-100 px-4 py-2 rounded-lg transition-all duration-200 ease-in-out">
      <i class="bi bi-box-arrow-right"></i>
      Logout
    </a>
  </div>
</aside>

<!-- Animasi Fade -->
<style>
  @keyframes fadeIn {
    from {
      opacity: 0;
      transform: translateX(-10px);
    }
    to {
      opacity: 1;
      transform: translateX(0);
    }
  }
  .animate-fadeIn {
    animation: fadeIn 0.6s ease-out forwards;
  }
</style>
