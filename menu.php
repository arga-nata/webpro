<?php
$pageTitle = "Menu - Street Sushi";
$currentPage = "menu";

include 'config/db.php';
include 'includes/header.php';

// Query ambil data menu
$query = "SELECT m.*, c.name as category_name 
            FROM menu_items m 
            LEFT JOIN categories c ON m.category_id = c.id
            ORDER BY m.id DESC";
$result = mysqli_query($conn, $query);

// Query ambil kategori untuk filter
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
    <img src="assets/images/menu/gambar2.jpg" alt="Premium Sushi Platter">
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

        // 1. Format Harga
        $price = number_format($row['base_price'], 0, ',', '.');

        // 2. LOGIKA GAMBAR (CERDAS)
        // A. Set Default dulu (Ini akan dipakai jika DB NULL atau File tidak ketemu)
        // Pastikan kamu punya file 'default.jpg' di folder assets/images/menu/
        $img_src = "assets/images/menu/Default.jpg"; 

        // B. Cek apakah database punya data gambar (TIDAK NULL/KOSONG)?
        if (!empty($row['image'])) {
            // Cek Prioritas 1: Folder Uploads (Untuk data yang baru diinput admin)
            if (file_exists("assets/uploads/" . $row['image'])) {
                $img_src = "assets/uploads/" . $row['image'];
            } 
            // Cek Prioritas 2: Folder Menu Bawaan (Untuk data bawaan tema)
            elseif (file_exists("assets/images/menu/" . $row['image'])) {
                $img_src = "assets/images/menu/" . $row['image'];
            }
            // Cek Prioritas 3: Folder Images Root (Jaga-jaga)
            elseif (file_exists("assets/images/" . $row['image'])) {
                $img_src = "assets/images/" . $row['image'];
            }
        }
        // Jika $row['image'] kosong, dia akan melewati blok IF dan tetap pakai default.jpg
    ?>

        <div class="menu-card" data-category="<?php echo $row['category_id']; ?>">
          <div class="card-img-wrapper">
            <img src="<?php echo $img_src; ?>" alt="<?php echo $row['name']; ?>" loading="lazy">

            <?php if ($row['base_price'] >= 45000): ?>
              <span class="badge-best">Best Seller</span>
            <?php elseif (strpos(strtolower($row['name']), 'salmon') !== false): ?>
              <span class="badge-fresh">Fresh Catch</span>
            <?php endif; ?>
          </div>

          <div class="card-info">
            <div class="info-header">
              <h3><?php echo $row['name']; ?></h3>
              <span class="jp-name"><?php echo isset($row['category_name']) ? $row['category_name'] : 'Menu'; ?></span>
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
      // Tampilan jika Database Kosong
      echo "<p style='text-align:center; width:100%; color:#92929f; padding:50px; font-style:italic;'>Belum ada menu yang tersedia saat ini.</p>";
    }
    ?>

  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
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