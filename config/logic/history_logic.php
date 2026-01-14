<?php
// config/logic/history_logic.php
session_start();
include '../config/db.php';

// --- CEK LOGIN ---
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: login.php");
    exit;
}

// --- 1. SETTING DEFAULT ---
$pageTitle = "Riwayat Pesanan";
$currentPage = "history";

// Default Tanggal (Awal Bulan s/d Hari Ini)
$defaultStart = date('Y-m-01'); 
$defaultEnd   = date('Y-m-d'); 

$startDate = isset($_GET['start']) && !empty($_GET['start']) ? $_GET['start'] : $defaultStart;
$endDate   = isset($_GET['end'])   && !empty($_GET['end'])   ? $_GET['end']   : $defaultEnd;

// Filter Lainnya
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$sort   = isset($_GET['sort']) ? $_GET['sort'] : 'desc'; 
$limit  = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
if ($limit < 1) $limit = 10;

// Pagination Helper
$page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// --- 2. BUILD QUERY ---
$whereClause = "WHERE 1=1";

// Filter Search (ID atau Nama)
if (!empty($search)) {
    $whereClause .= " AND (o.customer_name LIKE '%$search%' OR o.id LIKE '%$search%')";
}

// Filter Tanggal
$whereClause .= " AND DATE(o.created_at) BETWEEN '$startDate' AND '$endDate'";

// Sorting
$orderClause = ($sort == 'asc') ? "ORDER BY o.created_at ASC" : "ORDER BY o.created_at DESC";

// --- 3. EKSEKUSI QUERY ---

// Hitung Total Data (untuk Pagination)
$countQuery = "SELECT COUNT(*) as total FROM orders o $whereClause";
$countResult = mysqli_query($conn, $countQuery);
$totalRows = mysqli_fetch_assoc($countResult)['total'];
$totalPages = ceil($totalRows / $limit);

// Ambil Data Utama
$query = "
    SELECT o.*, 
    GROUP_CONCAT(CONCAT(m.name, '|', od.quantity, '|', m.base_price, '|', od.subtotal) SEPARATOR '||') as item_details
    FROM orders o
    LEFT JOIN order_details od ON o.id = od.order_id
    LEFT JOIN menu_items m ON od.menu_item_id = m.id
    $whereClause
    GROUP BY o.id
    $orderClause
    LIMIT $offset, $limit
";
$result = mysqli_query($conn, $query);

// Parameter URL untuk Pagination Link
$urlParams = "&search=$search&start=$startDate&end=$endDate&sort=$sort&limit=$limit";
?>