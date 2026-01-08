<?php
session_start();
include '../config/db.php';

$pageTitle = "Laporan Operasional";
$currentPage = "laporan";

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$startDate = isset($_GET['start']) ? $_GET['start'] : date('Y-m-01');
$endDate   = isset($_GET['end'])   ? $_GET['end']   : date('Y-m-d');

// --- 1. QUERY UTAMA ---
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
        $row['cost'] = $row['gross'] * 0.60; 
        $reportData[] = $row;
    }
}

// --- 2. HITUNG TOTAL KPI ---
$totalGross = 0; $totalCost = 0; $totalTrx = 0; $totalSold = 0;

foreach ($reportData as $d) {
    $totalGross += $d['gross'];
    $totalCost  += $d['cost'];
    $totalTrx   += $d['trx'];
    $totalSold  += $d['sold'];
}

$totalNet = $totalGross - $totalCost;
$avgOrder = ($totalTrx > 0) ? $totalGross / $totalTrx : 0;

// --- 3. ANALISIS OTOMATIS ---
$queryTopItem = "
    SELECT m.name, SUM(od.quantity) as total_qty
    FROM order_details od
    JOIN menu_items m ON od.menu_item_id = m.id
    JOIN orders o ON od.order_id = o.id
    WHERE o.status != 'cancelled' 
    AND DATE(o.created_at) BETWEEN '$startDate' AND '$endDate'
    GROUP BY m.id
    ORDER BY total_qty DESC LIMIT 1
";
$resTop = mysqli_query($conn, $queryTopItem);
$topItemData = mysqli_fetch_assoc($resTop);
$topItemName = $topItemData ? $topItemData['name'] : "Belum ada data";

$analisis_text = "Total pendapatan tercatat <strong>Rp " . number_format($totalGross, 0, ',', '.') . "</strong>. ";
if ($avgOrder > 50000) {
    $analisis_text .= "Rata-rata order sangat sehat (<strong>Rp " . number_format($avgOrder, 0, ',', '.') . "</strong>). ";
} else {
    $analisis_text .= "Rata-rata order stabil (<strong>Rp " . number_format($avgOrder, 0, ',', '.') . "</strong>). ";
}
if ($topItemName != "Belum ada data") {
    $analisis_text .= "Menu terlaris: <strong>" . $topItemName . "</strong>.";
}

// --- 4. PAGINATION ---
$limit = 10; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

$totalRows = count($reportData);
$totalPages = ceil($totalRows / $limit);
if ($page > $totalPages && $totalPages > 0) $page = $totalPages;

$offset = ($page - 1) * $limit;
$tableData = array_slice($reportData, $offset, $limit);

$showStart = ($totalRows > 0) ? $offset + 1 : 0;
$showEnd   = min($offset + $limit, $totalRows);
$urlParams = "&start=$startDate&end=$endDate";
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - Street Sushi</title>
    <link rel="stylesheet" href="../assets/css/dashboard/page.css">
    <link rel="stylesheet" href="../assets/css/dashboard/laporan.css">
    <link rel="shortcut icon" href="../assets/images/favicon.ico" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>
    <div class="wrapper">
        <?php include '../assets/components/sidebar.php'; ?>
        <div class="main-content">
            <?php include '../assets/components/topbar.php'; ?>
            <div class="content-padding">

                <div class="report-header">
                    <div class="page-header-title">
                        <h1>Laporan Penjualan</h1>
                        <p>Periode: <?= date('d M Y', strtotime($startDate)) ?> - <?= date('d M Y', strtotime($endDate)) ?></p>
                    </div>
                    <form method="GET" action="" class="filter-wrapper">
                        <div class="date-group">
                            <i class='bx bx-calendar' style="color: var(--text-muted);"></i>
                            <input type="date" name="start" value="<?= $startDate ?>" onchange="this.form.submit()">
                            <span class="separator">-</span>
                            <input type="date" name="end" value="<?= $endDate ?>" onchange="this.form.submit()">
                        </div>
                        <button type="button" class="btn-export" onclick="window.print()">
                            <i class='bx bxs-file-pdf'></i> Export
                        </button>
                    </form>
                </div>

                <div class="kpi-grid">
                    <div class="kpi-card">
                        <div class="kpi-title">Pendapatan Kotor</div>
                        <div class="kpi-value">Rp <?= number_format($totalGross / 1000000, 1, ',', '.') ?> Jt</div>
                        <div class="kpi-trend text-green"><i class='bx bx-up-arrow-alt'></i> +12.5%</div>
                    </div>
                    <div class="kpi-card">
                        <div class="kpi-title">Laba Bersih</div>
                        <div class="kpi-value" style="color: #00b894;">Rp <?= number_format($totalNet / 1000000, 1, ',', '.') ?> Jt</div>
                        <div class="kpi-trend" style="color: var(--text-muted);">Margin 40%</div>
                    </div>
                    <div class="kpi-card">
                        <div class="kpi-title">Total Transaksi</div>
                        <div class="kpi-value"><?= $totalTrx ?></div>
                        <div class="kpi-trend text-red"><i class='bx bx-down-arrow-alt'></i> -2%</div>
                    </div>
                    <div class="kpi-card">
                        <div class="kpi-title">Rata-rata Order</div>
                        <div class="kpi-value">Rp <?= number_format($avgOrder, 0, ',', '.') ?></div>
                        <div class="kpi-trend" style="color: var(--text-muted);">Per pelanggan</div>
                    </div>
                    <div class="kpi-card">
                        <div class="kpi-title">Item Terjual</div>
                        <div class="kpi-value"><?= $totalSold ?> <span style="font-size:0.8rem; font-weight:400;">Porsi</span></div>
                        <div class="kpi-trend text-green"><i class='bx bxs-hot'></i> Best: Salmon Roll</div>
                    </div>
                    <div class="kpi-card">
                        <div class="kpi-title">Dine-in vs Online</div>
                        <div class="kpi-value">70%</div>
                        <div class="kpi-trend" style="color: var(--text-muted);">Makan di tempat</div>
                    </div>
                </div>

                <div class="analysis-section">
                    <div class="analysis-header">
                        <h3><i class='bx bxs-analyse'></i> Analisis Singkat</h3>
                    </div>
                    <div class="analysis-text">
                        <p><?= $analisis_text ?></p>
                    </div>
                </div>

                <div class="table-minimal-wrapper">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th class="text-center">Tanggal</th>
                                <th class="text-center">Order</th>
                                <th class="text-center">Item</th>
                                <th class="text-right">Gross</th>
                                <th class="text-right">HPP</th>
                                <th class="text-right">Net</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($tableData)): ?>
                                <tr><td colspan="6" class="text-center" style="padding: 30px;">Tidak ada data.</td></tr>
                            <?php else: ?>
                                <?php foreach ($tableData as $row): $net = $row['gross'] - $row['cost']; ?>
                                    <tr>
                                        <td class="text-center"><?= date('d M Y', strtotime($row['date'])) ?></td>
                                        <td class="text-center num-font"><?= $row['trx'] ?></td>
                                        <td class="text-center num-font"><?= $row['sold'] ?></td>
                                        <td class="text-right num-font">Rp <?= number_format($row['gross'], 0, ',', '.') ?></td>
                                        <td class="text-right num-font" style="opacity: 0.7;">Rp <?= number_format($row['cost'], 0, ',', '.') ?></td>
                                        <td class="text-right num-font" style="color: #00b894;">Rp <?= number_format($net, 0, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            
                            <tr class="row-total">
                                <td colspan="3" class="text-right" style="padding-right: 20px;">TOTAL (PERIODE INI)</td>
                                <td class="text-right num-font">Rp <?= number_format($totalGross, 0, ',', '.') ?></td>
                                <td class="text-right num-font">Rp <?= number_format($totalCost, 0, ',', '.') ?></td>
                                <td class="text-right num-font" style="color: #00b894;">Rp <?= number_format($totalNet, 0, ',', '.') ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <?php if ($totalRows > 0): ?>
                    <div class="pagination-external">
                        <span class="pagination-info">Menampilkan <?= $showStart ?> - <?= $showEnd ?> dari <?= $totalRows ?> data</span>
                        
                        <div class="pagination-nav">
                            <a href="?page=<?= $page - 1 ?><?= $urlParams ?>" class="page-link <?= ($page <= 1) ? 'disabled' : '' ?>">
                                <i class='bx bx-chevron-left'></i>
                            </a>

                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <?php if ($i == 1 || $i == $totalPages || ($i >= $page - 1 && $i <= $page + 1)): ?>
                                    <a href="?page=<?= $i ?><?= $urlParams ?>" class="page-link <?= ($i == $page) ? 'active' : '' ?>">
                                        <?= $i ?>
                                    </a>
                                <?php elseif ($i == $page - 2 || $i == $page + 2): ?>
                                    <span class="dots">...</span>
                                <?php endif; ?>
                            <?php endfor; ?>

                            <a href="?page=<?= $page + 1 ?><?= $urlParams ?>" class="page-link <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                                <i class='bx bx-chevron-right'></i>
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
    <div class="mobile-overlay"></div>
    <script>
        const sidebar = document.querySelector('.sidebar');
        const toggleBtn = document.querySelector('.toggle-btn');
        const overlay = document.querySelector('.mobile-overlay');
        if (overlay) {
            toggleBtn.addEventListener('click', () => {
                if (window.innerWidth > 768) { sidebar.classList.toggle('close'); } 
                else { sidebar.classList.toggle('active'); overlay.classList.toggle('active'); sidebar.classList.remove('close'); }
            });
            overlay.addEventListener('click', () => { sidebar.classList.remove('active'); overlay.classList.remove('active'); });
        }
    </script>
</body>
</html>