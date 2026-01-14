<?php
include '../config/logic/laporan_logic.php'; // Include Logic di sini
$pageTitle = "Laporan Operasional";
$currentPage = "laporan";
ob_start();
?>

<?php $customCSS = '<link rel="stylesheet" href="../assets/css/dashboard/laporan.css">'; ?>

<div class="report-header">
    <div class="page-header-title">
        <h1>Laporan Penjualan</h1>
        <p>Periode: <?= date('d M Y', strtotime($startDate)) ?> - <?= date('d M Y', strtotime($endDate)) ?></p>
    </div>
    <form method="GET" action="" class="filter-wrapper">
        <div class="date-group">
            <i class='bx bx-calendar'></i>
            <input type="date" name="start" value="<?= $startDate ?>" onchange="this.form.submit()">
            <span class="separator">-</span>
            <input type="date" name="end" value="<?= $endDate ?>" onchange="this.form.submit()">
        </div>
        <button type="button" class="btn-export" onclick="window.print()">
            <i class='bx bxs-file-pdf'></i> Export PDF
        </button>
    </form>
</div>

<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-title">Pendapatan Kotor</div>
        <div class="kpi-value">Rp <?= number_format($totalGross / 1000000, 1, ',', '.') ?> Jt</div>
        <div class="kpi-trend text-green"><i class='bx bx-trending-up'></i> Gross Revenue</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-title">Laba Bersih (Est.)</div>
        <div class="kpi-value" style="color: #00b894;">Rp <?= number_format($totalNet / 1000000, 1, ',', '.') ?> Jt</div>
        <div class="kpi-trend" style="color: var(--text-muted);">Margin ~40%</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-title">Rata-rata Order</div>
        <div class="kpi-value">Rp <?= number_format($avgOrder, 0, ',', '.') ?></div>
        <div class="kpi-trend" style="color: var(--text-muted);">Per Pelanggan</div>
    </div>
    
    <div class="kpi-card">
        <div class="kpi-title">Total Transaksi</div>
        <div class="kpi-value"><?= $totalTrx ?></div>
        <div class="kpi-trend text-green"><i class='bx bx-check-circle'></i> Selesai</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-title">Item Terjual</div>
        <div class="kpi-value"><?= $totalSold ?> <span style="font-size:1rem; font-weight:400; color:var(--text-muted);">Porsi</span></div>
        <div class="kpi-trend text-brand" style="color: var(--brand-primary);"><i class='bx bxs-hot'></i> Best: <?= substr($topItemName, 0, 15) ?></div>
    </div>
    <div class="kpi-card">
        <div class="kpi-title">Dine-in vs Online</div>
        <div class="kpi-value">70%</div>
        <div class="kpi-trend" style="color: var(--text-muted);">Makan di tempat</div>
    </div>
</div>

<div class="analysis-section">
    <div class="analysis-header">
        <h3><i class='bx bxs-analyse'></i> Analisis Performa Periode Ini</h3>
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
                <th class="text-right">Gross (IDR)</th>
                <th class="text-right">HPP (Est.)</th>
                <th class="text-right">Net Profit</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($reportData)): ?>
                <tr><td colspan="6" class="text-center" style="padding: 30px;">Tidak ada data pada periode ini.</td></tr>
            <?php else: ?>
                <?php foreach ($reportData as $row): $net = $row['gross'] - $row['cost']; ?>
                    <tr>
                        <td class="text-center"><?= date('d M Y', strtotime($row['date'])) ?></td>
                        <td class="text-center num-font"><?= $row['trx'] ?></td>
                        <td class="text-center num-font"><?= $row['sold'] ?></td>
                        <td class="text-right num-font">Rp <?= number_format($row['gross'], 0, ',', '.') ?></td>
                        <td class="text-right num-font" style="opacity: 0.6;">Rp <?= number_format($row['cost'], 0, ',', '.') ?></td>
                        <td class="text-right num-font" style="color: #00b894; font-weight:600;">Rp <?= number_format($net, 0, ',', '.') ?></td>
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

<?php
$content = ob_get_clean();
include 'layouts.php';
?>