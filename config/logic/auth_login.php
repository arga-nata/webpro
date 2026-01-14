<?php
session_start();
include '../db.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $query = "SELECT * FROM admins WHERE username = '$username'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);

        if (password_verify($password, $row['password'])) {
            
            // 1. REGENERATE SESSION ID (PENTING UNTUK KEAMANAN)
            session_regenerate_id(true); 
            $current_session_id = session_id();

            // 2. UPDATE DATABASE: CATAT SESSION ID BARU
            // Ini yang bikin user lama tertendang
            $id = $row['id'];
            $update_sql = "UPDATE admins SET last_session_id = '$current_session_id' WHERE id = '$id'";
            mysqli_query($conn, $update_sql);

            // 3. SET SESSION PHP
            $_SESSION['login'] = true;
            $_SESSION['admin_user'] = $row['username'];
            $_SESSION['admin_id'] = $row['id']; // Kita butuh ID ini buat pengecekan nanti

            $_SESSION['flash_status'] = 'success';
            $_SESSION['flash_message'] = 'Login Berhasil! Sesi aman terkunci.';
            
            header("Location: ../../admin/home.php");
            exit;

        } else {
            $_SESSION['flash_status'] = 'error';
            $_SESSION['flash_message'] = 'Password salah!';
            header("Location: ../../admin/login.php");
            exit;
        }
    } else {
        $_SESSION['flash_status'] = 'error';
        $_SESSION['flash_message'] = 'Username tidak ditemukan!';
        header("Location: ../../admin/login.php");
        exit;
    }
} else {
    header("Location: ../../admin/login.php");
    exit;
}
?>