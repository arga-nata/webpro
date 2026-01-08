<?php
  $pageTitle = "Chef's Selection - Street Sushi";
  $currentPage = "chef";
  include 'includes/header.php';
?>

<link rel="stylesheet" href="assets/css/chef.css">

<header class="chef-hero">

  <div class="hero-chef-image">
    <img src="assets/images/chef.jpeg" alt="Chef Cooking">
  </div>

  <div class="hero-content">
    <span class="sub-title">OMAKASE SELECTION</span>
    <h1>The Chef's <span class="text-serif">Table</span></h1>
    <p>Sebuah kurasi rasa terbaik, dipersembahkan langsung dari dapur kami untuk meja Anda.</p>
    <div class="chef-signature">
      <span>Master Chef, Street Sushi</span>
    </div>
  </div>
</header>

<main class="container-chef">

  <section class="chef-intro">
    <div class="quote-box">
      <i class='bx bxs-quote-alt-left quote-icon'></i>
      <p>"Sushi bukan sekadar makanan, tapi seni keseimbangan. Menu-menu di bawah ini adalah favorit pribadi saya—kombinasi tekstur, rasa, dan inovasi yang paling mewakili jiwa Street Sushi."</p>
    </div>
  </section>

  <section class="menu-grid">
    
    <div class="menu-card">
      <div class="card-image">
        <img src="assets/images/sushi1.jpg" alt="Salmon Mentai Roll">
        <div class="badge">Most Loved</div>
      </div>
      <div class="card-details">
        <h3>Salmon Mentai Roll</h3>
        <p class="desc">Lembutnya salmon panggang berpadu saus mentai khas yang creamy dan sedikit pedas.</p>
        <div class="card-footer">
          <span class="recommendation">⭐ Chef's Top Pick</span>
        </div>
      </div>
    </div>

    <div class="menu-card">
      <div class="card-image">
        <img src="assets/images/tamagosupreme.jpg" alt="Tamago Supreme">
      </div>
      <div class="card-details">
        <h3>Tamago Supreme</h3>
        <p class="desc">Telur omelet Jepang dengan sentuhan manis gurih. Lembut, ringan, dan menenangkan.</p>
        <div class="card-footer">
          <span class="recommendation">Comfort Food</span>
        </div>
      </div>
    </div>

    <div class="menu-card">
      <div class="card-image">
        <img src="assets/images/steetspecial.jpg" alt="Street Special Roll">
        <div class="badge">Signature</div>
      </div>
      <div class="card-details">
        <h3>Street Special Roll</h3>
        <p class="desc">Perpaduan unik seafood segar dan sayuran pilihan dengan saus spesial rahasia.</p>
        <div class="card-footer">
          <span class="recommendation">Rich Flavor</span>
        </div>
      </div>
    </div>

    <div class="menu-card">
      <div class="card-image">
        <img src="assets/images/spicytuna.jpg" alt="Spicy Tuna Bomb">
      </div>
      <div class="card-details">
        <h3>Spicy Tuna Bomb</h3>
        <p class="desc">Ledakan rasa tuna segar berbumbu pedas, disajikan dengan nori renyah.</p>
        <div class="card-footer">
          <span class="recommendation">🌶️ Spicy Kick</span>
        </div>
      </div>
    </div>

    <div class="menu-card">
      <div class="card-image">
        <img src="assets/images/ebitempuraroll.jpg" alt="Ebi Tempura Roll">
      </div>
      <div class="card-details">
        <h3>Ebi Tempura Roll</h3>
        <p class="desc">Udang tempura renyah bertemu saus mayo Jepang. Tekstur krispi yang bikin nagih.</p>
        <div class="card-footer">
          <span class="recommendation">Crunchy</span>
        </div>
      </div>
    </div>

    <div class="menu-card">
      <div class="card-image">
        <img src="assets/images/salmonaburi.jpg" alt="Salmon Aburi">
      </div>
      <div class="card-details">
        <h3>Salmon Aburi</h3>
        <p class="desc">Salmon segar yang dibakar teknik Aburi (Torch) untuk aroma smokey yang khas.</p>
        <div class="card-footer">
          <span class="recommendation">🔥 Smokey Aroma</span>
        </div>
      </div>
    </div>

  </section>

</main>

<?php include 'includes/footer.php'; ?>