<!DOCTYPE html>
<html lang="id" data-theme="dark">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title><?php echo isset($pageTitle) ? $pageTitle : "Street Sushi"; ?></title>

  <link rel="shortcut icon" href="assets/images/favicon.ico" type="image/x-icon">

  <link rel="stylesheet" href="assets/css/page.css">
  <link rel="stylesheet" href="assets/css/navbar.css">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Dela+Gothic+One&family=Poppins:wght@300;400;600;800&display=swap" rel="stylesheet">
</head>

<body>

  <nav class="navbar">
    <button class="mobile-menu-btn" id="menu-toggle">☰</button>

    <div class="logo">
      <i class="bx bxs-bowl-hot"></i> STREET SUSHI
    </div>

    <ul class="nav-links" id="nav-menu">
      <li>
        <a href="index.php" class="<?php echo ($currentPage == 'home') ? 'active' : ''; ?>">Beranda</a>
      </li>
      <li>
        <a href="artikel.php" class="<?php echo ($currentPage == 'artikel') ? 'active' : ''; ?>">Artikel</a>
      </li>
      <li>
        <a href="about.php" class="<?php echo ($currentPage == 'about') ? 'active' : ''; ?>">Tentang</a>
      </li>
      <li>
        <a href="galeri.php" class="<?php echo ($currentPage == 'galeri') ? 'active' : ''; ?>">Galeri</a>
      </li>
      <li>
        <a href="review.php" class="<?php echo ($currentPage == 'review') ? 'active' : ''; ?>">Review</a>
      </li>
      <li>
        <a href="chef.php" class="<?php echo ($currentPage == 'chef') ? 'active' : ''; ?>">Chef's</a>
      </li>
       <li>
        <a href="menu.php" class="<?php echo ($currentPage == 'menu') ? 'active' : ''; ?>">Menu</a>
      </li>
       <li>
        <a href="order.php" class="<?php echo ($currentPage == 'order') ? 'active' : ''; ?>">Order</a>
      </li>
    </ul>

    <div class="nav-actions">
      <button id="theme-toggle" class="theme-btn">
        <i class="bx bx-moon" id="theme-icon"></i>
      </button>

      <a href="admin/login.php" class="login-nav-btn">
        <i class="bx bx-user"></i>
        <span class="login-text">Login</span>
      </a>
    </div>
  </nav>