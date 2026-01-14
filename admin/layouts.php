<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= isset($pageTitle) ? $pageTitle : 'Street Sushi Admin'; ?></title>

  <link rel="shortcut icon" href="../assets/images/favicon.ico" type="image/x-icon">

  <link rel="stylesheet" href="../assets/css/dashboard2/global.css">

  <?php if (isset($customCSS)) echo $customCSS; ?>

  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>

  <div class="wrapper">

    <?php include '../assets/components/sidebar.php'; ?>

    <div class="main-content">

      <?php include '../assets/components/topbar.php'; ?>

      <div class="content-padding">
        <?php
        if (isset($content)) {
          echo $content;
        }
        ?>
      </div>

    </div>
  </div>

  <div class="mobile-overlay"></div>

  <script>
    // 1. Ambil elemen yang dibutuhkan
    const sidebar = document.querySelector('.sidebar');
    const toggleBtn = document.querySelector('.toggle-btn');
    const overlay = document.querySelector('.mobile-overlay');

    // 2. Cek apakah elemen ada (Mencegah error jika salah satu hilang)
    if (sidebar && toggleBtn && overlay) {

      // Event Klik Tombol Menu (Garis Tiga)
      toggleBtn.addEventListener('click', () => {
        if (window.innerWidth > 768) {
          // Di Laptop: Mode Minimize (Kecil)
          sidebar.classList.toggle('close');
        } else {
          // Di HP: Mode Muncul/Sembunyi
          sidebar.classList.toggle('active');
          overlay.classList.toggle('active');
          sidebar.classList.remove('close'); // Pastikan tidak close saat aktif di HP
        }
      });

      // Event Klik Overlay (Layar Gelap) untuk tutup sidebar di HP
      overlay.addEventListener('click', () => {
        sidebar.classList.remove('active');
        overlay.classList.remove('active');
      });
    }
  </script>

  <?php if (isset($customScript)) echo $customScript; ?>

  <?php if (isset($_SESSION['flash_status']) && isset($_SESSION['flash_message'])): ?>
    <div id="toast-container">
      <div class="toast-box toast-<?= $_SESSION['flash_status']; ?>">
        <?php if ($_SESSION['flash_status'] == 'success'): ?>
          <i class='bx bxs-check-circle'></i>
        <?php elseif ($_SESSION['flash_status'] == 'error'): ?>
          <i class='bx bxs-x-circle'></i>
        <?php else: ?>
          <i class='bx bxs-info-circle'></i>
        <?php endif; ?>

        <div class="toast-content">
          <span class="toast-title"><?= ucfirst($_SESSION['flash_status']); ?></span>
          <span class="toast-msg"><?= $_SESSION['flash_message']; ?></span>
        </div>
      </div>
    </div>

    <?php
    unset($_SESSION['flash_status']);
    unset($_SESSION['flash_message']);
    ?>

    <script>
      // Hilangkan Toaster otomatis setelah 4 detik
      setTimeout(() => {
        const toast = document.querySelector('.toast-box');
        if (toast) {
          toast.classList.add('hide');
          setTimeout(() => toast.remove(), 500); // Hapus dari DOM
        }
      }, 4000);
    </script>
  <?php endif; ?>

</body>

</html>