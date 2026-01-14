<?php
require_once 'config/db.php';

$query_settings = "SELECT * FROM system_settings LIMIT 1";
$result_settings = mysqli_query($conn, $query_settings);
$settings = mysqli_fetch_assoc($result_settings);

$map_hari = [
  'Monday' => 'Senin',
  'Tuesday' => 'Selasa',
  'Wednesday' => 'Rabu',
  'Thursday' => 'Kamis',
  'Friday' => 'Jumat',
  'Saturday' => 'Sabtu',
  'Sunday' => 'Minggu'
];
$hari_ini = $map_hari[date('l')];

$query_jadwal = "SELECT * FROM store_schedule WHERE day_name = '$hari_ini'";
$result_jadwal = mysqli_query($conn, $query_jadwal);
$jadwal = mysqli_fetch_assoc($result_jadwal);

$query_libur = "SELECT * FROM special_dates WHERE date = CURDATE()";
$result_libur = mysqli_query($conn, $query_libur);
$is_holiday = mysqli_num_rows($result_libur) > 0;

$status_resto = "TUTUP";
$jam_sekarang = date('H:i:s');
$jam_operasional_text = "Tutup";

if ($jadwal && $jadwal['is_closed'] == 0) {
  $buka = date('H:i', strtotime($jadwal['open_time']));
  $tutup = date('H:i', strtotime($jadwal['close_time']));
  $jam_operasional_text = "$buka - $tutup WIB";
} else {
  $jam_operasional_text = "Libur";
}

if ($settings['force_status'] == 'open') {
  $status_resto = "BUKA";
} elseif ($settings['force_status'] == 'close') {
  $status_resto = "TUTUP";
} else {
  if ($is_holiday || $jadwal['is_closed'] == 1) {
    $status_resto = "LIBUR";
  } elseif ($jam_sekarang >= $jadwal['open_time'] && $jam_sekarang <= $jadwal['close_time']) {
    $status_resto = "BUKA";
  } else {
    $status_resto = "TUTUP";
  }
}

$query_antrian = "SELECT COUNT(*) as total FROM orders WHERE status IN ('pending', 'confirmed', 'cooking')";
$result_antrian = mysqli_query($conn, $query_antrian);
$data_antrian = mysqli_fetch_assoc($result_antrian);
$jumlah_antrian = $data_antrian['total'];

$total_meja = $settings['total_tables'];
$meja_kosong = $total_meja - $jumlah_antrian;
if ($meja_kosong < 0)
  $meja_kosong = 0;

$hype_items = [];

$query_hype = "SELECT m.*, SUM(od.quantity) as total_sold 
               FROM menu_items m
               JOIN order_details od ON m.id = od.menu_item_id
               JOIN orders o ON od.order_id = o.id
               WHERE DATE(o.created_at) = CURDATE() 
               AND m.is_available = 1 
               AND o.status != 'cancelled'
               GROUP BY m.id 
               ORDER BY total_sold DESC 
               LIMIT 3";

$result_hype = mysqli_query($conn, $query_hype);

while ($row = mysqli_fetch_assoc($result_hype)) {
  $hype_items[] = $row;
}

if (count($hype_items) < 3) {
  $needed = 3 - count($hype_items);

  $existing_ids = [];
  foreach ($hype_items as $item) {
    $existing_ids[] = $item['id'];
  }

  if (empty($existing_ids))
    $existing_ids = [0];

  $ids_string = implode(',', $existing_ids);

  $query_random = "SELECT * FROM menu_items 
                     WHERE is_available = 1 
                     AND id NOT IN ($ids_string) 
                     ORDER BY RAND() 
                     LIMIT $needed";

  $result_random = mysqli_query($conn, $query_random);
  while ($row = mysqli_fetch_assoc($result_random)) {
    $hype_items[] = $row;
  }
}

$pageTitle = "Home - Street Sushi";
$currentPage = "home";
include 'includes/header.php';
?>

<header class="hero">
  <div class="hero-body">
    <div class="hero-content">
      <span class="japanese-tag">未来の寿司</span>
      <h1>Street Food,<br>Premium Taste.</h1>
      <p>Nikmati sensasi sushi berkualitas restoran dengan kearifan lokal, disajikan dengan teknologi dan kecepatan masa
        depan.</p>
      <div style="display: flex; gap: 10px;">
        <div class="hero-buttons">
          <a href="order.php" class="cta-btn primary">Pesan Sekarang</a>
        </div>
        <div class="hero-buttons">
          <a href="track.php" class="cta-btn secondary">Cek Pesanan</a>
        </div>
      </div>
    </div>

    <div class="hero-image">
      <img src="assets/images/menu/Iconik.png" alt="iconik">
      <div class="glow-effect"></div>
    </div>
  </div>

  <div id="status" class="live-status-bar">
    <div class="status-item">
      <div class="status-icon <?= ($status_resto == 'BUKA') ? 'pulse-green' : ''; ?>">
        <?= ($status_resto == 'BUKA') ? '●' : '🔴'; ?>
      </div>
      <div>
        <span class="label">Status Resto</span>
        <span class="value">
          <?= $status_resto; ?> SEKARANG
        </span>
      </div>
    </div>
    <div class="status-item">
      <div class="status-icon">🪑</div>
      <div>
        <span class="label">Meja Kosong</span>
        <span class="value">
          <?= $meja_kosong; ?> Meja
        </span>
      </div>
    </div>
    <div class="status-item">
      <div class="status-icon">⏳</div>
      <div>
        <span class="label">Antrian</span>
        <span class="value">
          <?= $jumlah_antrian; ?> Pesanan
        </span>
      </div>
    </div>
    <div class="status-item">
      <div class="status-icon">🕒</div>
      <div>
        <span class="label">Jam Operasional</span>
        <span class="value">
          <?= $jam_operasional_text; ?>
        </span>
      </div>
    </div>
  </div>
</header>

<main class="container">
  <section id="menu-pilihan" class="quick-menu">
    <div class="section-header">
      <h2>Sedang Hype Hari Ini 🔥</h2>
    </div>

    <div class="menu-grid">
      <?php foreach ($hype_items as $item): ?>
        <div class="menu-card-glass">
          <?php
          $img_path = "assets/uploads/" . $item['image'];
          if (!file_exists($img_path) || empty($item['image'])) {
            $img_path = "assets/images/Default.jpg";
          }
          ?>
          <img src="<?= $img_path; ?>" alt="<?= htmlspecialchars($item['name']); ?>">

          <div class="menu-info">
            <h3><?= htmlspecialchars($item['name']); ?></h3>
            <p>Rp <?= number_format($item['base_price'], 0, ',', '.'); ?></p>

            <button class="add-btn" onclick="window.location.href='order.php?add=<?= $item['id']; ?>'">+</button>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="view-all-wrapper">
      <a href="menu.php" class="view-all">Lihat Semua Menu →</a>
    </div>
  </section>

  <section class="values-grid">
    <div class="section-header">
      <h2>Mengapa Harus Street Sushi? ✨</h2>
    </div>
    <div class="card">
      <div class="card-icon">💎</div>
      <h3>01. Kualitas Premium</h3>
      <p>Kami mematahkan stigma sushi murah. Menggunakan bahan segar standar restoran bintang 5, namun dengan harga yang
        tetap ramah di kantong.</p>
    </div>
    <div class="card">
      <div class="card-icon">🌶️</div>
      <h3>02. Fusion Taste</h3>
      <p>Lidah Indonesia banget! Nikmati inovasi rasa unik seperti <i>Sushi Rendang</i> & <i>Ayam Geprek</i> yang tidak
        akan kamu temukan di Jepang.</p>
    </div>
    <div class="card">
      <div class="card-icon">⚡</div>
      <h3>03. Smart Service</h3>
      <p>Lupakan antrian manual. Pesan, bayar, dan pantau status pesanan langsung dari HP via QR Code. Cepat, higienis,
        dan futuristik.</p>
    </div>
  </section>

  <section class="testimonials">
    <h2 class="section-title">Suara Pelanggan</h2>
    <div class="testi-grid">
      <div class="testi-card">
        <div class="quote-icon">“</div>
        <p>Packaging rapi, nyampe masih hangat. Bakalan repeat order sih ini!</p>
        <div class="user">
          <div class="avatar">R</div>
          <span>Rani Putri, Mahasiswi</span>
        </div>
      </div>
      <div class="testi-card">
        <div class="quote-icon">“</div>
        <p>Sushi rasa rendang ternyata enak banget! Unik dan bikin nagih.</p>
        <div class="user">
          <div class="avatar">A</div>
          <span>Andi, Pegawai</span>
        </div>
      </div>
      <div class="testi-card">
        <div class="quote-icon">“</div>
        <p>Pelayanan cepet banget. Pas buat makan siang pas jam istirahat kantor.</p>
        <div class="user">
          <div class="avatar">S</div>
          <span>Sinta, Karyawan</span>
        </div>
      </div>
    </div>
  </section>
</main>

<?php include 'includes/footer.php'; ?>