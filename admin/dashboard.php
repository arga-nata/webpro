<?php
session_start();
// Hubungkan Database untuk Pengecekan Sesi
include '../config/db.php'; 

$pageTitle = "Home"; 
$currentPage = "home"; // Pastikan ini sama dengan kata kunci di sidebar.php

// --- SCRIPT ANTI-CACHE ---
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// 1. CEK STATUS LOGIN DASAR
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: login.php");
    exit;
}

// 2. CEK SINGLE DEVICE (FITUR BARU)
// Ambil ID session saat ini di browser
$my_session_id = session_id();
$my_id = $_SESSION['admin_id'];

// Cek apa session ID di database untuk user ini
$check_query = "SELECT last_session_id FROM admins WHERE id = '$my_id'";
$check_result = mysqli_query($conn, $check_query);
$check_row = mysqli_fetch_assoc($check_result);

// Jika Session ID di database BEDA dengan di browser, berarti ada login baru di tempat lain
if ($check_row['last_session_id'] !== $my_session_id) {
    // Hancurkan sesi paksa
    session_unset();
    session_destroy();
    
    // Mulai sesi baru cuma buat ngasih pesan error
    session_start();
    $_SESSION['flash_status'] = 'error';
    $_SESSION['flash_message'] = 'Akun Anda telah login di perangkat lain. Anda keluar otomatis.';
    
    header("Location: login.php");
    exit;
}

// AMBIL NAMA ADMIN DARI SESSION
$adminName = isset($_SESSION['admin_user']) ? ucfirst($_SESSION['admin_user']) : "Admin";

$stats = [
  'pesanan_pending' => 12,
  'total_omset' => "Rp 4.500.000",
  'menu_total' => 32
];

// Contoh data tabel
$orders = [
  ['id' => '#ORD-001', 'nama' => 'Budi Santoso', 'menu' => 'Salmon Roll (2)', 'total' => 'Rp 90.000', 'status' => 'pending'],
  ['id' => '#ORD-002', 'nama' => 'Siti Aminah', 'menu' => 'Tuna Maki (1)', 'total' => 'Rp 35.000', 'status' => 'pending'],
  ['id' => '#ORD-003', 'nama' => 'Driver Gojek', 'menu' => 'Paket Hemat A', 'total' => 'Rp 120.000', 'status' => 'success'],
];
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
</head>

<body>

  <div class="wrapper">

    <?php include '../assets/components/sidebar.php'; ?>

    <div class="main-content">

      <?php include '../assets/components/topbar.php'; ?>

      <div class="content-padding">

        <div class="stats-grid">
          <div class="card-stat">
            <div class="icon-box icon-red"><i class='bx bxs-bell-ring'></i></div>
            <div>
              <h3 style="margin:0; font-size:1.5rem;"><?php echo $stats['pesanan_pending']; ?></h3>
              <span style="color:var(--text-muted); font-size:0.9rem;">Pesanan Baru</span>
            </div>
          </div>
          <div class="card-stat">
            <div class="icon-box icon-green"><i class='bx bxs-wallet'></i></div>
            <div>
              <h3 style="margin:0; font-size:1.5rem;"><?php echo $stats['total_omset']; ?></h3>
              <span style="color:var(--text-muted); font-size:0.9rem;">Omset Hari Ini</span>
            </div>
          </div>
          <div class="card-stat">
            <div class="icon-box icon-blue"><i class='bx bxs-food-menu'></i></div>
            <div>
              <h3 style="margin:0; font-size:1.5rem;"><?php echo $stats['menu_total']; ?></h3>
              <span style="color:var(--text-muted); font-size:0.9rem;">Total Menu</span>
            </div>
          </div>
        </div>

        <div class="card-table">
          <div class="card-header">
            <h3>Pesanan Masuk Terbaru</h3>
            <a href="#" style="color:var(--brand-red); text-decoration:none; font-size:0.9rem;">Lihat Semua</a>
          </div>

          <table>
            <thead>
              <tr>
                <th>ID Order</th>
                <th>Pelanggan</th>
                <th>Menu</th>
                <th>Total</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($orders as $o): ?>
                <tr>
                  <td><?php echo $o['id']; ?></td>
                  <td><strong><?php echo $o['nama']; ?></strong></td>
                  <td><?php echo $o['menu']; ?></td>
                  <td style="color:var(--success); font-weight:bold;"><?php echo $o['total']; ?></td>

                  <td>
                    <span class="badge <?php echo $o['status']; ?>">
                      <?php echo ucfirst($o['status']); ?>
                    </span>
                  </td>

                  <td>
                    <?php if ($o['status'] == 'pending'): ?>
                      <button class="btn-sm btn-acc" title="Terima"><i class='bx bx-check'></i></button>
                      <button class="btn-sm btn-rej" title="Tolak" onclick="openRejectModal('<?php echo $o['id']; ?>')"><i class='bx bx-x'></i></button>
                    <?php else: ?>
                      <span style="color:#555; font-size:0.8rem;">Selesai</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

      </div>
    </div>
  </div> 
  
  <?php include '../assets/components/modal_reject.php'; ?>

  <div class="mobile-overlay"></div>

  <script>
        const sidebar = document.querySelector('.sidebar');
        const mainContent = document.querySelector('.main-content');
        const toggleBtn = document.querySelector('.toggle-btn');
        const overlay = document.querySelector('.mobile-overlay');

        // Pastikan overlay ada sebelum menjalankan logic
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