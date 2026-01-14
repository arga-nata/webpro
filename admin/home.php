<?php
// Masukkan Logika PHP (Statistik, Pesanan) yang sudah kita bahas sebelumnya
include '../config/logic/home_logic.php';

$pageTitle = "Home Dashboard";
$currentPage = "home";

// Ambil Nama Admin (Opsional)
$adminName = isset($_SESSION['admin_user']) ? ucfirst($_SESSION['admin_user']) : 'Chef';

ob_start();
?>

<?php $customCSS = '<link rel="stylesheet" href="../assets/css/dashboard/home.css">'; ?>

<div class="hero-card">

    <div class="hero-content">
        <h1 class="hero-greeting">Halo, <span><?= $adminName; ?>!</span></h1>
        <p class="hero-subtitle">
            Restoran berjalan lancar. Siap memantau pesanan?
        </p>

        <div class="shift-badge">
            <i class='bx bx-store-alt'></i> Cabang Utama &nbsp;|&nbsp; <?= $current_shift_name ?>
        </div>
    </div>

    <div class="hero-status-box">
        <span class="hero-date"><?= date('l, d F Y'); ?></span>
        <div id="clock" class="hero-clock">00:00</div>

        <?php
        // Logika Sederhana Status Buka/Tutup
        $jam = (int)date('H');
        $isOpen = ($jam >= 10 && $jam < 22); // Buka jam 10 - 22
        ?>

        <div class="status-pill <?= $isOpen ? 'open' : 'closed'; ?>">
            <div class="pulsing-dot"></div>
            <?= $isOpen ? 'OPERASIONAL BUKA' : 'OPERASIONAL TUTUP'; ?>
        </div>
    </div>
</div>

<div class="stats-grid">
    <div class="card-stat">
        <div class="icon-box" style="background: rgba(30, 144, 255, 0.1); color: #1e90ff;">
            <i class='bx bx-import'></i>
        </div>
        <div>
            <h3 style="font-size: 1.8rem;"><?= $stats['masuk_today']; ?></h3>
            <span class="text-muted">Total Masuk</span>
        </div>
    </div>

    <div class="card-stat" style="border: 1px solid rgba(255, 165, 2, 0.3);">
        <div class="icon-box" style="background: rgba(255, 165, 2, 0.1); color: #ffa502;">
            <i class='bx bx-loader-circle'></i>
        </div>
        <div>
            <h3 style="font-size: 1.8rem; color: #ffa502;"><?= $stats['aktif_sekarang']; ?></h3>
            <span class="text-muted">Sedang Proses</span>
        </div>
    </div>

    <div class="card-stat">
        <div class="icon-box icon-success"><i class='bx bx-check-double'></i></div>
        <div>
            <h3 style="font-size: 1.8rem;"><?= $stats['selesai_today']; ?></h3>
            <span class="text-muted">Selesai</span>
        </div>
    </div>

    <div class="card-stat">
        <div class="icon-box icon-danger"><i class='bx bx-x-circle'></i></div>
        <div>
            <h3 style="font-size: 1.8rem;"><?= $stats['batal_today']; ?></h3>
            <span class="text-muted">Dibatalkan</span>
        </div>
    </div>
</div>

<?php
$alert_count = 0;
// Cek jika ada pesanan pending lebih dari 10 menit
foreach ($active_orders as $ao) {
    if ($ao['status'] == 'pending' && $ao['durasi'] > 10) $alert_count++;
}
?>

<?php if ($alert_count > 0): ?>
    <div class="card-glass" style="background: rgba(255, 71, 87, 0.1); border: 1px solid var(--color-error); padding: 15px 20px; display: flex; align-items: center; gap: 15px; margin-bottom: 25px;">
        <i class='bx bxs-megaphone' style="font-size: 1.5rem; color: var(--color-error); animation: wobble 2s infinite;"></i>
        <div>
            <strong style="color: var(--color-error); font-size: 1rem;">PERHATIAN DIPERLUKAN</strong>
            <p style="font-size: 0.9rem; margin: 0; color: var(--text-main);">
                Terdapat <b><?= $alert_count; ?> Pesanan Pending</b> yang belum direspon lebih dari 10 menit.
            </p>
        </div>
    </div>
<?php endif; ?>

<div class="card-glass">
    <div class="card-header">
        <h3><i class='bx bx-list-ul'></i> Antrian Aktif</h3>
        <a href="home.php" class="btn btn-sm btn-primary" style="background: rgba(255,255,255,0.05); border: none;"><i class='bx bx-refresh'></i> Refresh</a>
    </div>

    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>Pelanggan</th>
                    <th>Menu Dipesan</th>
                    <th>Status</th>
                    <th>Aksi Cepat</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($active_orders)): ?>
                    <tr>
                        <td colspan="5" style="text-align:center; padding: 50px; color: var(--text-muted);">
                            <img src="../assets/images/logo.png" style="width: 50px; opacity: 0.5; margin-bottom: 15px; filter: grayscale(1);">
                            <br>
                            Tidak ada pesanan aktif. Dapur bisa istirahat sejenak.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($active_orders as $o): ?>
                        <tr style="<?= ($o['status'] == 'pending' && $o['durasi'] > 10) ? 'background: rgba(255,71,87,0.05);' : ''; ?>">
                            <td>
                                <span style="font-weight: 700; color: var(--text-main); font-size: 1rem;">
                                    <?= date('H:i', strtotime($o['created_at'])); ?>
                                </span>
                                <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 4px;">
                                    <i class='bx bx-time'></i> <?= $o['durasi']; ?> mnt lalu
                                </div>
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($o['customer_name']); ?></strong><br>
                                <span class="badge" style="background: rgba(255,255,255,0.05); color: #aaa; margin-top: 5px; display:inline-block;">
                                    #<?= str_pad($o['id'], 3, '0', STR_PAD_LEFT); ?>
                                </span>
                            </td>
                            <td style="max-width:300px;">
                                <div style="margin-bottom: 5px;"><?= $o['menu_list']; ?></div>
                                <small style="color: var(--color-success); font-weight: 700; background: rgba(0,184,148,0.1); padding: 2px 6px; border-radius: 4px;">
                                    Rp <?= number_format($o['total_amount'], 0, ',', '.'); ?>
                                </small>
                            </td>
                            <td>
                                <span class="badge <?= $o['status']; ?>" style="font-size: 0.8rem; padding: 6px 12px;">
                                    <?= ucfirst($o['status']); ?>
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <?php if ($o['status'] == 'pending'): ?>
                                        <form action="" method="POST">
                                            <input type="hidden" name="order_id" value="<?= $o['id']; ?>">
                                            <input type="hidden" name="action" value="accept">
                                            <button type="submit" class="btn btn-sm btn-acc" title="Terima"><i class='bx bx-check'></i></button>
                                        </form>
                                        <button class="btn btn-sm btn-rej" title="Tolak" onclick="openRejectModal('<?= $o['id']; ?>')"><i class='bx bx-x'></i></button>
                                    <?php elseif ($o['status'] == 'cooking'): ?>
                                        <form action="" method="POST">
                                            <input type="hidden" name="order_id" value="<?= $o['id']; ?>">
                                            <input type="hidden" name="action" value="deliver">
                                            <button type="submit" class="btn btn-sm btn-primary" title="Antar"><i class='bx bxs-truck'></i> Antar</button>
                                        </form>
                                    <?php elseif ($o['status'] == 'delivery'): ?>
                                        <form action="" method="POST">
                                            <input type="hidden" name="order_id" value="<?= $o['id']; ?>">
                                            <input type="hidden" name="action" value="complete">
                                            <button type="submit" class="btn btn-sm btn-acc" title="Selesai"><i class='bx bxs-check-circle'></i> Selesai</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../assets/components/modal_reject.php'; ?>

<script>
    function updateClock() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('en-GB', {
            hour: '2-digit',
            minute: '2-digit'
        });
        document.getElementById('clock').textContent = timeString;
    }
    setInterval(updateClock, 1000);
    updateClock();
</script>

<?php
$content = ob_get_clean();
include 'layouts.php';
?>