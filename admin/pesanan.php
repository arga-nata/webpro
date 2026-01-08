<?php
session_start();
include '../config/db.php'; 

// --- BAGIAN 1: KONFIGURASI HALAMAN (Minta Temanmu Ubah Ini) ---
$pageTitle = "Pesanan"; // Judul yang muncul di Tab Browser
$currentPage = "pesanan";     // Harus sesuai dengan logika di sidebar.php (misal: 'pesanan', 'menu')


// --- BAGIAN 2: CEK KEAMANAN (JANGAN DIUBAH) ---
// Cek Login
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: login.php");
    exit;
}
// Ambil Nama Admin untuk Topbar
$adminName = isset($_SESSION['admin_user']) ? ucfirst($_SESSION['admin_user']) : "Admin";
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $pageTitle ?> - Street Sushi</title>

  <link rel="shortcut icon" href="../assets/images/favicon.ico" type="image/x-icon">
  
  <link rel="stylesheet" href="../assets/css/dashboard/page.css">
  
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

  <style>
    /* CSS Khusus halaman ini */
  </style>
</head>

<body>

  <div class="wrapper">

    <?php include '../assets/components/sidebar.php'; ?>

    <div class="main-content">

      <?php include '../assets/components/topbar.php'; ?>

      <div class="content-padding">

        <div class="header-halaman">
            <h1>Judul Konten Di Sini</h1>
            <button class="btn-tambah">+ Tombol Aksi</button>
        </div>

        <div class="card-table">
            <p>Isi tabel, form, atau konten lainnya di dalam sini...</p>
            </div>

        </div>
    </div>
  </div> 

  <div class="mobile-overlay"></div>

  <script>
        const sidebar = document.querySelector('.sidebar');
        const mainContent = document.querySelector('.main-content');
        const toggleBtn = document.querySelector('.toggle-btn');
        const overlay = document.querySelector('.mobile-overlay');

        if (overlay) {
            toggleBtn.addEventListener('click', () => {
                if (window.innerWidth > 768) {
                    sidebar.classList.toggle('close');
                } else {
                    sidebar.classList.toggle('active');
                    overlay.classList.toggle('active');
                    sidebar.classList.remove('close'); 
                }
            });

            overlay.addEventListener('click', () => {
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
            });
        }
  </script>

</body>
</html>