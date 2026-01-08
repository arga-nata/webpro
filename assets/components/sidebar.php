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
        <span>Pesanan</span>
      </a>
    </li>
    
    <li>
      <a href="menu.php" class="<?= ($currentPage == 'menu') ? 'active' : '' ?>">
        <i class='bx bxs-sushi'></i>
        <span>Menu</span>
      </a>
    </li>
    
    <li>
      <a href="laporan.php" class="<?= ($currentPage == 'laporan') ? 'active' : '' ?>">
        <i class='bx bxs-report'></i>
        <span>Laporan</span>
      </a>
    </li>
    
    <li>
      <a href="users.php" class="<?= ($currentPage == 'users') ? 'active' : '' ?>">
        <i class='bx bxs-user-detail'></i>
        <span>Pelanggan</span>
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