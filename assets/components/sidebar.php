<div class="sidebar">
  <div class="brand">
    <img src="../assets/images/logo.png" alt="Logo">
    <span>STREET SUSHI</span>
  </div>

  <ul class="side-menu">
    <li>
      <a href="dashboard.php" class="<?= ($currentPage == 'home') ? 'active' : '' ?>">
        <i class='bx bxs-dashboard'></i>
        <span>Home</span>
      </a>
    </li>
    
    <li>
      <a href="pesanan.php" class="<?= ($currentPage == 'pesanan') ? 'active' : '' ?>">
        <i class='bx bxs-shopping-bag'></i>
        <span>History</span>
      </a>
    </li>
    
    <li>
      <a href="manage.php" class="<?= ($currentPage == 'manage') ? 'active' : '' ?>">
        <i class='bx bxs-sushi'></i>
        <span>Manage</span>
      </a>
    </li>
    
    <li>
      <a href="laporan.php" class="<?= ($currentPage == 'laporan') ? 'active' : '' ?>">
        <i class='bx bxs-report'></i>
        <span>Laporan</span>
      </a>
    </li>

  </ul>

  <div class="sidebar-footer">
    <a href="../config/logic/logout.php" class="btn-logout">
      <i class='bx bx-log-out'></i>
      <span>Logout</span>
    </a>
  </div>
</div>