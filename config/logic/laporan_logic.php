<?php
// config/logic/laporan_logic.php
session_start();
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
$reportData = [];
$query = "
    SELECT 
        DATE(o.created_at) as date,
        COUNT(DISTINCT o.id) as trx,
        COALESCE(SUM(od.quantity), 0) as sold,
        COALESCE(SUM(od.subtotal), 0) as gross
    FROM orders o
    LEFT JOIN order_details od ON o.id = od.order_id
    WHERE o.status != 'cancelled' 
    AND DATE(o.created_at) BETWEEN '$startDate' AND '$endDate'
    GROUP BY DATE(o.created_at)
    ORDER BY date ASC
";
$result = mysqli_query($conn, $query);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Asumsi HPP 60% (Bisa disesuaikan nanti)
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

// 4. DATA TAMBAHAN (Untuk Analisis)

// A. Cancel Rate
$queryCancelled = "SELECT COUNT(*) as total FROM orders WHERE status = 'cancelled' AND DATE(created_at) BETWEEN '$startDate' AND '$endDate'";
$resCancelled = mysqli_query($conn, $queryCancelled);
$totalCancelled = mysqli_fetch_assoc($resCancelled)['total'] ?? 0;
$totalAllOrders = $totalTrx + $totalCancelled;
$cancelRate = ($totalAllOrders > 0) ? ($totalCancelled / $totalAllOrders) * 100 : 0;

// B. Top Menu
$queryTopItem = "
    SELECT m.name, SUM(od.quantity) as qty
    FROM order_details od
    JOIN menu_items m ON od.menu_item_id = m.id
    JOIN orders o ON od.order_id = o.id
    WHERE o.status != 'cancelled' AND DATE(o.created_at) BETWEEN '$startDate' AND '$endDate'
    GROUP BY m.id ORDER BY qty DESC LIMIT 1
";
$resTop = mysqli_query($conn, $queryTopItem);
$topItem = mysqli_fetch_assoc($resTop);
$topItemName = $topItem['name'] ?? "Belum ada data";
$topItemContribution = ($totalSold > 0) ? (($topItem['qty'] ?? 0) / $totalSold) * 100 : 0;


// 5. SMART NARRATIVE ENGINE (LOGIKA AI SEDERHANA)
$analisis_text = "";

// SKENARIO 1: DATA KOSONG
if ($totalTrx == 0) {
    if ($totalCancelled > 0) {
        $analisis_text = "⚠️ <strong>Perhatian:</strong> Belum ada pendapatan masuk, namun tercatat ada <strong class='text-red'>{$totalCancelled} pesanan yang dibatalkan</strong>. Segera periksa kendala operasional di dapur atau kasir karena potensi penjualan hilang sepenuhnya.";
    } else {
        $analisis_text = "Belum ada aktivitas penjualan yang terekam pada periode ini. Data analisis akan muncul secara otomatis setelah toko mulai menerima pesanan yang berhasil diselesaikan.";
    }
} 
// SKENARIO 2: ADA DATA (ANALISIS MENDALAM)
else {
    // Tentukan Variabel Kalimat
    $operasionalStatus = ($cancelRate <= 5) ? 'baik' : (($cancelRate <= 10) ? 'normal' : 'perlu perhatian');
    $dayaBeli = ($avgOrder >= 60000) ? 'tinggi' : (($avgOrder >= 45000) ? 'sehat' : 'rendah');
    $menuDominan = $topItemContribution >= 25;

    // Bank Kalimat
    $bankKalimat = [
        'operasional' => [
            'baik' => 'Aktivitas operasional berjalan sangat lancar didominasi oleh order yang berhasil diselesaikan',
            'normal' => 'Proses operasional berjalan cukup stabil dengan tingkat pembatalan yang masih dalam batas wajar',
            'perlu perhatian' => 'Terdapat kendala dalam operasional yang ditandai dengan tingkat pembatalan order cukup tinggi'
        ],
        'daya_beli' => [
            'tinggi' => 'mengindikasikan daya beli pelanggan berada pada level yang kuat',
            'sehat' => 'menunjukkan daya beli pelanggan berada pada level yang sehat',
            'rendah' => 'mengindikasikan pelanggan cenderung memilih menu hemat atau transaksi bernilai kecil'
        ],
        'menu' => [
            'dominan' => 'menjadi kontributor utama penjualan',
            'biasa' => 'menjadi menu dengan tingkat pemesanan tertinggi'
        ]
    ];

    // Rangkai Paragraf
    // 1. Keuangan
    $analisis_text .= "Selama periode ini, Street Sushi mencatat total pendapatan sebesar <strong class='text-white'>Rp " . number_format($totalGross, 0, ',', '.') . "</strong>. ";
    $analisis_text .= "Rata-rata nilai per transaksi berada di kisaran <strong class='text-white'>Rp " . number_format($avgOrder, 0, ',', '.') . "</strong>, yang {$bankKalimat['daya_beli'][$dayaBeli]}. ";

    // 2. Operasional
    $analisis_text .= "Dari sisi operasional, {$bankKalimat['operasional'][$operasionalStatus]}, dengan rasio pembatalan sekitar <strong class='" . ($cancelRate > 10 ? 'text-red' : 'text-white') . "'>" . number_format($cancelRate, 1) . "%</strong> dari total pesanan masuk. ";

    // 3. Produk
    if ($topItem) {
        $menuSentence = $menuDominan 
            ? $bankKalimat['menu']['dominan'] . " dengan kontribusi signifikan sekitar " . number_format($topItemContribution, 1) . "%" 
            : $bankKalimat['menu']['biasa'];
        $analisis_text .= "Untuk performa produk, menu <strong class='text-brand'>{$topItemName}</strong> {$menuSentence} dari total item terjual. ";
    }

    // 4. Kesimpulan Strategis
    if ($dayaBeli == 'rendah') {
        $analisis_text .= "Fokus ke depan dapat diarahkan pada strategi <em>upselling</em> atau paket <em>bundling</em> untuk meningkatkan nilai transaksi per pelanggan.";
    } elseif ($operasionalStatus == 'perlu perhatian') {
        $analisis_text .= "Prioritas utama saat ini adalah mengevaluasi alur kerja dapur dan layanan untuk menekan angka pembatalan pesanan.";
    } else {
        $analisis_text .= "Secara keseluruhan, performa penjualan stabil. Strategi ke depan dapat difokuskan pada optimalisasi stok menu terlaris untuk menjaga momentum penjualan.";
    }
}
?>