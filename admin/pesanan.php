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

// --- AMBIL DATA SEMUA PESANAN ---
$query = "
    SELECT o.*, 
    GROUP_CONCAT(CONCAT(m.name, ' (', od.quantity, ')') SEPARATOR ', ') as menu_list
    FROM orders o
    LEFT JOIN order_details od ON o.id = od.order_id
    LEFT JOIN menu_items m ON od.menu_item_id = m.id
    GROUP BY o.id
    ORDER BY o.created_at DESC
";
$result = mysqli_query($conn, $query);

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

                <div class="card-table">
                    <div class="card-header">
                        <h3>Riwayat Semua Pesanan</h3>
                        <a href="#" style="color:var(--brand-red); text-decoration:none; font-size:0.9rem;">Lihat
                            Semua</a>
                    </div>

                    <table>
                        <thead>
                            <tr>
                                <th>ID Order</th>
                                <th>Tanggal</th>
                                <th>Pelanggan</th>
                                <th>Detail Pesanan</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <?php
                                // Tentukan class warna berdasarkan status database
                                $status = strtolower($row['status']);
                                $badgeClass = 'badge-pending'; // Default
                            
                                if ($status == 'cooking')
                                    $badgeClass = 'badge-cooking';
                                elseif ($status == 'delivery')
                                    $badgeClass = 'badge-delivery';
                                elseif ($status == 'completed')
                                    $badgeClass = 'badge-completed';
                                elseif ($status == 'cancelled')
                                    $badgeClass = 'badge-cancelled';
                                ?>
                                <tr>
                                    <td><strong>#ORD-<?= str_pad($row['id'], 3, '0', STR_PAD_LEFT) ?></strong></td>

                                    <td style="font-size:0.85rem; color:#666;">
                                        <?= date('d M Y H:i', strtotime($row['created_at'])) ?>
                                    </td>

                                    <td><?= htmlspecialchars($row['customer_name']) ?></td>

                                    <td style="max-width: 300px; line-height: 1.4;">
                                        <?= $row['menu_list'] ?: '<span style="color:#aaa;">Item dihapus</span>' ?>
                                    </td>

                                    <td style="font-weight:bold;">
                                        Rp <?= number_format($row['total_amount'], 0, ',', '.') ?>
                                    </td>

                                    <td>
                                        <span class="badge <?php echo $o['status']; ?>">
                                            <?php echo ucfirst($row['status']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>

                            <?php if (mysqli_num_rows($result) == 0): ?>
                                <tr>
                                    <td colspan="6" style="text-align:center; padding: 30px; color:#999;">Belum ada riwayat
                                        pesanan.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
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