<?php 
session_start();
// Mencegah Cache Browser (Biar kalau di-Back nggak nyangkut)
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (isset($_SESSION['login']) && $_SESSION['login'] === true) {
    header("Location: home.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login – Street Sushi</title>
    <link rel="icon" type="image/x-icon" href="../assets/images/favicon.ico">

    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Dela+Gothic+One&family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="../assets/css/dashboard/login.css">
    
    <link rel="stylesheet" href="../assets/css/toaster.css">
</head>
<body>

    <div class="bg-grid"></div>

    <div class="login-wrapper">
        <div class="login-card">

            <div class="login-visual">
                <img src="../assets/images/cafe3.jpg" alt="Admin Visual">
                <div class="visual-content">
                    <div class="badge">ADMIN PORTAL</div>
                    <h2>CONTROL<br>PANEL</h2>
                    <p>Manage orders & menu efficiently.</p>
                </div>
            </div>

            <div class="login-form-side">
                
                <div class="brand-header">
                    <img src="../assets/images/logo.png" alt="Logo"> 
                    <span>STREET SUSHI</span>
                </div>

                <div class="catchphrase">
                    <h3>System Access</h3>
                    <p>Authentication required to proceed.</p>
                </div>

                <form action="../config/logic/auth_login.php" method="POST">

                    <div class="input-group">
                        <input type="text" id="username" name="username" required autocomplete="off">
                        <label for="username">USERNAME</label>
                        <i class='bx bx-user'></i>
                    </div>

                    <div class="input-group">
                        <input type="password" id="password" name="password" required>
                        <label for="password">PASSWORD</label>
                        <i class='bx bx-hide toggle' onclick="togglePassword()"></i>
                    </div>

                    <button type="submit" class="btn-login">
                        ENTER DASHBOARD <i class='bx bx-right-arrow-alt'></i>
                    </button>

                </form>
            </div>

        </div>
    </div>

    <script>
        function togglePassword() {
            const pw = document.getElementById('password');
            const icon = document.querySelector('.toggle');
            if (pw.type === 'password') {
                pw.type = 'text';
                icon.classList.replace('bx-hide', 'bx-show');
            } else {
                pw.type = 'password';
                icon.classList.replace('bx-show', 'bx-hide');
            }
        }
    </script>

    <script src="../assets/js/toaster.js"></script>

    <?php
    // Cek apakah ada pesan di Session (Flash Message)
    if (isset($_SESSION['flash_status']) && isset($_SESSION['flash_message'])) {
        $status = $_SESSION['flash_status']; // 'success' atau 'error'
        $title  = ($status == 'success') ? 'ACCESS GRANTED' : 'ACCESS DENIED';
        $msg    = $_SESSION['flash_message'];
        
        // Panggil JS function showToast
        echo "<script>showToast('$status', '$title', '$msg');</script>";

        // Hapus session biar tidak muncul lagi pas refresh
        unset($_SESSION['flash_status']);
        unset($_SESSION['flash_message']);
    }
    ?>

</body>
</html>