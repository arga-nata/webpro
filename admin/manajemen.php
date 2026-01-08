<!DOCTYPE html>
<html lang="id">

<head>
  <title>Manajemen Menu - Street Sushi</title>
  <link rel="stylesheet" href="../src/global.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Dela+Gothic+One&family=Poppins:wght@400;600;800&display=swap"
    rel="stylesheet">
</head>

<body>

  <div class="header">
    <br>
    <font size="7" color="white">STREET SUSHI</font>
    <br>
    <font size="4" color="white">Blog Analisis & Studi Kasus UMKM Kuliner</font>
    <br><br>
    <div class="navbar">
      <ul>
        <li><a href="dashboard.html">Dashboard</a></li>
        <li><a href="manajemen.html"><b>Manajemen</b></a></li>
        <li><a href="laporan.html">Laporan</a></li>
      </ul>
    </div>
    <br><br>
  </div>

  <div class="welcome-container">
    <p>Welcome, <strong>User Ganteng/Cantik</strong></p>
    <a href="login.html">
    <button class="logout-btn">Logout</button>
    </a>
  </div>

  <div class="ss-man-container">
    
    <div class="ss-man-header">
        <h2 style="font-family: 'Dela Gothic One'; color: #a01a35; margin: 0;">DAFTAR MENU</h2>
        <a href="crud.html" class="ss-btn-add">+ Tambah Menu Baru</a>
    </div>

    <div class="ss-menu-grid">
        
        <div class="ss-menu-card">
            <span class="ss-category">SUSHI ROLL</span>
            <h3>Salmon Mentai Roll</h3>
            <p style="margin: 0 0 15px 0;">Nasi sushi dengan topping salmon segar dan saus mentai yang dibakar sempurna.</p>
            <span class="ss-price">Rp 45.000</span>
            <div class="ss-card-actions">
                <a href="#" class="ss-btn-edit">Edit</a>
                <a href="#" class="ss-btn-delete">Hapus</a>
            </div>
        </div>

        <div class="ss-menu-card">
            <span class="ss-category">MAKI</span>
            <h3>Tuna Spicy Maki</h3>
            <p style="margin: 0 0 15px 0;">Gulungan nori isi tuna pedas dengan timun jepang yang renyah.</p>
            <span class="ss-price">Rp 35.000</span>
            <div class="ss-card-actions">
                <a href="#" class="ss-btn-edit">Edit</a>
                <a href="#" class="ss-btn-delete">Hapus</a>
            </div>
        </div>

        <div class="ss-menu-card">
            <span class="ss-category">NIGIRI</span>
            <h3>Ebi Nigiri</h3>
            <p style="margin: 0 0 15px 0;">Udang rebus manis di atas kepalan nasi sushi premium.</p>
            <span class="ss-price">Rp 28.000</span>
            <div class="ss-card-actions">
                <a href="#" class="ss-btn-edit">Edit</a>
                <a href="#" class="ss-btn-delete">Hapus</a>
            </div>
        </div>

        <div class="ss-menu-card">
            <span class="ss-category">NIGIRI</span>
            <h3>Ebi Nigiri</h3>
            <p style="margin: 0 0 15px 0;">Udang rebus manis di atas kepalan nasi sushi premium.</p>
            <span class="ss-price">Rp 28.000</span>
            <div class="ss-card-actions">
                <a href="#" class="ss-btn-edit">Edit</a>
                <a href="#" class="ss-btn-delete">Hapus</a>
            </div>
        </div>
    </div>
  </div>

  <hr width="800" style="margin-top: 50px;">
  <p align="center">
    &copy; 2025 Blog Street Sushi Indonesia. Dibuat dengan penuh semangat oleh Kelompok 3.
  </p>
  <br>

</body>
</html>