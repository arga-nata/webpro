<?php
$pageTitle = "Menu - Street Sushi";
$currentPage = "menu";

include 'config/db.php';
include 'includes/header.php';
$query = "SELECT m.*, c.name as category_name 
            FROM menu_items m 
            LEFT JOIN categories c ON m.category_id = c.id
            ORDER BY m.id DESC";
$result = mysqli_query($conn, $query);

$cat_query = "SELECT * FROM categories ORDER BY name ASC";
$cat_result = mysqli_query($conn, $cat_query);
?>

<link rel="stylesheet" href="assets/css/menu.css">

<style>
  .menu-card.hide {
    display: none;
  }
</style>

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
    <button class="filter-btn active" data-filter="all">All Menu</button>
    <?php
    if (mysqli_num_rows($cat_result) > 0) {
      while ($cat = mysqli_fetch_assoc($cat_result)) {
        echo '<button class="filter-btn" data-filter="' . $cat['id'] . '">' . $cat['name'] . '</button>';
      }
    }
    ?>
  </div>

  <div class="menu-grid">
    <?php
    // Cek jika ada data menu
    if (mysqli_num_rows($result) > 0) {
      while ($row = mysqli_fetch_assoc($result)) {
        // Format Harga (Contoh: 25000 -> 25.000)
        $price = number_format($row['base_price'], 0, ',', '.');

        // Gambar (Pakai gambar default jika null)
        $image = !empty($row['image']) ? "assets/images/" . $row['image'] : "assets/images/sushi-default.jpg";
        ?>
        <div class="menu-card" data-category="<?php echo $row['category_id']; ?>">
          <div class="card-img-wrapper">
            <img src="<?php echo $image; ?>" alt="<?php echo $row['name']; ?>">

            <?php if ($row['base_price'] >= 35000): ?>
              <span class="badge-best">Best Seller</span>
            <?php elseif (strpos(strtolower($row['name']), 'salmon') !== false): ?>
              <span class="badge-fresh">Fresh Catch</span>
            <?php endif; ?>
          </div>
          <div class="card-info">
            <div class="info-header">
              <h3><?php echo $row['name']; ?></h3>
              <span class="jp-name"><?php echo $row['category_name']; ?></span>
            </div>
            <p class="desc"><?php echo $row['description']; ?></p>

            <div class="price-action">
              <div class="price-tag">
                <span class="currency">Rp</span>
                <span class="amount"><?php echo $price; ?></span>
              </div>
              <a href="order.php" class="order-btn">
                Order <i class='bx bx-right-arrow-alt'></i>
              </a>
            </div>
          </div>
        </div>

        <?php
      }
    } else {
      echo "<p style='text-align:center; width:100%; color:white;'>Belum ada menu yang tersedia.</p>";
    }
    ?>

    <!-- <div class="menu-card">
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
    </div> -->

    <!-- <div class="menu-card">
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
    </div> -->

  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const filterBtns = document.querySelectorAll('.filter-btn');
      const menuCards = document.querySelectorAll('.menu-card');

      filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
          // 1. Hapus class 'active' dari semua tombol
          filterBtns.forEach(b => b.classList.remove('active'));
          // 2. Tambah class 'active' ke tombol yang diklik
          btn.classList.add('active');

          // 3. Ambil nilai kategori yang dipilih
          const filterValue = btn.getAttribute('data-filter');

          // 4. Cek semua kartu menu
          menuCards.forEach(card => {
            const cardCategory = card.getAttribute('data-category');

            if (filterValue === 'all' || filterValue === cardCategory) {
              // Kalau pilih 'All' atau Kategori cocok -> Tampilkan
              card.classList.remove('hide');
            } else {
              // Kalau tidak cocok -> Sembunyikan
              card.classList.add('hide');
            }
          });
        });
      });
    });
  </script>

</main>

<?php include 'includes/footer.php'; ?>