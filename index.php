<?php 
  $pageTitle = "Home - Street Sushi"; 
  $currentPage = "home";             
  include 'includes/header.php'; 
?>

<header class="hero">
  <div class="hero-body">
    <div class="hero-content">
      <span class="japanese-tag">未来の寿司</span>
      <h1>Street Food,<br>Premium Taste.</h1>
      <p>Nikmati sensasi sushi berkualitas restoran dengan kearifan lokal, disajikan dengan teknologi dan kecepatan masa depan.</p>
      <div class="hero-buttons">
        <a href="#menu-pilihan" class="cta-btn primary">Pesan Sekarang</a>
        <a href="#status" class="cta-btn secondary">Cek Antrian</a>
      </div>
    </div>

    <div class="hero-image">
      <img src="assets/images/Iconik.png" alt="iconik">
      <div class="glow-effect"></div>
    </div>
  </div>

  <div id="status" class="live-status-bar">
    <div class="status-item">
      <div class="status-icon pulse-green">●</div>
      <div>
        <span class="label">Status Resto</span>
        <span class="value">BUKA SEKARANG</span>
      </div>
    </div>
    <div class="status-item">
      <div class="status-icon">🪑</div>
      <div>
        <span class="label">Meja Kosong</span>
        <span class="value">4 Meja Tersedia</span>
      </div>
    </div>
    <div class="status-item">
      <div class="status-icon">⏳</div>
      <div>
        <span class="label">Antrian</span>
        <span class="value">2 Pesanan</span>
      </div>
    </div>
    <div class="status-item">
      <div class="status-icon">🕒</div>
      <div>
        <span class="label">Jam Operasional</span>
        <span class="value">10:00 - 22:00 WIB</span>
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
      <div class="menu-card-glass">
        <img src="assets/images/Salmon-roll.jpg" alt="Salmon Roll">
        <div class="menu-info">
          <h3>Salmon Roll</h3>
          <p>Rp 25.000</p>
          <button class="add-btn">+</button>
        </div>
      </div>
      <div class="menu-card-glass">
        <img src="assets/images/tuna-roll.jpg" alt="Tuna Roll">
        <div class="menu-info">
          <h3>Tuna Roll</h3>
          <p>Rp 22.000</p>
          <button class="add-btn">+</button>
        </div>
      </div>
      <div class="menu-card-glass">
        <img src="assets/images/Tamago-Roll.jpg" alt="Tamago Roll">
        <div class="menu-info">
          <h3>Tamago Roll</h3>
          <p>Rp 18.000</p>
          <button class="add-btn">+</button>
        </div>
      </div>
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
      <p>Kami mematahkan stigma sushi murah. Menggunakan bahan segar standar restoran bintang 5, namun dengan harga yang tetap ramah di kantong.</p>
    </div>
    <div class="card">
      <div class="card-icon">🌶️</div>
      <h3>02. Fusion Taste</h3>
      <p>Lidah Indonesia banget! Nikmati inovasi rasa unik seperti <i>Sushi Rendang</i> & <i>Ayam Geprek</i> yang tidak akan kamu temukan di Jepang.</p>
    </div>
    <div class="card">
      <div class="card-icon">⚡</div>
      <h3>03. Smart Service</h3>
      <p>Lupakan antrian manual. Pesan, bayar, dan pantau status pesanan langsung dari HP via QR Code. Cepat, higienis, dan futuristik.</p>
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