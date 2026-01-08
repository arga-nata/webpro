<?php 
  $pageTitle = "Galeri - Street Sushi"; 
  $currentPage = "galeri";             
  include 'includes/header.php'; 
?>

<link rel="stylesheet" href="assets/css/galeri.css">

<header class="gallery-hero">
  <div class="hero-overlay"></div>
  <div class="hero-content text-center">
    <span class="japanese-title">フォトギャラリー</span>
    <h1 class="glitch-text" data-text="Visual Taste">Visual Taste</h1>
    <p>Menjelajahi seni kuliner jalanan dalam bingkai futuristik.</p>
  </div>
</header>

<main class="container-gallery">

  <section class="gallery-section">
    <div class="section-title">
      <span class="sub-jp">メニュー</span>
      <h2>Signature Menu</h2>
      <div class="neon-line"></div>
    </div>

    <div class="gallery-grid">
      <div class="gallery-item">
        <div class="img-box">
          <img src="https://byfood.b-cdn.net/api/public/assets/8208/Salmon-nigiri-sushi?optimizer=image" alt="Nigiri">
          <div class="overlay">
            <h3>Nigiri Sushi</h3>
            <p>握り寿司</p>
          </div>
        </div>
      </div>
      
      <div class="gallery-item">
        <div class="img-box">
          <img src="https://byfood.b-cdn.net/api/public/assets/8222/Futomaki?optimizer=image" alt="Maki">
          <div class="overlay">
            <h3>Futomaki</h3>
            <p>太巻</p>
          </div>
        </div>
      </div>

      <div class="gallery-item">
        <div class="img-box">
          <img src="https://byfood.b-cdn.net/api/public/assets/8212/uramaki?optimizer=image" alt="Uramaki">
          <div class="overlay">
            <h3>Uramaki</h3>
            <p>裏巻</p>
          </div>
        </div>
      </div>

      <div class="gallery-item">
        <div class="img-box">
          <img src="https://byfood.b-cdn.net/api/public/assets/8445/Inarizushi?optimizer=image" alt="Inari">
          <div class="overlay">
            <h3>Inari Sushi</h3>
            <p>稲荷寿司</p>
          </div>
        </div>
      </div>
      
      <div class="gallery-item">
        <div class="img-box">
          <img src="https://byfood.b-cdn.net/api/public/assets/8452/Temarizushi?optimizer=image" alt="Temari">
          <div class="overlay">
            <h3>Temari Sushi</h3>
            <p>手まり寿司</p>
          </div>
        </div>
      </div>

      <div class="gallery-item">
        <div class="img-box">
          <img src="https://byfood.b-cdn.net/api/public/assets/8451/Gunkanmaki?optimizer=image" alt="Gunkan">
          <div class="overlay">
            <h3>Gunkan Maki</h3>
            <p>軍艦巻</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="gallery-section dark-mode-bg">
    <div class="section-title">
      <span class="sub-jp">雰囲気</span>
      <h2>Street Atmosphere</h2>
      <div class="neon-line"></div>
    </div>

    <div class="atmosphere-grid">
      <div class="atmo-item large">
        <img src="https://i.ytimg.com/vi/zMdb2A2mzlc/hq720.jpg" alt="Gerobak Street Sushi">
        <div class="caption">
          <span>The Origin</span>
          <h4>Gerobak Legendaris</h4>
        </div>
      </div>

      <div class="atmo-item">
        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQfJzn1td9Zm3ZmqsImbC0_YSW81Qxl9Gn20Q&s" alt="Suasana">
        <div class="caption">
          <span>Public Space</span>
          <h4>Suasana Hangat</h4>
        </div>
      </div>

      <div class="atmo-item">
        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS8ixLiy-UfEyluUhlGbvYGFPFSPviqI4VtLQ&s" alt="Detail">
        <div class="caption">
          <span>Details</span>
          <h4>Cita Rasa Lokal</h4>
        </div>
      </div>
    </div>
  </section>

</main>

<?php include 'includes/footer.php'; ?>