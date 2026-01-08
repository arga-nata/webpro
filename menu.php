<?php 
  $pageTitle = "Premium Menu Selection - Street Sushi"; 
  $currentPage = "menu";             
  include 'includes/header.php'; 
?>

<link rel="stylesheet" href="assets/css/menu.css">

<header class="menu-hero">
  
  <div class="hero-menu-image">
    <img src="assets/images/gambar2.jpg" alt="Premium Sushi Platter">
  </div>

  <div class="hero-overlay"></div>
  
  <div class="hero-content text-center">
    <span class="japanese-tag">プレミアムメニュー</span>
    <h1>Taste the <span class="text-gradient">Future</span></h1>
    <p>Eksplorasi rasa autentik Jepang yang dipadukan dengan inovasi kuliner modern.</p>
  </div>
</header>

<main class="container-menu">

  <div class="category-filter">
    <button class="filter-btn active">All Menu</button>
    <button class="filter-btn">Signature Roll</button>
    <button class="filter-btn">Classic</button>
    <button class="filter-btn">Vegetarian</button>
  </div>

  <div class="menu-grid">
    
    <div class="menu-card">
      <div class="card-img-wrapper">
        <img src="assets/images/Salmon-roll.jpg" alt="Salmon Roll">
        <span class="badge-fresh">Fresh Catch</span>
      </div>
      <div class="card-info">
        <div class="info-header">
          <h3>Salmon Roll</h3>
          <span class="jp-name">サーモン</span>
        </div>
        <p class="desc">Isi salmon segar, nasi Jepang pulen, dan nori premium. Disajikan dengan saus spesial racikan chef.</p>
        
        <div class="price-action">
          <div class="price-tag">
            <span class="currency">Rp</span>
            <span class="amount">25.000</span>
          </div>
          <a href="order.php" class="order-btn">
            Order <i class='bx bx-right-arrow-alt'></i>
          </a>
        </div>
      </div>
    </div>

    <div class="menu-card">
      <div class="card-img-wrapper">
        <img src="assets/images/tuna-roll.jpg" alt="Tuna Roll">
      </div>
      <div class="card-info">
        <div class="info-header">
          <h3>Tuna Roll</h3>
          <span class="jp-name">マグロ</span>
        </div>
        <p class="desc">Tuna merah segar berpadu dengan nasi lembut dan wijen panggang untuk aroma yang kaya.</p>
        
        <div class="price-action">
          <div class="price-tag">
            <span class="currency">Rp</span>
            <span class="amount">22.000</span>
          </div>
          <a href="order.php" class="order-btn">
            Order <i class='bx bx-right-arrow-alt'></i>
          </a>
        </div>
      </div>
    </div>

    <div class="menu-card">
      <div class="card-img-wrapper">
        <img src="assets/images/Tamago-Roll.jpg" alt="Tamago Roll">
        <span class="badge-best">Best Seller</span>
      </div>
      <div class="card-info">
        <div class="info-header">
          <h3>Tamago Roll</h3>
          <span class="jp-name">卵焼き</span>
        </div>
        <p class="desc">Omelet Jepang manis gurih yang lembut, favorit semua kalangan dari anak-anak hingga dewasa.</p>
        
        <div class="price-action">
          <div class="price-tag">
            <span class="currency">Rp</span>
            <span class="amount">18.000</span>
          </div>
          <a href="order.php" class="order-btn">
            Order <i class='bx bx-right-arrow-alt'></i>
          </a>
        </div>
      </div>
    </div>

    <div class="menu-card">
      <div class="card-img-wrapper">
        <img src="assets/images/Shrimp-Tempura-Roll.jpg" alt="Shrimp Tempura">
      </div>
      <div class="card-info">
        <div class="info-header">
          <h3>Shrimp Tempura</h3>
          <span class="jp-name">海老天</span>
        </div>
        <p class="desc">Udang tempura renyah dengan saus mayo pedas manis. Tekstur krispi yang bikin nagih.</p>
        
        <div class="price-action">
          <div class="price-tag">
            <span class="currency">Rp</span>
            <span class="amount">27.000</span>
          </div>
          <a href="order.php" class="order-btn">
            Order <i class='bx bx-right-arrow-alt'></i>
          </a>
        </div>
      </div>
    </div>

    <div class="menu-card">
      <div class="card-img-wrapper">
        <img src="assets/images/Crab-Roll.jpg" alt="Crab Roll">
      </div>
      <div class="card-info">
        <div class="info-header">
          <h3>Crab Roll</h3>
          <span class="jp-name">カニ</span>
        </div>
        <p class="desc">Daging kepiting lembut dengan irisan timun segar. Pilihan ringan namun memuaskan.</p>
        
        <div class="price-action">
          <div class="price-tag">
            <span class="currency">Rp</span>
            <span class="amount">20.000</span>
          </div>
          <a href="order.php" class="order-btn">
            Order <i class='bx bx-right-arrow-alt'></i>
          </a>
        </div>
      </div>
    </div>

    <div class="menu-card">
      <div class="card-img-wrapper">
        <img src="assets/images/Avocado-Roll.jpg" alt="Avocado Roll">
        <span class="badge-vegan">Vegan</span>
      </div>
      <div class="card-info">
        <div class="info-header">
          <h3>Avocado Roll</h3>
          <span class="jp-name">アボカド</span>
        </div>
        <p class="desc">Alpukat mentega yang creamy dengan sedikit minyak wijen. Sehat, lezat, dan menyegarkan.</p>
        
        <div class="price-action">
          <div class="price-tag">
            <span class="currency">Rp</span>
            <span class="amount">19.000</span>
          </div>
          <a href="order.php" class="order-btn">
            Order <i class='bx bx-right-arrow-alt'></i>
          </a>
        </div>
      </div>
    </div>

  </div>
</main>

<?php include 'includes/footer.php'; ?>