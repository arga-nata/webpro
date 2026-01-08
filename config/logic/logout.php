<?php
session_start();

// 1. Hapus semua session
session_unset();
session_destroy();

// 2. Mulai session baru hanya untuk pesan notifikasi (Flash Message)
session_start();
$_SESSION['flash_status'] = 'success';
$_SESSION['flash_message'] = 'Anda berhasil logout. Sampai jumpa!';

// 3. Lempar balik ke halaman login
header("Location: ../../admin/login.php");
exit;
?>