<?php
session_start();

// Error Reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../config/db.php';

// Cek Login
if (!isset($_SESSION['login'])) {
    header("Location: ../admin/login.php");
    exit;
}

// --- FUNGSI REDIRECT DINAMIS ---
// Ditambahkan parameter $tab (default ke 'menu')
function redirect($status, $msg, $tab = 'menu') {
    $_SESSION['flash_status'] = $status;
    $_SESSION['flash_message'] = $msg;
    // Redirect membawa parameter tab agar JS tahu harus buka tab mana
    header("Location: manage.php?tab=" . $tab);
    exit;
}

// ==========================================
// 1. SETUP PAGINATION & VARIABEL
// ==========================================
$limit = 10;

// Pagination Menu
$page_menu = isset($_GET['page_menu']) ? (int)$_GET['page_menu'] : 1;
if ($page_menu < 1) $page_menu = 1;
$start_menu = ($page_menu - 1) * $limit;

// Pagination Pegawai
$page_emp = isset($_GET['page_emp']) ? (int)$_GET['page_emp'] : 1;
if ($page_emp < 1) $page_emp = 1;
$start_emp = ($page_emp - 1) * $limit;


// ==========================================
// 2. QUERY DATA UTAMA (UNTUK TABEL)
// ==========================================

// A. TABEL MENU
$total_menu = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM menu_items"));
$total_pages_menu = ceil($total_menu / $limit);
$sql_menu = "SELECT m.*, c.name as cat_name 
             FROM menu_items m 
             LEFT JOIN categories c ON m.category_id = c.id 
             ORDER BY m.id DESC 
             LIMIT $start_menu, $limit";
$q_menu = mysqli_query($conn, $sql_menu);

// B. TABEL PEGAWAI (Join job_roles & work_shifts)
$total_emp = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM employees"));
$total_pages_emp = ceil($total_emp / $limit);
$sql_emp = "SELECT e.*, r.role_name, s.shift_name, s.start_time, s.end_time 
            FROM employees e 
            LEFT JOIN job_roles r ON e.role_id = r.id 
            LEFT JOIN work_shifts s ON e.shift_id = s.id 
            ORDER BY e.id DESC 
            LIMIT $start_emp, $limit";
$q_emp = mysqli_query($conn, $sql_emp);

// C. TABEL KATEGORI (Dengan hitungan items)
$sql_cat = "SELECT c.*, COUNT(m.id) as total_items 
            FROM categories c 
            LEFT JOIN menu_items m ON c.id = m.category_id 
            GROUP BY c.id 
            ORDER BY c.id ASC";
$q_cat = mysqli_query($conn, $sql_cat);

// D. TABEL VARIAN
$q_var = mysqli_query($conn, "SELECT * FROM variants ORDER BY id ASC");

// E. TABEL ADDONS
$q_add = mysqli_query($conn, "SELECT * FROM addons ORDER BY id ASC");


// ==========================================
// 3. QUERY DATA PELENGKAP (UNTUK DROPDOWN MODAL)
// ==========================================

// List Jabatan (job_roles)
$roles = [];
$q_roles = mysqli_query($conn, "SELECT * FROM job_roles ORDER BY id ASC");
while ($r = mysqli_fetch_assoc($q_roles)) {
    $roles[] = $r;
}

// List Shift (work_shifts)
// LOGIKA BARU: Hanya ambil shift yang start_time dan end_time TIDAK KOSONG (NOT NULL)
$shifts = [];
$q_shifts = mysqli_query($conn, "SELECT * FROM work_shifts WHERE start_time IS NOT NULL AND end_time IS NOT NULL ORDER BY id ASC");
while ($s = mysqli_fetch_assoc($q_shifts)) {
    $shifts[] = $s;
}

// List Kategori (Simple List)
$cat_list = [];
$q_cat_list = mysqli_query($conn, "SELECT * FROM categories ORDER BY id ASC");
while ($cl = mysqli_fetch_assoc($q_cat_list)) {
    $cat_list[] = $cl;
}


// ==========================================
// 4. LOGIKA ACTION (CREATE, UPDATE, DELETE)
// ==========================================

// --- A. MENU ---
if (isset($_POST['save_menu'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $cat_id = intval($_POST['category_id']);
    $price = (float)str_replace('.', '', $_POST['price']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $is_avail = isset($_POST['is_available']) ? 1 : 0; 
    
    // Handle Image
    $imageQueryPart = ""; 
    if (!empty($_FILES['image']['name'])) {
        $imgName = time() . '_' . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "../assets/uploads/" . $imgName);
        $imageQueryPart = ", image='$imgName'";
    } else {
        $imgName = ''; 
    }

    if (!empty($_POST['menu_id'])) {
        $id = intval($_POST['menu_id']);
        if ($imageQueryPart != "") {
            $sql = "UPDATE menu_items SET name='$name', category_id=$cat_id, base_price=$price, description='$desc', is_available=$is_avail $imageQueryPart WHERE id=$id";
        } else {
            $sql = "UPDATE menu_items SET name='$name', category_id=$cat_id, base_price=$price, description='$desc', is_available=$is_avail WHERE id=$id";
        }
        $msg = "Menu berhasil diperbarui.";
    } else {
        $sql = "INSERT INTO menu_items (name, category_id, base_price, description, image, is_available) VALUES ('$name', $cat_id, $price, '$desc', '$imgName', $is_avail)";
        $msg = "Menu baru berhasil dibuat.";
    }

    // Redirect tetap ke 'menu'
    if(mysqli_query($conn, $sql)) redirect('success', $msg, 'menu');
    else redirect('error', 'Gagal menyimpan menu.', 'menu');
}

if (isset($_POST['delete_menu'])) {
    $id = intval($_POST['menu_id']);
    mysqli_query($conn, "DELETE FROM menu_items WHERE id=$id");
    redirect('success', 'Menu berhasil dihapus.', 'menu');
}

// --- B. PEGAWAI ---
if (isset($_POST['save_employee'])) {
    $name = mysqli_real_escape_string($conn, $_POST['emp_name']);
    $role = intval($_POST['emp_role']);
    
    // Ambil input dari dropdown (bisa berupa ID Shift atau Teks Status)
    $input_shift = $_POST['emp_shift_input']; 

    // LOGIKA HYBRID:
    if (is_numeric($input_shift)) {
        // Jika Angka = Berarti memilih Shift
        $shift_val = intval($input_shift); // Simpan ID Shift
        $keterangan = "-";                 // Keterangan dikosongkan/strip
    } else {
        // Jika Teks = Berarti memilih Status (Cuti/Libur/dll)
        $shift_val = "NULL";               // Shift ID dikosongkan
        $keterangan = mysqli_real_escape_string($conn, $input_shift); // Simpan teksnya
    }

    if (!empty($_POST['emp_id'])) {
        // Update
        $id = intval($_POST['emp_id']);
        $sql = "UPDATE employees SET name='$name', role_id=$role, shift_id=$shift_val, keterangan='$keterangan' WHERE id=$id";
        $msg = "Data pegawai diperbarui.";
    } else {
        // Create
        $sql = "INSERT INTO employees (name, role_id, shift_id, keterangan) VALUES ('$name', $role, $shift_val, '$keterangan')";
        $msg = "Pegawai baru ditambahkan.";
    }

    if(mysqli_query($conn, $sql)) redirect('success', $msg, 'employees');
    else redirect('error', 'Gagal menyimpan pegawai: ' . mysqli_error($conn), 'employees');
}

if (isset($_POST['delete_employee'])) {
    $id = intval($_POST['emp_id']);
    mysqli_query($conn, "DELETE FROM employees WHERE id=$id");
    redirect('success', 'Pegawai dihapus.', 'employees');
}

// --- C. KATEGORI ---
if (isset($_POST['save_category'])) {
    $name = mysqli_real_escape_string($conn, $_POST['cat_name']);
    
    if (!empty($_POST['cat_id'])) {
        $id = intval($_POST['cat_id']);
        $sql = "UPDATE categories SET name='$name' WHERE id=$id";
        $msg = "Kategori diperbarui.";
    } else {
        $sql = "INSERT INTO categories (name) VALUES ('$name')";
        $msg = "Kategori baru ditambahkan.";
    }

    // Redirect ke tab 'categories'
    if(mysqli_query($conn, $sql)) redirect('success', $msg, 'categories');
    else redirect('error', 'Gagal menyimpan kategori.', 'categories');
}

if (isset($_POST['delete_category'])) {
    $id = intval($_POST['cat_id']);
    mysqli_query($conn, "DELETE FROM categories WHERE id=$id");
    redirect('success', 'Kategori dihapus.', 'categories');
}

// --- D. VARIAN ---
if (isset($_POST['save_variant'])) {
    $name = mysqli_real_escape_string($conn, $_POST['var_name']);
    $price = (float)str_replace('.', '', $_POST['var_price']); 

    if (!empty($_POST['var_id'])) {
        $id = intval($_POST['var_id']);
        $sql = "UPDATE variants SET name='$name', extra_price=$price WHERE id=$id";
        $msg = "Varian diperbarui.";
    } else {
        $sql = "INSERT INTO variants (name, extra_price) VALUES ('$name', $price)";
        $msg = "Varian baru ditambahkan.";
    }
    
    // Redirect ke tab 'variants'
    if(mysqli_query($conn, $sql)) redirect('success', $msg, 'variants');
    else redirect('error', 'Gagal menyimpan varian.', 'variants');
}

if (isset($_POST['delete_variant'])) {
    $id = intval($_POST['var_id']);
    mysqli_query($conn, "DELETE FROM variants WHERE id=$id");
    redirect('success', 'Varian dihapus.', 'variants');
}

// --- E. ADDONS ---
if (isset($_POST['save_addon'])) {
    $name = mysqli_real_escape_string($conn, $_POST['addon_name']);
    $price = (float)str_replace('.', '', $_POST['addon_price']); 

    if (!empty($_POST['addon_id'])) {
        $id = intval($_POST['addon_id']);
        $sql = "UPDATE addons SET name='$name', price=$price WHERE id=$id";
        $msg = "Add-on diperbarui.";
    } else {
        $sql = "INSERT INTO addons (name, price) VALUES ('$name', $price)";
        $msg = "Add-on baru ditambahkan.";
    }
    
    // Redirect ke tab 'addons'
    if(mysqli_query($conn, $sql)) redirect('success', $msg, 'addons');
    else redirect('error', 'Gagal menyimpan add-on.', 'addons');
}

if (isset($_POST['delete_addon'])) {
    $id = intval($_POST['addon_id']);
    mysqli_query($conn, "DELETE FROM addons WHERE id=$id");
    redirect('success', 'Add-on dihapus.', 'addons');
}
?>