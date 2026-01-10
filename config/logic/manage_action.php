<?php
session_start();
include '../db.php';

// Pastikan User Login
if (!isset($_SESSION['login'])) { header("Location: ../../admin/login.php"); exit; }

$act = isset($_REQUEST['act']) ? $_REQUEST['act'] : '';

// --- 1. HANDLE MENU UTAMA ---
if ($act == 'save_menu') {
    $id = $_POST['id'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $cat_id = (int)$_POST['category_id'];
    
    // AMBIL HARGA DARI INPUT HIDDEN (YANG RAW/MURNI ANGKA)
    $price = (int)$_POST['base_price']; 
    
    $desc = mysqli_real_escape_string($conn, $_POST['description']);

    // Handle Upload Gambar
    $image_query = ""; 
    if (!empty($_FILES['image']['name'])) {
        // UBAH TUJUAN UPLOAD KE FOLDER 'UPLOADS'
        $target_dir = "../../assets/uploads/";
        
        // Buat folder jika belum ada
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $filename = time() . "_" . basename($_FILES["image"]["name"]); 
        $target_file = $target_dir . $filename;
        
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_query = ", image='$filename'";
        }
    }

    if (!empty($id)) {
        // UPDATE
        $sql = "UPDATE menu_items SET name='$name', category_id='$cat_id', base_price='$price', description='$desc' $image_query WHERE id='$id'";
    } else {
        // INSERT
        // Default gambar jika tidak diupload
        $img_val = !empty($filename) ? $filename : ''; // Kosongkan saja biar nanti ditangani frontend
        $sql = "INSERT INTO menu_items (name, category_id, base_price, description, image) VALUES ('$name', '$cat_id', '$price', '$desc', '$img_val')";
    }

    mysqli_query($conn, $sql);
    header("Location: ../../admin/manage.php");
}

elseif ($act == 'delete_menu') {
    $id = (int)$_GET['id'];
    mysqli_query($conn, "DELETE FROM menu_items WHERE id=$id");
    header("Location: ../../admin/manage.php");
}
elseif ($act == 'save_cat') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    mysqli_query($conn, "INSERT INTO categories (name) VALUES ('$name')");
    header("Location: ../../admin/manage.php");
}
elseif ($act == 'delete_cat') {
    $id = (int)$_GET['id'];
    mysqli_query($conn, "DELETE FROM categories WHERE id=$id");
    header("Location: ../../admin/manage.php");
}
elseif ($act == 'save_var') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $price = (int)$_POST['extra_price'];
    mysqli_query($conn, "INSERT INTO variants (name, extra_price) VALUES ('$name', '$price')");
    header("Location: ../../admin/manage.php");
}
elseif ($act == 'delete_var') {
    $id = (int)$_GET['id'];
    mysqli_query($conn, "DELETE FROM variants WHERE id=$id");
    header("Location: ../../admin/manage.php");
}
elseif ($act == 'save_add') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $price = (int)$_POST['price'];
    mysqli_query($conn, "INSERT INTO addons (name, price) VALUES ('$name', '$price')");
    header("Location: ../../admin/manage.php");
}
elseif ($act == 'delete_add') {
    $id = (int)$_GET['id'];
    mysqli_query($conn, "DELETE FROM addons WHERE id=$id");
    header("Location: ../../admin/manage.php");
}
?>