<?php
session_start();
include 'config/db.php';

$pageTitle = "Order Station - Street Sushi";
$currentPage = "order";

// LOGIKA POPUP SUKSES
$order_data = null;
$order_items = [];

if (isset($_GET['status']) && $_GET['status'] == 'success' && isset($_GET['id'])) {
    $order_id = (int) $_GET['id'];

    $q_order = mysqli_query($conn, "SELECT * FROM orders WHERE id = $order_id");
    if (mysqli_num_rows($q_order) > 0) {
        $order_data = mysqli_fetch_assoc($q_order);

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

include 'includes/header.php';

// DATA UNTUK FORM
$cat_query = "SELECT * FROM categories ORDER BY name ASC";
$cat_result = mysqli_query($conn, $cat_query);
$categories = [];
while ($row = mysqli_fetch_assoc($cat_result))
    $categories[] = $row;

$menu_query = "SELECT id, category_id, name, base_price FROM menu_items ORDER BY name ASC";
$menu_result = mysqli_query($conn, $menu_query);
$all_menus = [];
while ($row = mysqli_fetch_assoc($menu_result))
    $all_menus[] = $row;

$variant_query = "SELECT * FROM variants";
$variant_result = mysqli_query($conn, $variant_query);
$variants = [];
while ($row = mysqli_fetch_assoc($variant_result))
    $variants[] = $row;

$addon_query = "SELECT * FROM addons";
$addon_result = mysqli_query($conn, $addon_query);
$addons = [];
while ($row = mysqli_fetch_assoc($addon_result))
    $addons[] = $row;

$preselect_menu_id = isset($_GET['add']) ? (int) $_GET['add'] : 0;
$preselect_cat_id = 0;

// Cari kategori dari menu yang dipilih
if ($preselect_menu_id > 0) {
    foreach ($all_menus as $menu) {
        if ($menu['id'] == $preselect_menu_id) {
            $preselect_cat_id = $menu['category_id'];
            break;
        }
    }
}
?>

<link rel="stylesheet" href="assets/css/order.css">

<style>
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.85);
        backdrop-filter: blur(5px);
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s ease;
    }

    .modal-overlay.active {
        opacity: 1;
        pointer-events: auto;
    }

    .receipt-modal {
        background: var(--card-bg);
        width: 90%;
        max-width: 380px;
        /* LEBIH KECIL & PROPORSIONAL */
        border-radius: 16px;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
        transform: scale(0.9);
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        display: flex;
        flex-direction: column;
        max-height: 85vh;
        /* Biar gak mentok atas bawah di HP */
        overflow: hidden;
    }

    .modal-overlay.active .receipt-modal {
        transform: scale(1);
    }

    /* HEADER COMPACT */
    .receipt-header {
        background: var(--accent);
        padding: 20px 15px;
        /* Padding dikurangi */
        text-align: center;
        color: white;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .success-icon-box {
        width: 50px;
        height: 50px;
        /* Icon lebih kecil */
        background: white;
        color: var(--accent);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        margin-bottom: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        animation: popIcon 0.4s 0.2s cubic-bezier(0.34, 1.56, 0.64, 1) backwards;
    }

    @keyframes popIcon {
        from {
            transform: scale(0);
            opacity: 0;
        }

        to {
            transform: scale(1);
            opacity: 1;
        }
    }

    .receipt-header h2 {
        font-family: 'Dela Gothic One';
        margin: 0;
        font-size: 1.2rem;
        /* Font Judul disesuaikan */
        line-height: 1.2;
    }

    /* BODY STRUK */
    .receipt-body {
        padding: 20px;
        background: var(--bg-color);
        flex: 1;
        overflow-y: auto;
        /* Scroll cuma di sini */
        position: relative;
        font-size: 0.9rem;
    }

    /* Hiasan Gerigi */
    .receipt-body::before {
        content: "";
        position: absolute;
        top: -8px;
        left: 0;
        width: 100%;
        height: 16px;
        background: radial-gradient(circle, transparent 0.4em, var(--bg-color) 0.4em) 0 0;
        background-size: 0.8em 0.8em;
        background-repeat: repeat-x;
        transform: rotate(180deg);
    }

    .info-group {
        margin-bottom: 15px;
        border-bottom: 2px dashed var(--border-color);
        padding-bottom: 10px;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 5px;
    }

    .info-row strong {
        font-weight: 700;
    }

    .item-list {
        margin-bottom: 15px;
    }

    .item-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
    }

    .item-left {
        flex: 1;
        padding-right: 10px;
    }

    .item-name {
        display: block;
        font-weight: 700;
        font-size: 0.85rem;
    }

    .item-desc {
        display: block;
        font-size: 0.75rem;
        opacity: 0.7;
    }

    .item-price {
        font-weight: 600;
        color: var(--accent);
        font-size: 0.9rem;
    }

    .total-section {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 10px;
        padding: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }

    .total-label {
        font-weight: 700;
        font-size: 1rem;
    }

    .total-value {
        font-family: 'Dela Gothic One';
        font-size: 1.1rem;
        color: #00b894;
    }

    .btn-close-modal {
        width: 100%;
        padding: 12px;
        background: var(--text-color);
        color: var(--bg-color);
        border: none;
        border-radius: 50px;
        font-weight: 700;
        font-size: 0.9rem;
        cursor: pointer;
        transition: 0.3s;
        text-decoration: none;
        display: block;
        text-align: center;
    }

    .btn-close-modal:hover {
        transform: translateY(-2px);
    }

    .upload-box {
        border: 2px dashed #555;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.05);
        text-align: center;
        cursor: pointer;
        position: relative;
        /* Tetap relative untuk transisi */
        transition: all 0.3s ease;
        min-height: 180px;
        /* Tinggi minimal */
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 10px;
        /* Padding biar gambar ga nempel pinggir */
    }

    .upload-box:hover {
        border-color: var(--accent);
        background: rgba(255, 255, 255, 0.1);
    }

    .upload-placeholder {
        padding: 20px;
        transition: 0.3s;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .upload-placeholder i {
        font-size: 3rem;
        color: #888;
        margin-bottom: 10px;
    }

    /* Style Input File (Sembunyi) */
    #payment_proof {
        display: none;
    }

    /* Style Gambar Preview (Fix Size Issue) */
    #img-preview {
        display: none;
        /* Default sembunyi */
        max-width: 100%;
        /* Lebar max 100% container */
        max-height: 300px;
        /* Tinggi max segini biar ga kegedean */
        object-fit: contain;
        /* Gambar utuh ga kepotong */
        border-radius: 8px;
        margin-top: 5px;
    }

    /* Style untuk Tombol Lacak */
    .btn-track-order {
        width: 100%;
        padding: 12px;
        margin-top: 10px;
        /* Jarak dari tombol atas */
        background: transparent;
        color: var(--text-color);
        border: 2px solid var(--text-color);
        border-radius: 50px;
        font-weight: 700;
        font-size: 0.9rem;
        cursor: pointer;
        transition: 0.3s;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-track-order:hover {
        background: rgba(255, 255, 255, 0.1);
        transform: translateY(-2px);
    }
</style>

<?php if ($order_data): ?>
    <div class="modal-overlay active" id="successModal">
        <div class="receipt-modal">

            <div class="receipt-header">
                <div class="success-icon-box"><i class='bx bx-check'></i></div>
                <h2>ORDER RECEIVED!</h2>
            </div>

            <div class="receipt-body">

                <div class="info-group">
                    <div class="info-row">
                        <span>No. Order</span>
                        <strong>#<?= str_pad($order_data['id'], 5, '0', STR_PAD_LEFT) ?></strong>
                    </div>
                    <div class="info-row">
                        <span>Nama</span>
                        <strong><?= htmlspecialchars($order_data['customer_name']) ?></strong>
                    </div>
                </div>

                <div class="item-list">
                    <?php foreach ($order_items as $item): ?>
                        <div class="item-row">
                            <div class="item-left">
                                <span class="item-name"><?= $item['quantity'] ?>x <?= $item['menu_name'] ?></span>
                                <span class="item-desc">
                                    <?= $item['variant_name'] ? $item['variant_name'] : 'Original' ?>
                                    <?= $item['addon_name'] ? '+ ' . $item['addon_name'] : '' ?>
                                </span>
                            </div>
                            <div class="item-price">
                                <?= number_format($item['subtotal'], 0, ',', '.') ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="total-section">
                    <span class="total-label">TOTAL</span>
                    <span class="total-value">Rp <?= number_format($order_data['total_amount'], 0, ',', '.') ?></span>
                </div>

                <a href="order.php" class="btn-close-modal">PESAN LAGI</a>
                <a href="track.php?order_id=<?= $order_data['id'] ?>" class="btn-track-order">LACAK PESANAN</a>

            </div>
        </div>
    </div>
<?php endif; ?>


<header class="order-hero">
    <div class="hero-content">
        <span class="sub-title">READY TO SERVE</span>
        <h1>Start Your <span class="text-gradient">Order</span></h1>
        <p>Pilih kategori, tentukan menu, dan nikmati sushi terbaik.</p>
    </div>
    <div class="hero-order-image">
        <div class="hero-slideshow">
            <img src="assets/images/animasi1.png" alt="Ornamen 1" class="slide-img">
            <img src="assets/images/animasi2.png" alt="Ornamen 2" class="slide-img">
            <img src="assets/images/animasi3.png" alt="Ornamen 3" class="slide-img">
            <img src="assets/images/animasi4.png" alt="Ornamen 4" class="slide-img">
            <img src="assets/images/animasi5.png" alt="Ornamen 5" class="slide-img">
        </div>
    </div>
</header>

<div class="ticker-wrap">
    <div class="ticker-fade-l"></div>
    <div class="ticker-fade-r"></div>
    <div class="ticker-track">
        <div class="ticker-content">
            <div class="ticker-item"><i class='bx bxs-leaf'></i> FRESH INGREDIENTS</div>
            <div class="ticker-item"><i class='bx bxs-hot'></i> MADE BY ORDER</div>
            <div class="ticker-item"><i class='bx bxs-time-five'></i> FAST DELIVERY</div>
            <div class="ticker-item"><i class='bx bxs-award'></i> PREMIUM QUALITY</div>
            <div class="ticker-item"><i class='bx bxs-certification'></i> 100% HALAL</div>
        </div>
        <div class="ticker-content">
            <div class="ticker-item"><i class='bx bxs-leaf'></i> FRESH INGREDIENTS</div>
            <div class="ticker-item"><i class='bx bxs-hot'></i> MADE BY ORDER</div>
            <div class="ticker-item"><i class='bx bxs-time-five'></i> FAST DELIVERY</div>
            <div class="ticker-item"><i class='bx bxs-award'></i> PREMIUM QUALITY</div>
            <div class="ticker-item"><i class='bx bxs-certification'></i> 100% HALAL</div>
        </div>
    </div>
</div>

<main class="container-order">
    <div class="order-layout">

        <section class="form-section">
            <div class="form-card">

                <div class="form-header">
                    <h3>Customer Details</h3>
                    <p>Informasi pengiriman pesanan Anda.</p>
                </div>

                <form action="config/logic/process_order.php" method="POST" id="orderForm"
                    enctype="multipart/form-data">

                    <div class="form-row">
                        <div class="form-group">
                            <label for="nama">Nama Lengkap</label>
                            <input type="text" id="nama" name="nama" placeholder="Masukkan nama Anda..." required>
                        </div>
                        <div class="form-group">
                            <label for="hp">No. WhatsApp</label>
                            <input type="tel" id="hp" name="hp" placeholder="08xx-xxxx-xxxx" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Tipe Pesanan</label>
                        <div class="payment-options">
                            <label class="payment-card">
                                <input type="radio" name="order_type" value="delivery" checked
                                    onchange="toggleOrderType()">
                                <div class="card-content">
                                    <i class='bx bxs-truck'></i>
                                    <span>Dikirim</span>
                                    <small>Kurir Resto</small>
                                </div>
                            </label>
                            <label class="payment-card">
                                <input type="radio" name="order_type" value="pickup" onchange="toggleOrderType()">
                                <div class="card-content">
                                    <i class='bx bxs-store'></i>
                                    <span>Ambil Sendiri</span>
                                    <small>Datang ke Resto</small>
                                </div>
                            </label>
                        </div>

                    </div>

                    <div id="delivery-group">
                        <div class="form-group">
                            <label for="alamat">Alamat Pengiriman</label>
                            <textarea id="alamat" name="alamat" rows="3" placeholder="Jalan, Nomor Rumah, Patokan..."
                                required style="resize: none;"></textarea>
                        </div>

                        <div class="form-group" style="margin-top: 15px;">
                            <label>Catatan Pengiriman (Opsional)</label>
                            <textarea name="catatan_umum" rows="2"
                                placeholder="Contoh: Pagar warna hitam, titip di satpam..."
                                style="resize: none;"></textarea>
                        </div>
                    </div>

                    <div id="pickup-group" style="display: none;">
                        <div class="form-group">
                            <label>Catatan Pick-Up (Opsional)</label>
                            <textarea name="catatan_umum" rows="2"
                                placeholder="Contoh: Saya datang jam 13.00, jangan pakai sendok plastik..."
                                style="resize: none;" disabled></textarea>
                        </div>
                    </div>

                    <div class="form-header" style="margin-top: 15px;">
                        <h3>Menu Selection</h3>
                        <p>Pilih kategori terlebih dahulu, lalu pilih menu.</p>
                    </div>

                    <div id="items-container">

                        <div class="menu-item-card" id="item-1">
                            <div class="card-title">
                                <h4>Item #1</h4>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-half">
                                    <label>Kategori</label>
                                    <div class="select-wrapper">
                                        <select class="category-select" onchange="filterMenus(this)" required>
                                            <option value="" disabled selected> kategori </option>
                                            <?php foreach ($categories as $c): ?>
                                                <option value="<?= $c['id'] ?>"><?= $c['name'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <i class='bx bx-chevron-down arrow-icon'></i>
                                    </div>
                                </div>

                                <div class="form-group col-menu">
                                    <label>Pilih Menu</label>
                                    <div class="select-wrapper">
                                        <select name="items[0][menu_id]" class="menu-select" required
                                            onchange="calculateItemTotal(this); calculateGrandTotal();" disabled>
                                            <option value="" data-price="0"> Tentukan kategori </option>
                                        </select>
                                        <i class='bx bx-chevron-down arrow-icon'></i>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-qty">
                                    <label>Jml</label>
                                    <input type="number" name="items[0][qty]" value="1" min="1" max="50" required
                                        onchange="calculateItemTotal(this); calculateGrandTotal();">
                                </div>

                                <div class="form-group col-half">
                                    <label>Varian</label>
                                    <div class="select-wrapper">
                                        <select name="items[0][variant_id]" class="variant-select"
                                            onchange="calculateItemTotal(this); calculateGrandTotal();">
                                            <?php foreach ($variants as $v): ?>
                                                <?php
                                                $priceLabel = ($v['extra_price'] > 0)
                                                    ? " (+" . number_format($v['extra_price'], 0, ',', '.') . ")"
                                                    : "";
                                                ?>
                                                <option value="<?= $v['id'] ?>" data-extra="<?= $v['extra_price'] ?>">
                                                    <?= $v['name'] . $priceLabel ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <i class='bx bx-chevron-down arrow-icon'></i>
                                    </div>
                                </div>

                                <div class="form-group col-half">
                                    <label>Addon</label>
                                    <div class="select-wrapper">
                                        <select name="items[0][addon_id]" class="addon-select"
                                            onchange="calculateItemTotal(this); calculateGrandTotal();">
                                            <option value="" data-price="0">Tidak Pakai</option>
                                            <?php foreach ($addons as $a): ?>
                                                <?php
                                                if (strtolower($a['name']) == 'tidak pakai')
                                                    continue;
                                                $priceLabel = ($a['price'] > 0)
                                                    ? " (+" . number_format($a['price'], 0, ',', '.') . ")"
                                                    : "";
                                                ?>
                                                <option value="<?= $a['id'] ?>" data-price="<?= $a['price'] ?>">
                                                    <?= $a['name'] . $priceLabel ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <i class='bx bx-chevron-down arrow-icon'></i>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row" style="align-items: flex-end;">
                                <div class="form-group" style="flex: 2;">
                                    <input type="text" name="items[0][notes]" placeholder="Catatan item..."
                                        style="font-size: 0.9rem;">
                                </div>
                                <div class="form-group" style="flex: 1; text-align: right;">
                                    <small style="color: var(--text-color); opacity:0.7;">Subtotal Item:</small>
                                    <span class="item-subtotal"
                                        style="font-weight: 700; color: var(--accent); font-size: 1.1rem;">Rp 0</span>
                                </div>
                            </div>

                        </div>
                    </div>

                    <button type="button" class="btn-add-item" onclick="addNewItem()">
                        <i class='bx bx-plus-circle'></i> Tambah Menu Lain
                    </button>

                    <div class="total-estimation">
                        <span>Total Estimasi Pembayaran:</span>
                        <h3 id="grand-total">Rp 0</h3>
                    </div>

                    <div class="divider"></div>

                    <div class="form-group">
                        <label>Metode Pembayaran</label>
                        <div class="payment-options">
                            <label class="payment-card" id="cod-option">
                                <input type="radio" name="pembayaran" value="cod" checked
                                    onchange="togglePaymentProof()">
                                <div class="card-content">
                                    <i class='bx bxs-truck'></i>
                                    <span>COD</span>
                                    <small>Bayar di Tempat</small>
                                </div>
                            </label>
                            <label class="payment-card">
                                <input type="radio" name="pembayaran" value="transfer" onchange="togglePaymentProof()">
                                <div class="card-content">
                                    <i class='bx bxs-bank'></i>
                                    <span>Transfer</span>
                                    <small>Bank</small>
                                </div>
                            </label>
                            <label class="payment-card">
                                <input type="radio" name="pembayaran" value="wallet" onchange="togglePaymentProof()">
                                <div class="card-content">
                                    <i class='bx bxs-wallet'></i>
                                    <span>E-Wallet</span>
                                    <small>QRIS / GoPay</small>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div id="proof-group"
                        style="display: none; margin-top: 20px; margin-bottom: 20px; background: rgba(0,0,0,0.2); border-radius: 12px; padding: 20px; border: 1px solid #333;">

                        <div id="info-transfer"
                            style="display: none; text-align: center; margin-bottom: 20px; border-bottom: 1px dashed #555; padding-bottom: 15px;">
                            <i class='bx bxs-bank'
                                style="font-size: 3rem; color: var(--accent); margin-bottom: 10px;"></i>
                            <p style="margin: 0; font-weight: bold; color: white; font-size: 1.1rem;">Bank BCA</p>
                            <h2 style="margin: 5px 0; color: var(--accent); letter-spacing: 2px;">1234-5678-90</h2>
                            <p style="margin: 0; color: #ccc;">Street Sushi Official</p>
                            <small style="display: block; margin-top: 5px; color: #888;">Silakan transfer sesuai total
                                tagihan</small>
                        </div>

                        <div id="info-wallet"
                            style="display: none; text-align: center; margin-bottom: 20px; border-bottom: 1px dashed #555; padding-bottom: 15px;">
                            <p style="margin: 0 0 10px; font-weight: bold; color: white;">Scan QRIS (GoPay/OVO/Dana):
                            </p>

                            <img src="assets/images/qris.jpg" alt="QRIS Code"
                                style="width: 180px; border-radius: 10px; border: 4px solid white;">

                            <p style="margin: 10px 0 0; font-size: 0.9rem; color: #ccc;">Street Sushi Official</p>
                        </div>

                        <label style="color: var(--accent); font-weight: bold; display: block; margin-bottom: 10px;">
                            Upload Bukti Pembayaran <span style="color: red;">*</span>
                        </label>

                        <label for="payment_proof" class="upload-box">
                            <input type="file" id="payment_proof" name="payment_proof" accept="image/*"
                                onchange="previewImage()">

                            <div class="upload-placeholder" id="placeholder-content">
                                <i class='bx bxs-cloud-upload'></i>
                                <p style="margin:0; color:#ccc;">Klik untuk Upload Foto</p>
                                <small style="color: #666;">(JPG/PNG, Max 2MB)</small>
                            </div>

                            <img id="img-preview" src="#" alt="Preview Bukti">
                        </label>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-submit">
                            Konfirmasi Pesanan <i class='bx bx-right-arrow-alt'></i>
                        </button>
                    </div>

                </form>
            </div>
        </section>

        <aside class="visual-section">
            <div class="visual-card">
                <div class="visual-content">
                    <h4>Why Choose Us?</h4>
                    <ul class="benefit-list">
                        <li><i class='bx bxs-check-shield'></i><span>100% Bahan Segar & Halal</span></li>
                        <li><i class='bx bxs-time-five'></i><span>Pengiriman Cepat < 30 Menit</span>
                        </li>
                        <li><i class='bx bxs-star'></i><span>Rating 4.9/5 dari Pelanggan</span></li>
                    </ul>
                    <div class="mini-gallery">
                        <img src="assets/images/menu/sushi1.jpg" alt="Preview 1">
                        <img src="assets/images/menu/salmonaburi.jpg" alt="Preview 2">
                    </div>
                </div>
                <img src="assets/images/chef.jpeg" alt="Background" class="bg-visual">
            </div>
        </aside>

    </div>
</main>

<script>
    const allMenus = <?php echo json_encode($all_menus); ?>;
</script>

<script>
    let itemCount = 1;

    function filterMenus(categorySelect) {
        const card = categorySelect.closest('.menu-item-card');
        const menuSelect = card.querySelector('.menu-select');
        const selectedCatId = categorySelect.value;
        const subtotalText = card.querySelector('.item-subtotal');

        menuSelect.innerHTML = '<option value="" data-price="0" selected> Pilih Menu </option>';
        menuSelect.disabled = false;
        subtotalText.innerText = "Rp 0";

        const filteredMenus = allMenus.filter(m => m.category_id == selectedCatId);

        filteredMenus.forEach(m => {
            const priceFormatted = new Intl.NumberFormat('id-ID').format(m.base_price);
            const option = document.createElement('option');
            option.value = m.id;
            option.dataset.price = m.base_price;
            option.text = `${m.name} - Rp ${priceFormatted}`;
            menuSelect.appendChild(option);
        });
        calculateGrandTotal();
    }

    function calculateItemTotal(element) {
        const card = element.closest('.menu-item-card');

        const menuSelect = card.querySelector('.menu-select');
        const qtyInput = card.querySelector('input[name*="[qty]"]');
        const variantSelect = card.querySelector('.variant-select');
        const addonSelect = card.querySelector('.addon-select');
        const subtotalText = card.querySelector('.item-subtotal');

        const basePrice = parseFloat(menuSelect.selectedOptions[0]?.dataset.price || 0);
        const variantPrice = parseFloat(variantSelect.selectedOptions[0]?.dataset.extra || 0);
        const addonPrice = parseFloat(addonSelect.selectedOptions[0]?.dataset.price || 0);
        const qty = parseInt(qtyInput.value || 1);

        const subtotal = (basePrice + variantPrice + addonPrice) * qty;
        subtotalText.innerText = "Rp " + subtotal.toLocaleString('id-ID');
        return subtotal;
    }

    function calculateGrandTotal() {
        let grandTotal = 0;
        const cards = document.querySelectorAll('.menu-item-card');

        cards.forEach(card => {
            const subtotalText = card.querySelector('.item-subtotal');
            const val = parseInt(subtotalText.innerText.replace(/[^0-9]/g, '')) || 0;
            grandTotal += val;
        });

        document.getElementById('grand-total').innerText = "Rp " + grandTotal.toLocaleString('id-ID');
    }

    function addNewItem() {
        const container = document.getElementById('items-container');
        const firstItem = document.getElementById('item-1');
        const newItem = firstItem.cloneNode(true);

        itemCount++;
        newItem.id = `item-${itemCount}`;
        newItem.querySelector('.card-title h4').innerText = `Item #${itemCount}`;

        newItem.querySelectorAll('input, select').forEach(input => {
            let name = input.getAttribute('name');
            if (name) input.setAttribute('name', name.replace('[0]', `[${itemCount - 1}]`));

            if (input.tagName === 'SELECT') {
                input.selectedIndex = 0;
                if (input.classList.contains('menu-select')) {
                    input.innerHTML = '<option value="" data-price="0"> kategori </option>';
                    input.disabled = true;
                }
            } else if (input.type === 'number') {
                input.value = 1;
            } else {
                input.value = '';
            }
        });

        newItem.querySelector('.item-subtotal').innerText = "Rp 0";

        if (!newItem.querySelector('.btn-delete-item')) {
            const deleteBtn = document.createElement('button');
            deleteBtn.type = 'button';
            deleteBtn.className = 'btn-delete-item';
            deleteBtn.innerHTML = "<i class='bx bxs-trash'></i> Hapus";
            deleteBtn.onclick = function () { newItem.remove(); calculateGrandTotal(); };
            newItem.querySelector('.card-title').appendChild(deleteBtn);
        }

        container.appendChild(newItem);
    }

    // function addNewItem() {
    //     container.appendChild(newItem);
    // }

    document.addEventListener("DOMContentLoaded", function () {
        const preMenuId = <?php echo $preselect_menu_id; ?>;
        const preCatId = <?php echo $preselect_cat_id; ?>;

        if (preMenuId > 0 && preCatId > 0) {
            const firstCard = document.getElementById('item-1');
            const catSelect = firstCard.querySelector('.category-select');
            const menuSelect = firstCard.querySelector('.menu-select');

            catSelect.value = preCatId;

            filterMenus(catSelect);

            menuSelect.value = preMenuId;

            calculateItemTotal(menuSelect);
            calculateGrandTotal();

            document.querySelector('.form-section').scrollIntoView({ behavior: 'smooth' });
        }
    });

    function toggleOrderType() {
        const type = document.querySelector('input[name="order_type"]:checked').value;
        const deliveryGroup = document.getElementById('delivery-group');
        const pickupGroup = document.getElementById('pickup-group');
        const addrInput = document.getElementById('alamat');
        const deliveryNote = deliveryGroup.querySelector('textarea[name="catatan_umum"]');
        const pickupNote = pickupGroup.querySelector('textarea[name="catatan_umum"]');
        const codOption = document.getElementById('cod-option');
        const codInput = codOption.querySelector('input');

        if (type === 'pickup') {
            deliveryGroup.style.display = 'none';
            addrInput.required = false;
            addrInput.value = '-';
            deliveryNote.disabled = true;

            pickupGroup.style.display = 'block';
            pickupNote.disabled = false;

            codOption.style.display = 'none';
            if (codInput.checked) {
                document.querySelector('input[value="transfer"]').checked = true;
            }

        } else {
            deliveryGroup.style.display = 'block';
            addrInput.required = true;
            if (addrInput.value === '-') addrInput.value = '';
            deliveryNote.disabled = false;
            pickupGroup.style.display = 'none';
            pickupNote.disabled = true;

            codOption.style.display = '';
        }
        togglePaymentProof();
    }

    function togglePaymentProof() {
        const paymentType = document.querySelector('input[name="pembayaran"]:checked').value;

        // Ambil elemen utama
        const proofGroup = document.getElementById('proof-group');
        const proofInput = document.getElementById('payment_proof');
        const preview = document.getElementById('img-preview');
        const placeholder = document.getElementById('placeholder-content');

        // Ambil elemen info khusus
        const infoTransfer = document.getElementById('info-transfer');
        const infoWallet = document.getElementById('info-wallet');

        if (paymentType === 'cod') {
            // --- MODE COD ---
            proofGroup.style.display = 'none';
            proofInput.required = false;
            proofInput.value = '';

            // Reset Preview
            preview.style.display = 'none';
            preview.src = '#';
            placeholder.style.display = '';

        } else {
            // --- MODE TRANSFER ATAU WALLET ---
            proofGroup.style.display = 'block'; // Munculkan container upload
            proofInput.required = true;

            // Cek detail mana yang ditampilkan
            if (paymentType === 'transfer') {
                infoTransfer.style.display = 'block'; // Tampilkan Rekening
                infoWallet.style.display = 'none';    // Sembunyikan QRIS
            } else if (paymentType === 'wallet') {
                infoTransfer.style.display = 'none';  // Sembunyikan Rekening
                infoWallet.style.display = 'block';   // Tampilkan QRIS
            }
        }
    }

    function previewImage() {
        const input = document.getElementById('payment_proof');
        const preview = document.getElementById('img-preview');
        const placeholder = document.getElementById('placeholder-content');

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function (e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
                placeholder.style.display = 'none';
            }

            reader.readAsDataURL(input.files[0]);
        } else {
            preview.style.display = 'none';
            preview.src = '#';
            placeholder.style.display = 'flex';
        }
    }

</script>

<?php include 'includes/footer.php'; ?>