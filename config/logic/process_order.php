<?php
session_start();
include '../db.php'; // Pastikan path koneksi DB benar

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. AMBIL DATA PELANGGAN (Sanitize Input)
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $hp = mysqli_real_escape_string($conn, $_POST['hp']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $metode = mysqli_real_escape_string($conn, $_POST['pembayaran']); // cod, transfer, wallet
    $catatan = mysqli_real_escape_string($conn, $_POST['catatan_umum']);

    // Validasi Sederhana
    if (empty($nama) || empty($hp) || empty($alamat) || empty($_POST['items'])) {
        echo "<script>alert('Data pesanan tidak lengkap!'); window.history.back();</script>";
        exit;
    }

    // 2. SIAPKAN VARIABEL UNTUK ITEM & TOTAL
    $items = $_POST['items']; // Array dari form
    $grand_total = 0;
    $order_items_data = []; // Penampung data untuk di-insert nanti

    // 3. LOOPING ITEM UNTUK HITUNG TOTAL HARGA (Server-Side Validation)
    foreach ($items as $item) {
        $menu_id = (int) $item['menu_id'];
        $qty = (int) $item['qty'];
        $variant_id = !empty($item['variant_id']) ? (int) $item['variant_id'] : "NULL";
        $addon_id = !empty($item['addon_id']) ? (int) $item['addon_id'] : "NULL";
        $note = mysqli_real_escape_string($conn, $item['note']);

        // Ambil Harga Menu
        $q_menu = mysqli_query($conn, "SELECT base_price FROM menu_items WHERE id = $menu_id");
        $d_menu = mysqli_fetch_assoc($q_menu);
        $price_menu = $d_menu['base_price'];

        // Ambil Harga Varian
        $price_variant = 0;
        if ($variant_id != "NULL") {
            $q_var = mysqli_query($conn, "SELECT extra_price FROM variants WHERE id = $variant_id");
            if ($row = mysqli_fetch_assoc($q_var)) {
                $price_variant = $row['extra_price'];
            }
        }

        // Ambil Harga Addon
        $price_addon = 0;
        if ($addon_id != "NULL") {
            $q_add = mysqli_query($conn, "SELECT price FROM addons WHERE id = $addon_id");
            if ($row = mysqli_fetch_assoc($q_add)) {
                $price_addon = $row['price'];
            }
        }

        // Hitung Subtotal per Item
        $subtotal = ($price_menu + $price_variant + $price_addon) * $qty;
        $grand_total += $subtotal;

        // Simpan data sementara ke array
        $order_items_data[] = [
            'menu_id' => $menu_id,
            'variant_id' => $variant_id,
            'addon_id' => $addon_id,
            'qty' => $qty,
            'subtotal' => $subtotal,
            'note' => $note
        ];
    }

    // 4. INSERT KE TABEL ORDERS (HEADER)
    $sql_order = "INSERT INTO orders (customer_name, customer_phone, delivery_address, payment_method, total_amount, notes_general, status) 
                  VALUES ('$nama', '$hp', '$alamat', '$metode', '$grand_total', '$catatan', 'pending')";

    if (mysqli_query($conn, $sql_order)) {
        // Ambil ID Order yang baru saja dibuat
        $order_id = mysqli_insert_id($conn);

        // 5. INSERT KE TABEL ORDER_DETAILS (DETAIL)
        foreach ($order_items_data as $data) {
            $sql_detail = "INSERT INTO order_details (order_id, menu_item_id, variant_id, addon_id, quantity, subtotal, notes) 
                           VALUES ('$order_id', '{$data['menu_id']}', {$data['variant_id']}, {$data['addon_id']}, '{$data['qty']}', '{$data['subtotal']}', '{$data['note']}')";
            mysqli_query($conn, $sql_detail);
        }

        // 6. SUKSES - REDIRECT KE HALAMAN KONFIRMASI
        header("Location: ../../order.php?status=success&id=$order_id");
        exit;

    } else {
        echo "Error: " . mysqli_error($conn);
    }

} else {
    header("Location: ../../order.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'accept') {
    $order_id = intval($_POST['order_id']);

    // Update status jadi 'cooking'
    $update = mysqli_query($conn, "UPDATE orders SET status = 'cooking' WHERE id = $order_id");

    if ($update) {
        // Refresh halaman otomatis
        header("Location: dashboard.php");
        exit;
    }
}
?>