<?php
// FILE: config/logic/laporan_logic.php
session_start();
date_default_timezone_set('Asia/Jakarta');
include '../config/db.php';

// Cek Login
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

// 1. FILTER PERIODE
$startDate = isset($_GET['start']) ? $_GET['start'] : date('Y-m-01');
$endDate   = isset($_GET['end'])   ? $_GET['end']   : date('Y-m-d');
$urlParams = "&start=$startDate&end=$endDate";

// 2. QUERY DATA HARIAN (Tabel Utama)
// Mengambil data Orders yang valid (bukan cancelled)
$reportData = [];
$query = "
    SELECT 
        DATE(o.created_at) as date,
        COUNT(DISTINCT o.id) as trx,
        COALESCE(SUM(od.quantity), 0) as sold,
        COALESCE(SUM(od.subtotal), 0) as gross
    FROM orders o
    LEFT JOIN order_details od ON o.id = od.order_id
    WHERE o.status = 'completed' 
    AND DATE(o.created_at) BETWEEN '$startDate' AND '$endDate'
    GROUP BY DATE(o.created_at)
    ORDER BY date ASC
";
$result = mysqli_query($conn, $query);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Estimasi HPP (Misal 60% dari Gross)
        $row['cost'] = $row['gross'] * 0.60; 
        $reportData[] = $row;
    }
}

// 3. HITUNG TOTAL KPI
$totalGross = 0; $totalCost = 0; $totalTrx = 0; $totalSold = 0;

foreach ($reportData as $d) {
    $totalGross += $d['gross'];
    $totalCost  += $d['cost'];
    $totalTrx   += $d['trx'];
    $totalSold  += $d['sold'];
}

$totalNet = $totalGross - $totalCost;
$avgOrder = ($totalTrx > 0) ? $totalGross / $totalTrx : 0;

// 4. DATA TAMBAHAN (Analisis)

// A. Cancel Rate (Hitung dari tabel orders saja agar cepat)
$queryCancelled = "SELECT COUNT(*) as total FROM orders WHERE status = 'cancelled' AND DATE(created_at) BETWEEN '$startDate' AND '$endDate'";
$resCancelled = mysqli_query($conn, $queryCancelled);
$totalCancelled = mysqli_fetch_assoc($resCancelled)['total'] ?? 0;

// Total semua order masuk (Completed + Cancelled + Others)
$queryAll = "SELECT COUNT(*) as total FROM orders WHERE DATE(created_at) BETWEEN '$startDate' AND '$endDate'";
$resAll = mysqli_query($conn, $queryAll);
$totalAllOrders = mysqli_fetch_assoc($resAll)['total'] ?? 0;

$cancelRate = ($totalAllOrders > 0) ? ($totalCancelled / $totalAllOrders) * 100 : 0;

// B. Top Menu (Best Seller)
$queryTopItem = "
    SELECT m.name, SUM(od.quantity) as qty
    FROM order_details od
    JOIN menu_items m ON od.menu_item_id = m.id
    JOIN orders o ON od.order_id = o.id
    WHERE o.status = 'completed' AND DATE(o.created_at) BETWEEN '$startDate' AND '$endDate'
    GROUP BY m.id ORDER BY qty DESC LIMIT 1
";
$resTop = mysqli_query($conn, $queryTopItem);
$topItem = mysqli_fetch_assoc($resTop);
$topItemName = $topItem['name'] ?? "Belum ada data";
$topItemContribution = ($totalSold > 0) ? (($topItem['qty'] ?? 0) / $totalSold) * 100 : 0;


// 5. SMART NARRATIVE (Analisis Teks)
$analisis_text = "";

if ($totalTrx == 0) {
    if ($totalCancelled > 0) {
        $analisis_text = "⚠️ <strong>Perhatian:</strong> Belum ada transaksi sukses, namun tercatat <strong class='text-red'>{$totalCancelled} pembatalan</strong>. Cek operasional segera.";
    } else {
        $analisis_text = "Belum ada data transaksi pada periode ini.";
    }
} else {
    // Variabel Kalimat
    $operasionalStatus = ($cancelRate <= 5) ? 'baik' : (($cancelRate <= 10) ? 'normal' : 'perlu perhatian');
    $dayaBeli = ($avgOrder >= 50000) ? 'tinggi' : (($avgOrder >= 25000) ? 'sehat' : 'rendah');
    
    // Paragraf 1: Keuangan
    $analisis_text .= "Total pendapatan periode ini mencapai <strong class='text-white'>Rp " . number_format($totalGross, 0, ',', '.') . "</strong>. ";
    $analisis_text .= "Rata-rata transaksi per pelanggan adalah <strong class='text-white'>Rp " . number_format($avgOrder, 0, ',', '.') . "</strong> (daya beli $dayaBeli). ";

    // Paragraf 2: Operasional
    $analisis_text .= "Tingkat pembatalan tercatat <strong class='" . ($cancelRate > 10 ? 'text-red' : 'text-green') . "'>" . number_format($cancelRate, 1) . "%</strong>. ";

    // Paragraf 3: Menu
    if ($topItem) {
        $analisis_text .= "Menu terlaris adalah <strong class='text-brand'>{$topItemName}</strong> yang menyumbang " . number_format($topItemContribution, 1) . "% dari total item terjual.";
    }
}