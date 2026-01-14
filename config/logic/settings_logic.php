<?php
session_start();
// Error Reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../config/db.php';

// 1. CEK LOGIN
if (!isset($_SESSION['login'])) {
    header("Location: login.php"); // Sesuaikan path jika perlu
    exit;
}

// --- FUNGSI HELPER ---
function redirectWithToast($status, $message) {
    $_SESSION['flash_status'] = $status;
    $_SESSION['flash_message'] = $message;
    header("Location: settings.php");
    exit;
}

// ==========================================
// 2. PROSES DATA (POST REQUEST)
// ==========================================
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // A. SAVE STATUS TOKO
    if (isset($_POST['save_status'])) {
        $force = mysqli_real_escape_string($conn, $_POST['force_status']);
        if(mysqli_query($conn, "UPDATE system_settings SET force_status='$force' WHERE id=1")) {
            redirectWithToast('success', 'Status toko diperbarui!');
        } else {
            redirectWithToast('error', 'Gagal update status.');
        }
    }

    // B. SAVE KAPASITAS
    if (isset($_POST['save_capacity'])) {
        $tables = intval($_POST['total_tables']);
        if(mysqli_query($conn, "UPDATE system_settings SET total_tables=$tables WHERE id=1")) {
            redirectWithToast('success', 'Kapasitas meja diperbarui!');
        } else {
            redirectWithToast('error', 'Gagal update kapasitas.');
        }
    }

    // C. SAVE JADWAL TOKO
    if (isset($_POST['save_schedule'])) {
        $dayNames = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        $hasError = false;
        $errorMsg = "";

        // Validasi
        foreach ($_POST['sch_id'] as $key => $id) {
            $open = $_POST['sch_open'][$key];
            $close = $_POST['sch_close'][$key];
            
            if (empty($open) && empty($close)) continue; // Libur

            if ((empty($open) && !empty($close)) || (!empty($open) && empty($close))) {
                $hasError = true;
                $errorMsg = "Jadwal hari " . $dayNames[$key] . " tidak lengkap.";
                break;
            }
            if ($close <= $open) {
                $hasError = true;
                $errorMsg = "Jadwal hari " . $dayNames[$key] . " salah (Tutup <= Buka).";
                break;
            }
        }

        if ($hasError) {
            redirectWithToast('error', $errorMsg);
        } else {
            // Eksekusi Update
            foreach ($_POST['sch_id'] as $key => $id) {
                $open = $_POST['sch_open'][$key];
                $close = $_POST['sch_close'][$key];
                $is_closed = (empty($open) && empty($close)) ? 1 : 0;
                $open_val = empty($open) ? "NULL" : "'$open'";
                $close_val = empty($close) ? "NULL" : "'$close'";
                
                mysqli_query($conn, "UPDATE store_schedule SET open_time=$open_val, close_time=$close_val, is_closed=$is_closed WHERE id=$id");
            }
            redirectWithToast('success', 'Jadwal operasional disimpan!');
        }
    }

    // D. SAVE SHIFT
    if (isset($_POST['save_shift'])) {
        $hasError = false;
        $errorMsg = "";
        $prev_end_time = null;
        $prev_shift_name = "";

        foreach ($_POST['shift_id'] as $key => $id) {
            // Ambil nama shift untuk pesan error
            $q_name = mysqli_query($conn, "SELECT shift_name FROM work_shifts WHERE id=$id");
            $r_name = mysqli_fetch_assoc($q_name);
            $shift_name = $r_name ? $r_name['shift_name'] : "Shift " . ($key + 1);

            $start = $_POST['shift_start'][$key];
            $end = $_POST['shift_end'][$key];

            if (empty($start) && empty($end)) continue;

            if (empty($start) || empty($end)) {
                $hasError = true; $errorMsg = "$shift_name jamnya tidak lengkap."; break;
            }
            if ($end <= $start) {
                $hasError = true; $errorMsg = "Jam selesai $shift_name harus lebih besar dari mulai."; break;
            }
            if ($prev_end_time !== null && $start < $prev_end_time) {
                $hasError = true; $errorMsg = "Jam $shift_name bentrok dengan $prev_shift_name."; break;
            }

            $prev_end_time = $end;
            $prev_shift_name = $shift_name;
        }

        if ($hasError) {
            redirectWithToast('error', $errorMsg);
        } else {
            foreach ($_POST['shift_id'] as $key => $id) {
                $start = $_POST['shift_start'][$key];
                $end = $_POST['shift_end'][$key];
                $start_val = empty($start) ? "NULL" : "'$start'";
                $end_val = empty($end) ? "NULL" : "'$end'";
                
                mysqli_query($conn, "UPDATE work_shifts SET start_time=$start_val, end_time=$end_val WHERE id=$id");
            }
            redirectWithToast('success', 'Pengaturan Shift disimpan!');
        }
    }

    // E. JOB ROLES (Posisi)
    if (isset($_POST['add_job'])) {
        $raw_role = trim($_POST['role_name']);
        if(!empty($raw_role)) {
            $role_clean = ucwords(strtolower($raw_role)); // Format: Chef De Partie
            $cek = mysqli_query($conn, "SELECT id FROM job_roles WHERE role_name = '$role_clean'");
            if(mysqli_num_rows($cek) > 0) {
                redirectWithToast('error', 'Nama jabatan sudah ada!');
            } else {
                mysqli_query($conn, "INSERT INTO job_roles (role_name) VALUES ('$role_clean')");
                redirectWithToast('success', 'Jabatan ditambahkan.');
            }
        }
    }
    if (isset($_POST['delete_job'])) {
        $job_id = intval($_POST['job_id']);
        mysqli_query($conn, "DELETE FROM job_roles WHERE id=$job_id");
        redirectWithToast('success', 'Jabatan dihapus.');
    }

    // F. HOLIDAYS
    if (isset($_POST['add_holiday'])) {
        $date = $_POST['h_date'];
        $desc = mysqli_real_escape_string($conn, $_POST['h_desc']);
        $cek = mysqli_query($conn, "SELECT id FROM special_dates WHERE date = '$date'");
        if(mysqli_num_rows($cek) > 0) {
            redirectWithToast('error', 'Tanggal libur tersebut sudah ada!');
        } else {
            mysqli_query($conn, "INSERT INTO special_dates (date, description) VALUES ('$date', '$desc')");
            redirectWithToast('success', 'Libur ditambahkan.');
        }
    }
    if (isset($_POST['delete_holiday'])) {
        $h_id = intval($_POST['h_id']);
        mysqli_query($conn, "DELETE FROM special_dates WHERE id=$h_id");
        redirectWithToast('success', 'Libur dihapus.');
    }
}

// ==========================================
// 3. AMBIL DATA (GET REQUEST) - Persiapan Variabel View
// ==========================================

// A. Setting Umum
$q_set = mysqli_query($conn, "SELECT * FROM system_settings WHERE id = 1");
$setting = mysqli_fetch_assoc($q_set);

// B. Jadwal Toko
$schedule = [];
$q_sch = mysqli_query($conn, "SELECT * FROM store_schedule ORDER BY id ASC");
while($row = mysqli_fetch_assoc($q_sch)) { $schedule[] = $row; }

// C. Shift Kerja
$shifts = [];
$q_shf = mysqli_query($conn, "SELECT * FROM work_shifts ORDER BY id ASC");
while($row = mysqli_fetch_assoc($q_shf)) { $shifts[] = $row; }

// D. Jabatan
$roles = [];
$q_roles = mysqli_query($conn, "SELECT * FROM job_roles ORDER BY id ASC");
while($row = mysqli_fetch_assoc($q_roles)) { $roles[] = $row; }

// E. Hari Libur
$holidays = [];
$q_hol = mysqli_query($conn, "SELECT * FROM special_dates ORDER BY date ASC");
while($row = mysqli_fetch_assoc($q_hol)) { $holidays[] = $row; }

?>