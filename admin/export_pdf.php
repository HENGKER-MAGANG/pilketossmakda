<?php
require '../vendor/autoload.php';
require '../config/db.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Query data kandidat dan jumlah suara
$query = "
  SELECT c.*, 
    (SELECT COUNT(*) FROM votes WHERE osis_id = c.id) AS suara_osis,
    (SELECT COUNT(*) FROM votes WHERE mpk_id = c.id) AS suara_mpk
  FROM candidates c
  ORDER BY c.jenis, c.nama
";

$data = $pdo->query($query)->fetchAll();

// Buat HTML untuk PDF
$html = '
  <h2 style="text-align:center;">Statistik Suara Pemilihan</h2>
  <table border="1" cellpadding="8" cellspacing="0" width="100%">
    <thead>
      <tr style="background-color: #f0f0f0;">
        <th>No</th>
        <th>Nama</th>
        <th>Kelas</th>
        <th>Jenis</th>
        <th>Jumlah Suara</th>
      </tr>
    </thead>
    <tbody>';

$no = 1;
foreach ($data as $row) {
  $jumlah_suara = $row['jenis'] === 'osis' ? $row['suara_osis'] : $row['suara_mpk'];
  $html .= '
    <tr>
      <td>' . $no++ . '</td>
      <td>' . htmlspecialchars($row['nama']) . '</td>
      <td>' . htmlspecialchars($row['kelas']) . '</td>
      <td>' . strtoupper($row['jenis']) . '</td>
      <td>' . $jumlah_suara . '</td>
    </tr>';
}

$html .= '</tbody></table>';

// Konfigurasi Dompdf
$options = new Options();
$options->set('defaultFont', 'Arial');
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);

// Ukuran dan orientasi kertas
$dompdf->setPaper('A4', 'portrait');

// Render ke PDF
$dompdf->render();

// Download PDF
$dompdf->stream("statistik_kandidat.pdf", ["Attachment" => true]);
