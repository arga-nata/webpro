<?php 
  include 'config/db.php';
  
  if (!isset($_GET['id'])) {
      header("Location: order.php");
      exit;
  }

  $order_id = (int)$_GET['id'];

  // AMBIL DATA ORDER HEADER
  $q_order = mysqli_query($conn, "SELECT * FROM orders WHERE id = $order_id");
  $order = mysqli_fetch_assoc($q_order);

  if (!$order) {
      echo "Pesanan tidak ditemukan.";
      exit;
  }

  // AMBIL DATA DETAIL ITEM (JOIN BEBERAPA TABEL)
  $q_items = mysqli_query($conn, "
      SELECT od.*, m.name as menu_name, v.name as variant_name, a.name as addon_name 
      FROM order_details od
      JOIN menu_items m ON od.menu_item_id = m.id
      LEFT JOIN variants v ON od.variant_id = v.id
      LEFT JOIN addons a ON od.addon_id = a.id
      WHERE od.order_id = $order_id
  ");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Berhasil - Street Sushi</title>
    
    <link rel="stylesheet" href="assets/css/page.css">
    <style>
        body { background: var(--bg-color); color: var(--text-color); font-family: 'Inter', sans-serif; }
        .success-container {
            max-width: 600px; margin: 50px auto; padding: 20px;
        }
        .receipt-card {
            background: var(--card-bg);
            border-radius: 20px; padding: 40px;
            box-shadow: var(--shadow-soft);
            text-align: center;
            border-top: 5px solid var(--success-color, #00b894);
            position: relative;
            overflow: hidden;
        }
        /* Hiasan Bergerigi ala Struk Belanja */
        .receipt-card::after {
            content: ""; position: absolute; bottom: 0; left: 0; width: 100%; height: 20px;
            background: linear-gradient(135deg, transparent 75%, var(--bg-color) 75%) 0 50%,
                        linear-gradient(45deg, transparent 75%, var(--bg-color) 75%) 0 50%;
            background-size: 20px 20px;
        }

        .icon-success {
            width: 80px; height: 80px; background: rgba(0, 184, 148, 0.1);
            color: #00b894; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 3rem; margin: 0 auto 20px;
        }
        
        h1 { font-family: 'Dela Gothic One'; color: var(--accent); margin-bottom: 10px; }
        p.subtitle { opacity: 0.7; margin-bottom: 30px; }

        .order-info {
            text-align: left; background: var(--bg-color);
            padding: 20px; border-radius: 10px; margin-bottom: 20px;
            font-size: 0.9rem;
        }
        .info-row { display: flex; justify-content: space-between; margin-bottom: 8px; border-bottom: 1px dashed var(--border-color); padding-bottom: 8px; }
        .info-row:last-child { border-bottom: none; }

        .items-list { text-align: left; margin-bottom: 30px; }
        .item-row { display: flex; justify-content: space-between; margin-bottom: 15px; }
        .item-desc { flex: 1; padding-right: 10px; }
        .item-name { font-weight: 700; display: block; }
        .item-detail { font-size: 0.85rem; opacity: 0.7; display: block; }
        .item-price { font-weight: 600; color: var(--accent); }

        .total-row {
            display: flex; justify-content: space-between; align-items: center;
            font-size: 1.2rem; font-weight: 800;
            border-top: 2px dashed var(--border-color); padding-top: 15px; margin-top: 10px;
        }

        .btn-home {
            display: inline-block; padding: 12px 30px;
            background: var(--accent); color: white; text-decoration: none;
            border-radius: 30px; font-weight: 700; margin-top: 20px;
            transition: 0.3s;
        }
        .btn-home:hover { transform: translateY(-3px); box-shadow: var(--shadow-hover); }
    </style>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>

    <div class="success-container">
        <div class="receipt-card">
            
            <div class="icon-success"><i class='bx bx-check'></i></div>
            <h1>Pesanan Diterima!</h1>
            <p class="subtitle">Terima kasih, pesananmu sedang kami siapkan.</p>

            <div class="order-info">
                <div class="info-row">
                    <span>No. Order</span>
                    <strong>#ORD-<?= str_pad($order['id'], 5, '0', STR_PAD_LEFT) ?></strong>
                </div>
                <div class="info-row">
                    <span>Nama</span>
                    <strong><?= htmlspecialchars($order['customer_name']) ?></strong>
                </div>
                <div class="info-row">
                    <span>Metode Bayar</span>
                    <strong style="text-transform: uppercase;"><?= $order['payment_method'] ?></strong>
                </div>
                <div class="info-row">
                    <span>Alamat</span>
                    <span style="text-align: right; max-width: 60%;"><?= htmlspecialchars($order['delivery_address']) ?></span>
                </div>
            </div>

            <div class="items-list">
                <?php while($item = mysqli_fetch_assoc($q_items)): ?>
                    <div class="item-row">
                        <div class="item-desc">
                            <span class="item-name"><?= $item['quantity'] ?>x <?= $item['menu_name'] ?></span>
                            <span class="item-detail">
                                <?= $item['variant_name'] ? $item['variant_name'] : 'Original' ?> 
                                <?= $item['addon_name'] ? '+ '.$item['addon_name'] : '' ?>
                                <?= !empty($item['notes']) ? '<br><i>"'.$item['notes'].'"</i>' : '' ?>
                            </span>
                        </div>
                        <div class="item-price">
                            Rp <?= number_format($item['subtotal'], 0, ',', '.') ?>
                        </div>
                    </div>
                <?php endwhile; ?>
                
                <div class="total-row">
                    <span>TOTAL BAYAR</span>
                    <span style="color: #00b894;">Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></span>
                </div>
            </div>

            <a href="index.php" class="btn-home">Kembali ke Beranda</a>
            <p style="margin-top: 15px; font-size: 0.8rem; opacity: 0.5;">Simpan tangkapan layar ini sebagai bukti pesanan.</p>

        </div>
    </div>

</body>
</html>