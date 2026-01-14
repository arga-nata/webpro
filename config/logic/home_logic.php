<?php
// FILE: config/logic/home_logic.php

session_start();
include '../config/db.php'; // Sesuaikan path ini jika perlu (misal: ../../config/db.php)

// 1. CEK LOGIN & SESSION
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: login.php");
    exit;
}

$my_session_id = session_id();
$my_id = $_SESSION['admin_id'];
$check_query = "SELECT last_session_id FROM admins WHERE id = '$my_id'";
$check_result = mysqli_query($conn, $check_query);
$check_row = mysqli_fetch_assoc($check_result);

if ($check_row['last_session_id'] !== $my_session_id) {
    session_unset(); session_destroy(); session_start();
    $_SESSION['flash_status'] = 'error';
    $_SESSION['flash_message'] = 'Akun Anda telah login di perangkat lain.';
    header("Location: login.php"); exit;
}

// 2. LOGIKA AKSI (POST) - TERIMA/TOLAK/ANTAR
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $order_id = intval($_POST['order_id']);
    $action = $_POST['action'];
    $new_status = '';

    if ($action == 'accept') $new_status = 'cooking';
    elseif ($action == 'deliver') $new_status = 'delivery';
    elseif ($action == 'complete') $new_status = 'completed';
    elseif ($action == 'reject') {
        $new_status = 'cancelled';
        if (!empty($_POST['alasan_tolak'])) {
            $reason = mysqli_real_escape_string($conn, $_POST['alasan_tolak']);
            mysqli_query($conn, "UPDATE orders SET notes_general = CONCAT(IFNULL(notes_general, ''), ' [Ditolak: $reason]') WHERE id = $order_id");
        }
    }

    if ($new_status != '') {
        mysqli_query($conn, "UPDATE orders SET status = '$new_status' WHERE id = $order_id");
        header("Location: home.php"); // Refresh halaman
        exit;
    }
}

// 3. STATISTIK HARI INI (SECTION 2: TODAY SUMMARY)
$today = date('Y-m-d');

// A. Order Masuk Hari Ini
$q_today = mysqli_query($conn, "SELECT COUNT(*) as total FROM orders WHERE DATE(created_at) = '$today'");
$d_today = mysqli_fetch_assoc($q_today);

// B. Sedang Proses (Pending/Cooking/Delivery) - Realtime, bukan cuma hari ini
$q_process = mysqli_query($conn, "SELECT COUNT(*) as total FROM orders WHERE status IN ('pending', 'cooking', 'delivery')");
$d_process = mysqli_fetch_assoc($q_process);

// C. Selesai Hari Ini
$q_done = mysqli_query($conn, "SELECT COUNT(*) as total FROM orders WHERE status = 'completed' AND DATE(created_at) = '$today'");
$d_done = mysqli_fetch_assoc($q_done);

// D. Batal Hari Ini
$q_cancel = mysqli_query($conn, "SELECT COUNT(*) as total FROM orders WHERE status = 'cancelled' AND DATE(created_at) = '$today'");
$d_cancel = mysqli_fetch_assoc($q_cancel);

$stats = [
    'masuk_today' => $d_today['total'] ?? 0,
    'aktif_sekarang' => $d_process['total'] ?? 0,
    'selesai_today' => $d_done['total'] ?? 0,
    'batal_today' => $d_cancel['total'] ?? 0
];

// 4. DATA TABEL UTAMA (SECTION 3: ACTIVE ACTIVITY)
// Hanya menampilkan yang BELUM SELESAI (Pending, Cooking, Delivery)
$active_orders = [];
$q_orders = mysqli_query($conn, "
    SELECT o.id, o.customer_name, o.total_amount, o.status, o.created_at,
    GROUP_CONCAT(CONCAT(m.name, ' (', od.quantity, ')') SEPARATOR ', ') as menu_list
    FROM orders o
    LEFT JOIN order_details od ON o.id = od.order_id
    LEFT JOIN menu_items m ON od.menu_item_id = m.id
    WHERE o.status IN ('pending', 'cooking', 'delivery') 
    GROUP BY o.id
    ORDER BY 
        CASE 
            WHEN o.status = 'pending' THEN 1
            WHEN o.status = 'cooking' THEN 2
            ELSE 3 
        END,
        o.created_at ASC
");
// Logika sort: Pending paling atas (butuh aksi), lalu Cooking, lalu Delivery

while ($row = mysqli_fetch_assoc($q_orders)) {
    // Hitung durasi (Alert System Dasar)
    $waktu_pesan = strtotime($row['created_at']);
    $selisih_menit = round((time() - $waktu_pesan) / 60);
    
    $row['durasi'] = $selisih_menit; // Untuk diproses di View (Alert)
    $active_orders[] = $row;
}
?>