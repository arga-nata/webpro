<?php 
  $pageTitle = "About Us - Street Sushi"; 
  $currentPage = "about";             
  include 'includes/header.php'; 
?>

<link rel="stylesheet" href="assets/css/about.css">

<header class="about-hero">
  <div class="hero-content text-center">
    <span class="japanese-tag">私たちのチーム</span>
    <h1>Behind the Scenes <span class="text-gradient">Street Sushi</span></h1>
    <p>Sinergi kreativitas, teknologi, dan cita rasa lokal.</p>
  </div>
</header>

<main class="container-about">

  <section class="team-section">
    <div class="section-header">
      <h2>The Architects 🚀</h2>
      <div class="divider-red"></div>
    </div>

    <div class="team-grid">
      
      <div class="team-card">
  <div class="img-wrapper">
    <img src="assets/images/Firman.jpg" alt="Firman">
  </div>
  <div class="team-info">
    <h3>Firlian Firman Syah</h3>
    <span class="role">Ketua & Lead Programmer</span>
    <p>Visioner yang merangkai logika kode menjadi pengalaman pengguna yang mulus.</p>
  </div>
</div>

      <div class="team-card">
        <div class="img-wrapper">
          <img src="assets/images/Abbad.jpeg" alt="Naufal">
        </div>
        <div class="team-info">
          <h3>M. Naufal Abbad</h3>
          <span class="role">Frontend Developer</span>
          <p>Menerjemahkan desain visual menjadi antarmuka yang responsif dan interaktif.</p>
        </div>
      </div>

      <div class="team-card">
        <div class="img-wrapper">
          <img src="assets/images/Dikaa.jpeg" alt="Dika">
        </div>
        <div class="team-info">
          <h3>M. Putra Apridika</h3>
          <span class="role">Documentation Specialist</span>
          <p>Menjaga setiap detail proses terdokumentasi dengan rapi dan terstruktur.</p>
        </div>
      </div>

      <div class="team-card">
        <div class="img-wrapper">
          <img src="https://ui-avatars.com/api/?name=Vahmi+Reidina&background=2d3436&color=fff" alt="Vahmi">
        </div>
        <div class="team-info">
          <h3>Vahmi Reidina</h3>
          <span class="role">Quality Control</span>
          <p>Memastikan setiap konten dan data yang tersaji valid dan bebas kesalahan.</p>
        </div>
      </div>

      <div class="team-card">
        <div class="img-wrapper">
          <img src="assets/images/Rehan.jpeg" alt="Rechan">
        </div>
        <div class="team-info">
          <h3>Rechan Rizky</h3>
          <span class="role">Content Editor</span>
          <p>Meramu kata-kata agar pesan tersampaikan dengan lugas dan profesional.</p>
        </div>
      </div>

    </div>
  </section>

  <section class="story-section">
    <div class="story-content">
      <h2>Mengapa Kami Memulai?</h2>
      <p>
        Proyek <strong>Street Sushi</strong> lahir dari sebuah keresahan sederhana: <em>Bagaimana cara membawa teori akademik yang kaku menjadi solusi bisnis yang nyata?</em> 
      </p>
      <p>
        Kami melihat UMKM kuliner di Indonesia memiliki potensi luar biasa, namun seringkali tertinggal dalam adaptasi teknologi. Sebagai mahasiswa, kami tidak ingin hanya menjadi penonton. Kami menciptakan platform ini sebagai jembatan—menggabungkan cita rasa lokal, manajemen modern, dan teknologi web untuk membuktikan bahwa bisnis kaki lima pun bisa memiliki kelas bintang lima. Ini bukan sekadar tugas kuliah, ini adalah eksperimen kami dalam mendigitalisasi rasa.
      </p>
    </div>
  </section>

  <section class="quotes-section">
    <div class="section-header">
      <h2>Dialog Kepercayaan Diri ⚡</h2>
    </div>

    <div class="chat-container">
      <div class="chat-bubble left">
        <p>"Kadang rasanya kode ini terlalu rumit, apa kita bisa menyelesaikannya?"</p>
        <span class="chat-name">- Keraguan Diri</span>
      </div>

      <div class="chat-bubble right">
        <p>"Tenang. Error itu tanda progres. Setiap baris kode yang salah mengajarkan kita cara yang benar. <strong>Percayalah pada prosesnya.</strong>"</p>
        <span class="chat-name">- Keyakinan</span>
      </div>

      <div class="chat-bubble left">
        <p>"Tapi, bagaimana jika hasilnya tidak sempurna? Bagaimana jika orang tidak suka?"</p>
        <span class="chat-name">- Ketakutan</span>
      </div>

      <div class="chat-bubble right">
        <p>"Kesempurnaan itu ilusi. Keberanian untuk memulai adalah kuncinya. Karya yang dibuat dengan hati akan selalu menemukan penikmatnya. <strong>Maju terus!</strong>"</p>
        <span class="chat-name">- Semangat Tim</span>
      </div>
    </div>
  </section>

</main>

<?php include 'includes/footer.php'; ?>