<?php
// config/logic/history_logic.php
session_start();

// 1. WAJIB: SET TIMEZONE AGAR TANGGAL PHP & DATABASE SINKRON
date_default_timezone_set('Asia/Jakarta');

include '../config/db.php';

// --- CEK LOGIN ---
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: login.php");
    exit;
}

$pageTitle = "Riwayat Pesanan";
$currentPage = "history";

// --- 2. SETTING FILTER ---

// Default Tanggal: Dari 30 hari yang lalu sampai Hari Ini
// (Agar data bulan lalu tetap terlihat saat buka halaman pertama kali)
$defaultStart = date('Y-m-d', strtotime('-30 days'));
$defaultEnd   = date('Y-m-d');

$startDate = isset($_GET['start']) && !empty($_GET['start']) ? $_GET['start'] : $defaultStart;
$endDate   = isset($_GET['end'])   && !empty($_GET['end'])   ? $_GET['end']   : $defaultEnd;

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$sort   = isset($_GET['sort']) ? $_GET['sort'] : 'desc';
$limit  = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
if ($limit < 1) $limit = 10;

// Pagination
$page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// --- 3. BUILD QUERY ---
$whereClause = "WHERE 1=1";

// A. Filter Status (OPSIONAL TAPI DISARANKAN)
// Biasanya History hanya menampilkan yang sudah Selesai/Batal.
// Hapus baris ini jika kamu ingin SEMUA status muncul.
$whereClause .= " AND o.status IN ('completed', 'cancelled')";

// B. Filter Search
if (!empty($search)) {
    $whereClause .= " AND (o.customer_name LIKE '%$search%' OR o.id LIKE '%$search%')";
}

// C. Filter Tanggal
// Menggunakan DATE() agar jam diabaikan, fokus ke tanggalnya saja
$whereClause .= " AND DATE(o.created_at) BETWEEN '$startDate' AND '$endDate'";

// --- 4. EKSEKUSI ---

// Hitung Total Data (Tanpa Join untuk performa)
$countQuery = "SELECT COUNT(*) as total FROM orders o $whereClause";
$countResult = mysqli_query($conn, $countQuery);
$totalRows = mysqli_fetch_assoc($countResult)['total'] ?? 0;
$totalPages = ceil($totalRows / $limit);

// Ambil Data Utama
// GROUP_CONCAT untuk menggabungkan menu dalam satu baris
$query = "
    SELECT o.*, 
    GROUP_CONCAT(CONCAT(m.name, ' (', od.quantity, ')') SEPARATOR ', ') as items_summary,
    SUM(od.subtotal) as calculated_total
    FROM orders o
    LEFT JOIN order_details od ON o.id = od.order_id
    LEFT JOIN menu_items m ON od.menu_item_id = m.id
    $whereClause
    GROUP BY o.id
    ";

// Sorting
if ($sort == 'asc') {
    $query .= " ORDER BY o.created_at ASC";
} else {
    $query .= " ORDER BY o.created_at DESC";
}

$query .= " LIMIT $offset, $limit";

$result = mysqli_query($conn, $query);

// Cek Error Query (Untuk Debugging jika masih kosong)
if (!$result) {
    die("Query Error: " . mysqli_error($conn));
}

// Parameter URL untuk Pagination Link
$urlParams = "&search=$search&start=$startDate&end=$endDate&sort=$sort&limit=$limit";
