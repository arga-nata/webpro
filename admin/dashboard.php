<?php
session_start();
// Hubungkan Database untuk Pengecekan Sesi
include '../config/db.php';

$pageTitle = "Home"; // Judul yang muncul di Tab Browser
$currentPage = "home";

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

// --- LOGIKA UPDATE STATUS (Insert di sini) ---
// --- LOGIKA UPDATE STATUS (REVISI LENGKAP) ---
// --- LOGIKA UPDATE STATUS (REVISI FINAL) ---
// --- LOGIKA UPDATE STATUS (LENGKAP: TERIMA, ANTAR, SELESAI, TOLAK) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
  $order_id = intval($_POST['order_id']);
  $action = $_POST['action'];
  $new_status = '';

  // 1. Cek tombol apa yang diklik
  if ($action == 'accept') {
    $new_status = 'cooking';
  } elseif ($action == 'deliver') {
    $new_status = 'delivery';
  } elseif ($action == 'complete') {
    $new_status = 'completed';
  }
  // --- INI BAGIAN KODE YANG KAMU TANYAKAN TADI ---
  elseif ($action == 'reject') {
    $new_status = 'cancelled';

    // Simpan alasan penolakan jika ada
    if (isset($_POST['alasan_tolak']) && !empty($_POST['alasan_tolak'])) {
      $reason = mysqli_real_escape_string($conn, $_POST['alasan_tolak']);
      // Tambahkan alasan ke notes database
      mysqli_query($conn, "UPDATE orders SET notes_general = CONCAT(IFNULL(notes_general, ''), ' [Ditolak: $reason]') WHERE id = $order_id");
    }
  }
  // -----------------------------------------------

  // 2. Eksekusi Update ke Database
  if ($new_status != '') {
    $update = mysqli_query($conn, "UPDATE orders SET status = '$new_status' WHERE id = $order_id");
    if ($update) {
      header("Location: dashboard.php"); // Refresh halaman
      exit;
    }
  }
}

// --- GANTI BAGIAN INI (Step 1) ---

// 1. Hitung Pesanan Pending
$q_pending = mysqli_query($conn, "SELECT COUNT(*) as total FROM orders WHERE status = 'pending'");
$d_pending = mysqli_fetch_assoc($q_pending);

// 2. Hitung Omset Hari Ini (Hanya status 'completed')
$today = date('Y-m-d');
$q_omset = mysqli_query($conn, "SELECT SUM(total_amount) as total FROM orders WHERE status = 'completed' AND DATE(created_at) = '$today'");
$d_omset = mysqli_fetch_assoc($q_omset);

// 3. Hitung Total Menu
$q_menu = mysqli_query($conn, "SELECT COUNT(*) as total FROM menu_items");
$d_menu = mysqli_fetch_assoc($q_menu);

// Masukkan data asli ke variabel $stats
$stats = [
  'pesanan_pending' => $d_pending['total'] ?? 0,
  'total_omset' => "Rp " . number_format($d_omset['total'] ?? 0, 0, ',', '.'),
  'menu_total' => $d_menu['total'] ?? 0
];

// Cari bagian ini dan ganti dengan kode di bawah:
$q_orders = mysqli_query($conn, "
    SELECT o.id, o.customer_name, o.total_amount, o.status,
    GROUP_CONCAT(CONCAT(m.name, ' (', od.quantity, ')') SEPARATOR ', ') as menu_list
    FROM orders o
    LEFT JOIN order_details od ON o.id = od.order_id
    LEFT JOIN menu_items m ON od.menu_item_id = m.id
    WHERE o.status != 'completed' AND o.status != 'cancelled'
    GROUP BY o.id
    ORDER BY o.created_at DESC
");
while ($row = mysqli_fetch_assoc($q_orders)) {
  // Mapping status DB (completed) ke class CSS (misal: success) jika perlu
  $css_status = $row['status'];
  if ($row['status'] == 'completed')
    $css_status = 'success'; // Sesuaikan dengan CSS kamu

  $orders[] = [
    'id' => '#ORD-' . str_pad($row['id'], 3, '0', STR_PAD_LEFT), // Bikin ID jadi #ORD-001
    'nama' => htmlspecialchars($row['customer_name']),
    'menu' => $row['menu_list'] ?: 'Item dihapus',
    'total' => 'Rp ' . number_format($row['total_amount'], 0, ',', '.'),
    'status' => $css_status, // Untuk class warna badge
    'status_text' => $row['status'] // Untuk teks asli
  ];
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Home - Street Sushi</title>

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
                      <?php echo ucfirst($o['status_text']); ?>
                    </span>
                  </td>

                  <td>
                    <?php if ($o['status_text'] == 'pending'): ?>
                      <form action="" method="POST" style="display:inline;">
                        <input type="hidden" name="order_id" value="<?php echo intval(substr($o['id'], 5)); ?>">
                        <input type="hidden" name="action" value="accept">
                        <button type="submit" class="btn-sm btn-acc" title="Terima (Masak)">
                          <i class='bx bx-check'></i>
                        </button>
                      </form>

                      <button class="btn-sm btn-rej" title="Tolak"
                        onclick="openRejectModal('<?php echo intval(substr($o['id'], 5)); ?>')">
                        <i class='bx bx-x'></i>
                      </button>

                    <?php elseif ($o['status_text'] == 'cooking'): ?>
                      <form action="" method="POST" style="display:inline;">
                        <input type="hidden" name="order_id" value="<?php echo intval(substr($o['id'], 5)); ?>">
                        <input type="hidden" name="action" value="deliver">
                        <button type="submit" class="btn-sm" style="background:#3498db; color:white;" title="Antar Pesanan">
                          <i class='bx bxs-truck'></i> Antar
                        </button>
                      </form>

                    <?php elseif ($o['status_text'] == 'delivery'): ?>
                      <form action="" method="POST" style="display:inline;">
                        <input type="hidden" name="order_id" value="<?php echo intval(substr($o['id'], 5)); ?>">
                        <input type="hidden" name="action" value="complete">
                        <button type="submit" class="btn-sm" style="background:#27ae60; color:white;"
                          title="Selesaikan Order">
                          <i class='bx bxs-check-circle'></i> Selesai
                        </button>
                      </form>

                    <?php else: ?>
                      <span style="font-size:0.8rem; color:#888;">
                        <i class='bx bx-history'></i> Riwayat
                      </span>
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