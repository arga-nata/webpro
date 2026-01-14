<?php
session_start();
include 'config/db.php';

$pageTitle = "Lacak Pesanan - Street Sushi";
$currentPage = "track";
include 'includes/header.php';

$view_mode = 'search'; // Opsi: search, list, detail
$orders_list = [];
$order_detail = null;
$order_items = [];
$error_msg = "";
$search_value = "";

// 1. LOGIKA UNTUK DETAIL (Jika user klik tombol 'Lihat Detail')
if (isset($_GET['order_id'])) {
    $order_id = (int) $_GET['order_id'];
    $q_detail = mysqli_query($conn, "SELECT * FROM orders WHERE id = $order_id");

    if (mysqli_num_rows($q_detail) > 0) {
        $view_mode = 'detail';
        $order_detail = mysqli_fetch_assoc($q_detail);
        if (isset($_GET['keyword']) && !empty($_GET['keyword'])) {
            $search_value = $_GET['keyword'];
        } else {
            $search_value = $order_detail['customer_phone'];
        }

        $q_items = mysqli_query($conn, "
            SELECT od.*, m.name as menu_name, v.name as variant_name, a.name as addon_name 
            FROM order_details od
            JOIN menu_items m ON od.menu_item_id = m.id
            LEFT JOIN variants v ON od.variant_id = v.id
            LEFT JOIN addons a ON od.addon_id = a.id
            WHERE od.order_id = $order_id
        ");
        while ($row = mysqli_fetch_assoc($q_items)) {
            $order_items[] = $row;
        }
    }
}
// 2. LOGIKA UNTUK PENCARIAN (Tampilkan List)
elseif (isset($_GET['keyword']) && !empty($_GET['keyword'])) {
    $keyword = mysqli_real_escape_string($conn, $_GET['keyword']);
    $search_value = $_GET['keyword'];

    $query_list = "SELECT * FROM orders 
                   WHERE customer_phone = '$keyword' 
                   ORDER BY created_at DESC";

    $result_list = mysqli_query($conn, $query_list);

    if (mysqli_num_rows($result_list) > 0) {
        $view_mode = 'list';
        while ($row = mysqli_fetch_assoc($result_list)) {
            $orders_list[] = $row;
        }
    } else {
        $error_msg = "Riwayat pesanan tidak ditemukan untuk No. WhatsApp tersebut.";
    }
}

// Helper status steps
$status_steps = ['pending' => 1, 'confirmed' => 2, 'cooking' => 3, 'delivery' => 4, 'completed' => 5];
$current_step = 0;
if ($order_detail && isset($status_steps[$order_detail['status']])) {
    $current_step = $status_steps[$order_detail['status']];
}
?>

<style>
    /* UPDATE: Lebar container ditambah biar Grid muat 2 kolom */
    .track-container {
        max-width: 800px;
        margin: 120px auto 60px;
        padding: 20px;
        color: white;
    }

    .search-box {
        max-width: 500px;
        margin: 0 auto 30px;
        /* Search box tetep di tengah kecil */
        background: rgba(255, 255, 255, 0.05);
        padding: 30px;
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        text-align: center;
    }

    .search-input-group {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }

    .search-input {
        flex: 1;
        padding: 12px 20px;
        border-radius: 50px;
        border: none;
        background: rgba(0, 0, 0, 0.3);
        color: white;
        font-size: 1rem;
        outline: none;
    }

    .btn-track {
        background: var(--accent);
        color: white;
        border: none;
        padding: 12px 25px;
        border-radius: 50px;
        cursor: pointer;
        font-weight: bold;
        transition: 0.3s;
    }

    .btn-track:hover {
        transform: scale(1.05);
    }

    /* UPDATE: INI CSS GRID YANG HILANG TADI */
    .order-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        /* Responsif */
        gap: 20px;
    }

    /* CARD STYLE */
    .list-card {
        background: #1a1a1a;
        border-radius: 12px;
        padding: 20px;
        border: 1px solid #333;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: 0.3s;
        height: 100%;
        /* Agar tinggi kartu sama rata */
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
    }

    .list-card:hover {
        border-color: var(--accent);
        transform: translateY(-5px);
    }

    .list-info h4 {
        margin: 0 0 5px;
        font-size: 1.1rem;
    }

    .list-info p {
        margin: 0;
        color: #888;
        font-size: 0.9rem;
    }

    /* Footer kartu biar tombol ada di bawah */
    .list-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 15px;
        border-top: 1px dashed #333;
        padding-top: 10px;
    }

    .btn-detail {
        padding: 8px 15px;
        background: rgba(255, 255, 255, 0.1);
        color: white;
        text-decoration: none;
        border-radius: 50px;
        font-size: 0.85rem;
        transition: 0.3s;
    }

    .btn-detail:hover {
        background: var(--accent);
    }

    /* DETAIL STYLES */
    .result-card {
        max-width: 600px;
        margin: 0 auto;
        /* Detail view tetep ramping */
        background: #1a1a1a;
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid #333;
        animation: slideUp 0.5s ease;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .status-header {
        background: #252525;
        padding: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #333;
    }

    .status-badge {
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: bold;
        text-transform: uppercase;
    }

    .status-pending {
        background: #f1c40f;
        color: black;
    }

    .status-confirmed {
        background: #3498db;
        color: white;
    }

    .status-cooking {
        background: #e67e22;
        color: white;
    }

    .status-delivery {
        background: #9b59b6;
        color: white;
    }

    .status-completed {
        background: #2ecc71;
        color: white;
    }

    .status-cancelled {
        background: #e74c3c;
        color: white;
    }

    .timeline {
        display: flex;
        justify-content: space-between;
        padding: 30px 20px;
        position: relative;
    }

    .timeline::before {
        content: '';
        position: absolute;
        top: 40px;
        left: 40px;
        right: 40px;
        height: 4px;
        background: #333;
        z-index: 0;
    }

    .step {
        position: relative;
        z-index: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 20%;
    }

    .step-icon {
        width: 25px;
        height: 25px;
        background: #333;
        border-radius: 50%;
        margin-bottom: 10px;
        transition: 0.3s;
        border: 4px solid #1a1a1a;
    }

    .step.active .step-icon {
        background: var(--accent);
        box-shadow: 0 0 10px var(--accent);
    }

    .step p {
        font-size: 0.7rem;
        color: #777;
        margin: 0;
    }

    .step.active p {
        color: white;
        font-weight: bold;
    }

    .order-details {
        padding: 20px;
        background: rgba(0, 0, 0, 0.2);
    }

    .detail-item {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px dashed #333;
        font-size: 0.9rem;
    }

    .err-msg {
        color: #ff6b6b;
        margin-top: 15px;
        display: block;
    }

    .back-link {
        display: inline-block;
        margin-bottom: 15px;
        color: #aaa;
        text-decoration: none;
    }

    .back-link:hover {
        color: white;
    }
</style>

<div class="track-container">

    <div class="search-box">
        <h2><i class='bx bx-search-alt'></i> Lacak Pesanan</h2>
        <p>Masukkan No. WhatsApp (Contoh: 0812xxx)</p>

        <form method="GET" action="">
            <div class="search-input-group">
                <input type="text" name="keyword" class="search-input" placeholder="Masukkan No. WhatsApp..."
                    value="<?= htmlspecialchars($search_value) ?>" required>
                <button type="submit" class="btn-track">Cari</button>
            </div>
        </form>
        <?php if ($error_msg): ?>
            <span class="err-msg"><?= $error_msg ?></span>
        <?php endif; ?>
    </div>

    <?php if ($view_mode == 'list'): ?>
        <h3 style="margin-bottom: 20px; text-align:center;">Ditemukan <?= count($orders_list) ?> Riwayat Pesanan:</h3>

        <div class="order-grid">
            <?php foreach ($orders_list as $o): ?>
                <div class="list-card">
                    <div class="list-info">
                        <h4>Order #<?= $o['id'] ?> <span class="status-badge status-<?= strtolower($o['status']) ?>"
                                style="font-size: 0.6rem; padding: 2px 8px; margin-left: 5px;"><?= $o['status'] ?></span></h4>
                        <p><i class='bx bx-calendar'></i> <?= date('d M Y, H:i', strtotime($o['created_at'])) ?></p>
                    </div>

                    <div class="list-footer">
                        <p style="color: var(--accent); font-weight: bold; margin: 0;">
                            Rp <?= number_format($o['total_amount'], 0, ',', '.') ?>
                        </p>
                        <a href="track.php?order_id=<?= $o['id'] ?>&keyword=<?= $_GET['keyword'] ?>" class="btn-detail">
                            Lihat Status <i class='bx bx-right-arrow-alt'></i>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>


    <?php if ($view_mode == 'detail' && $order_detail): ?>

        <?php if (isset($_GET['keyword'])): ?>
            <a href="track.php?keyword=<?= htmlspecialchars($_GET['keyword']) ?>" class="back-link">
                <i class='bx bx-arrow-back'></i> Kembali ke List
            </a>
        <?php endif; ?>

        <div class="result-card">
            <div class="status-header">
                <div>
                    <h3 style="margin:0;">Order #<?= $order_detail['id'] ?></h3>
                    <small style="color:#aaa;"><?= date('d M Y H:i', strtotime($order_detail['created_at'])) ?></small>
                </div>
                <span class="status-badge status-<?= strtolower($order_detail['status']) ?>">
                    <?= $order_detail['status'] ?>
                </span>
            </div>

            <?php if ($order_detail['status'] != 'cancelled'): ?>
                <div class="timeline">
                    <div class="step <?= $current_step >= 1 ? 'active' : '' ?>">
                        <div class="step-icon"></div>
                        <p>Pending</p>
                    </div>
                    <div class="step <?= $current_step >= 2 ? 'active' : '' ?>">
                        <div class="step-icon"></div>
                        <p>Confirm</p>
                    </div>
                    <div class="step <?= $current_step >= 3 ? 'active' : '' ?>">
                        <div class="step-icon"></div>
                        <p>Cooking</p>
                    </div>
                    <div class="step <?= $current_step >= 4 ? 'active' : '' ?>">
                        <div class="step-icon"></div>
                        <p>Delivery</p>
                    </div>
                    <div class="step <?= $current_step >= 5 ? 'active' : '' ?>">
                        <div class="step-icon"></div>
                        <p>Done</p>
                    </div>
                </div>
            <?php else: ?>
                <div style="padding: 20px; text-align: center; color: #e74c3c;">
                    <i class='bx bxs-x-circle' style="font-size: 3rem;"></i>
                    <p>Pesanan ini telah dibatalkan.</p>
                </div>
            <?php endif; ?>

            <div class="order-details">

                <div
                    style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px; border-bottom: 1px dashed #444; padding-bottom: 20px;">

                    <div>
                        <small style="color: #888; display: block; margin-bottom: 2px;">Pemesan:</small>
                        <span style="font-weight: bold; font-size: 0.95rem;">
                            <i class='bx bx-user'></i> <?= htmlspecialchars($order_detail['customer_name']) ?>
                        </span>
                        <br>
                        <span style="font-size: 0.85rem; color: #ccc;">
                            <i class='bx bx-phone'></i> <?= htmlspecialchars($order_detail['customer_phone']) ?>
                        </span>
                    </div>

                    <div style="text-align: right;">
                        <small style="color: #888; display: block; margin-bottom: 2px;">Pembayaran:</small>
                        <span
                            style="font-weight: bold; font-size: 0.95rem; text-transform: uppercase; color: var(--accent);">
                            <?= $order_detail['payment_method'] ?>
                        </span>

                        <?php if (!empty($order_detail['payment_proof'])): ?>
                            <div style="margin-top: 5px;">
                                <a href="uploads/<?= $order_detail['payment_proof'] ?>" target="_blank"
                                    style="font-size: 0.75rem; background: #333; color: white; padding: 3px 8px; border-radius: 4px; text-decoration: none;">
                                    <i class='bx bx-image'></i> Lihat Bukti
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>

                <h4 style="margin-bottom: 15px; font-size: 1rem; color: #ddd;">Rincian Menu</h4>

                <?php foreach ($order_items as $item): ?>
                    <div class="detail-item">
                        <span>
                            <b><?= $item['quantity'] ?>x</b> <?= $item['menu_name'] ?>
                            <small style="color: #888; display: block; font-size: 0.8rem;">
                                <?= $item['variant_name'] ? $item['variant_name'] : 'Original' ?>
                                <?= $item['addon_name'] ? '+ ' . $item['addon_name'] : '' ?>
                                <?php if (!empty($item['note'])): ?>
                                    <i style="display:block; color: #aaa;">"<?= htmlspecialchars($item['note']) ?>"</i>
                                <?php endif; ?>
                            </small>
                        </span>
                        <span>Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></span>
                    </div>
                <?php endforeach; ?>

                <div class="detail-item"
                    style="margin-top: 10px; font-size: 1.1rem; border-top: 1px solid #444; padding-top: 15px; border-bottom: none;">
                    <strong>TOTAL</strong>
                    <strong style="color: var(--accent);">Rp
                        <?= number_format($order_detail['total_amount'], 0, ',', '.') ?></strong>
                </div>

                <div style="margin-top: 20px; background: rgba(255,255,255,0.05); padding: 15px; border-radius: 10px;">

                    <div style="margin-bottom: 10px;">
                        <i class='bx bxs-shopping-bag' style="color: var(--accent);"></i>
                        <strong>Tipe Pesanan:</strong>
                        <span style="float: right;">
                            <?= ($order_detail['order_type'] == 'pickup') ? 'Ambil Sendiri' : 'Delivery (Dikirim)' ?>
                        </span>
                    </div>

                    <?php if ($order_detail['order_type'] != 'pickup'): ?>
                        <div style="margin-bottom: 10px;">
                            <i class='bx bxs-map' style="color: var(--accent);"></i>
                            <strong>Alamat Pengiriman:</strong>
                            <p style="margin: 5px 0 0 22px; font-size: 0.9rem; color: #ccc; line-height: 1.4;">
                                <?= htmlspecialchars($order_detail['delivery_address']) ?>
                            </p>
                        </div>
                    <?php endif; ?>

                    <div>
                        <i class='bx bxs-note' style="color: var(--accent);"></i>
                        <strong>Catatan:</strong>
                        <p style="margin: 5px 0 0 22px; font-size: 0.9rem; color: #ccc;">
                            <?= $order_detail['notes_general'] ? htmlspecialchars($order_detail['notes_general']) : '-' ?>
                        </p>
                    </div>

                </div>
            </div>
        <?php endif; ?>

    </div>

    <?php include 'includes/footer.php'; ?>