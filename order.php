<?php 
  $pageTitle = "Order Station - Street Sushi"; 
  $currentPage = "order";             
  include 'includes/header.php'; 
?>

<link rel="stylesheet" href="assets/css/order.css">

<header class="order-hero">
  
  <div class="hero-content">
    <span class="sub-title">READY TO SERVE</span>
    <h1>Start Your <span class="text-gradient">Order</span></h1>
    <p>Isi formulir di bawah untuk pengalaman kuliner terbaik yang diantar langsung ke pintu Anda.</p>
  </div>

  <div class="hero-order-image">
  <div class="hero-slideshow">
    <img src="assets/images/animasi1.png" alt="Ornamen 1" class="slide-img">
    <img src="assets/images/animasi2.png" alt="Ornamen 2" class="slide-img">
    <img src="assets/images/animasi3.png" alt="Ornamen 3" class="slide-img">
    
    <img src="assets/images/animasi4.png" alt="Ornamen 4" class="slide-img">
    <img src="assets/images/animasi5.png" alt="Ornamen 5" class="slide-img">
  </div>
</div>

</header>

<div class="ticker-wrap">
  <div class="ticker-fade-l"></div>
  <div class="ticker-fade-r"></div>

  <div class="ticker-track">
    <div class="ticker-content">
      <div class="ticker-item"><i class='bx bxs-leaf'></i> FRESH INGREDIENTS</div>
      <div class="ticker-item"><i class='bx bxs-hot'></i> MADE BY ORDER</div>
      <div class="ticker-item"><i class='bx bxs-time-five'></i> FAST DELIVERY</div>
      <div class="ticker-item"><i class='bx bxs-award'></i> PREMIUM QUALITY</div>
      <div class="ticker-item"><i class='bx bxs-certification'></i> 100% HALAL</div>
    </div>
    
    <div class="ticker-content">
      <div class="ticker-item"><i class='bx bxs-leaf'></i> FRESH INGREDIENTS</div>
      <div class="ticker-item"><i class='bx bxs-hot'></i> MADE BY ORDER</div>
      <div class="ticker-item"><i class='bx bxs-time-five'></i> FAST DELIVERY</div>
      <div class="ticker-item"><i class='bx bxs-award'></i> PREMIUM QUALITY</div>
      <div class="ticker-item"><i class='bx bxs-certification'></i> 100% HALAL</div>
    </div>
  </div>
</div>

<main class="container-order">
  
  <div class="order-layout">
    
    <section class="form-section">
      <div class="form-card">
        <div class="form-header">
          <h3>Customer Details</h3>
          <p>Informasi pengiriman pesanan Anda.</p>
        </div>

        <form action="process_order.php" method="POST">
          
          <div class="form-row">
            <div class="form-group">
              <label for="nama">Nama Lengkap</label>
              <input type="text" id="nama" name="nama" placeholder="Masukkan nama Anda..." required>
            </div>
            <div class="form-group">
              <label for="hp">No. WhatsApp</label>
              <input type="tel" id="hp" name="hp" placeholder="08xx-xxxx-xxxx" required>
            </div>
          </div>

          <div class="form-group">
            <label for="alamat">Alamat Pengiriman</label>
            <textarea id="alamat" name="alamat" rows="3" placeholder="Jalan, Nomor Rumah, Patokan..." required></textarea>
          </div>

          <div class="divider"></div>

          <div class="form-header">
            <h3>Menu Selection</h3>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="menu">Pilih Menu</label>
              <div class="select-wrapper">
                <select id="menu" name="menu" required>
                  <option value="" disabled selected>-- Pilih Menu Favorit --</option>
                  <option value="Salmon Roll">Salmon Roll - Rp 25.000</option>
                  <option value="Tuna Roll">Tuna Roll - Rp 22.000</option>
                  <option value="Tamago Roll">Tamago Roll - Rp 18.000</option>
                  <option value="Shrimp Tempura">Shrimp Tempura - Rp 27.000</option>
                  <option value="Crab Roll">Crab Roll - Rp 20.000</option>
                  <option value="Avocado Roll">Avocado Roll - Rp 19.000</option>
                </select>
                <i class='bx bx-chevron-down arrow-icon'></i>
              </div>
            </div>
            <div class="form-group">
              <label for="jumlah">Jumlah Porsi</label>
              <input type="number" id="jumlah" name="jumlah" min="1" max="50" value="1" required>
            </div>
          </div>

          <div class="form-group">
            <label>Metode Pembayaran</label>
            <div class="payment-options">
              
              <label class="payment-card">
                <input type="radio" name="pembayaran" value="cod" checked>
                <div class="card-content">
                  <i class='bx bxs-truck'></i>
                  <span>COD</span>
                  <small>Bayar di Tempat</small>
                </div>
              </label>

              <label class="payment-card">
                <input type="radio" name="pembayaran" value="transfer">
                <div class="card-content">
                  <i class='bx bxs-bank'></i>
                  <span>Transfer</span>
                  <small>Bank / E-Wallet</small>
                </div>
              </label>

            </div>
          </div>

          <div class="form-group">
            <label for="catatan">Catatan Tambahan (Opsional)</label>
            <textarea id="catatan" name="catatan" rows="2" placeholder="Contoh: Jangan pakai wasabi, saus dipisah..."></textarea>
          </div>

          <div class="form-actions">
            <button type="submit" class="btn-submit">
              Konfirmasi Pesanan <i class='bx bx-right-arrow-alt'></i>
            </button>
          </div>

        </form>
      </div>
    </section>

    <aside class="visual-section">
      <div class="visual-card">
        <div class="visual-content">
          <h4>Why Choose Us?</h4>
          <ul class="benefit-list">
            <li>
              <i class='bx bxs-check-shield'></i>
              <span>100% Bahan Segar & Halal</span>
            </li>
            <li>
              <i class='bx bxs-time-five'></i>
              <span>Pengiriman Cepat < 30 Menit</span>
            </li>
            <li>
              <i class='bx bxs-star'></i>
              <span>Rating 4.9/5 dari Pelanggan</span>
            </li>
          </ul>
          <div class="mini-gallery">
            <img src="assets/images/sushi1.jpg" alt="Preview 1">
            <img src="assets/images/salmonaburi.jpg" alt="Preview 2">
          </div>
        </div>
        <img src="assets/images/chef.jpeg" alt="Background" class="bg-visual">
      </div>
    </aside>

  </div>
</main>

<?php include 'includes/footer.php'; ?>