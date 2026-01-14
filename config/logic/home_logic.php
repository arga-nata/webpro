<?php
// FILE: config/logic/home_logic.php

session_start();
// ATUR ZONA WAKTU (PENTING! Agar 'Hari Ini' sesuai jam Indonesia)
date_default_timezone_set('Asia/Jakarta');

include '../config/db.php'; 

// 1. CEK LOGIN & SESSION
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: login.php");
    exit;
}

// Cek Double Login (Session Hijacking Prevention)
$my_session_id = session_id();
$my_id = $_SESSION['admin_id'];
$check_query = "SELECT last_session_id FROM admins WHERE id = '$my_id'";
$check_result = mysqli_query($conn, $check_query);

if ($check_result && mysqli_num_rows($check_result) > 0) {
    $check_row = mysqli_fetch_assoc($check_result);
    if ($check_row['last_session_id'] !== $my_session_id) {
        session_unset(); session_destroy(); session_start();
        $_SESSION['flash_status'] = 'error';
        $_SESSION['flash_message'] = 'Akun Anda telah login di perangkat lain.';
        header("Location: login.php"); exit;
    }
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
        // Redirect kembali ke halaman home agar tidak resubmit saat refresh
        header("Location: home.php"); 
        exit;
    }
}

// 3. STATISTIK HARI INI (SECTION 2: TODAY SUMMARY)
$today = date('Y-m-d'); // Mengambil tanggal hari ini sesuai zona waktu Asia/Jakarta

// A. Order Masuk Hari Ini (Semua status yang dibuat hari ini)
$q_today = mysqli_query($conn, "SELECT COUNT(*) as total FROM orders WHERE DATE(created_at) = '$today'");
$d_today = mysqli_fetch_assoc($q_today);

// B. Sedang Proses (Pending/Cooking/Delivery) - REALTIME (Tidak filter hari ini, karena pesanan kemarin yg belum kelar harus tetap muncul)
$q_process = mysqli_query($conn, "SELECT COUNT(*) as total FROM orders WHERE status IN ('pending', 'cooking', 'delivery')");
$d_process = mysqli_fetch_assoc($q_process);

// C. Selesai Hari Ini
$q_done = mysqli_query($conn, "SELECT COUNT(*) as total FROM orders WHERE status = 'completed' AND DATE(created_at) = '$today'");
$d_done = mysqli_fetch_assoc($q_done);

// D. Batal Hari Ini
$q_cancel = mysqli_query($conn, "SELECT COUNT(*) as total FROM orders WHERE status = 'cancelled' AND DATE(created_at) = '$today'");
$d_cancel = mysqli_fetch_assoc($q_cancel);

// Gunakan Null Coalescing Operator (?? 0) untuk mencegah error jika hasil null
$stats = [
    'masuk_today' => $d_today['total'] ?? 0,
    'aktif_sekarang' => $d_process['total'] ?? 0,
    'selesai_today' => $d_done['total'] ?? 0,
    'batal_today' => $d_cancel['total'] ?? 0
];

// 4. DATA TABEL UTAMA (SECTION 3: ACTIVE ACTIVITY)
// Hanya menampilkan yang BELUM SELESAI
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

if ($q_orders) {
    while ($row = mysqli_fetch_assoc($q_orders)) {
        // Hitung durasi (Alert System Dasar)
        $waktu_pesan = strtotime($row['created_at']);
        $selisih_menit = round((time() - $waktu_pesan) / 60);
        
        $row['durasi'] = $selisih_menit; 
        $active_orders[] = $row;
    }
}


/* =========================================
   LOGIKA PENENTUAN SHIFT OTOMATIS
   ========================================= */

// 1. Set Default Status
$current_shift_name = "Tidak ada Shift"; 

// 2. Ambil Waktu Sekarang (Jam:Menit:Detik)
$now = date('H:i:s');

// 3. Ambil Data Shift yang Memiliki Jam Valid (Tidak NULL)
$q_shifts = mysqli_query($conn, "SELECT * FROM work_shifts WHERE start_time IS NOT NULL AND end_time IS NOT NULL");

while ($shift = mysqli_fetch_assoc($q_shifts)) {
    $start = $shift['start_time'];
    $end   = $shift['end_time'];
    $name  = $shift['shift_name'];

    // LOGIKA PERBANDINGAN WAKTU
    
    // KASUS A: Shift Normal (Contoh: 07:00 - 15:00)
    // Start lebih kecil dari End
    if ($start < $end) {
        if ($now >= $start && $now <= $end) {
            $current_shift_name = $name;
            break; // Ketemu, hentikan looping
        }
    }
    // KASUS B: Shift Lintas Hari / Malam (Contoh: 22:00 - 05:00)
    // Start lebih besar dari End
    else {
        if ($now >= $start || $now <= $end) {
            $current_shift_name = $name;
            break; // Ketemu, hentikan looping
        }
    }
}
?>