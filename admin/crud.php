<!DOCTYPE html>
<html lang="id">

<head>
  <title>Tambah Menu - Street Sushi</title>
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
    <p>Halaman Manajemen > <strong>Tambah Menu Baru</strong></p>
    <a href="login.html">
    <button class="logout-btn">Logout</button>
    </a>
  </div>

  <div class="ss-crud-main">
    <div class="ss-form-card">
        
        <div class="ss-label-header">TAMBAH ITEM MENU</div>

        <form action="#" method="POST" class="ss-input-container">
            <input type="text" name="nama_menu" placeholder="Nama Menu Sushi" class="ss-input-pink" required>
            
            <input type="number" name="harga_menu" placeholder="Harga (Contoh: 45000)" class="ss-input-pink" required>
            
            <select name="kategori" class="ss-input-pink" required>
                <option value="" disabled selected>Pilih Kategori</option>
                <option value="roll">Sushi Roll</option>
                <option value="nigiri">Nigiri</option>
                <option value="maki">Maki</option>
            </select>

            <textarea name="deskripsi" placeholder="Deskripsi Singkat" class="ss-input-pink" rows="4"></textarea>

            <button type="submit" class="ss-btn-save">SIMPAN KE DATABASE</button>
        </form>

        <div class="ss-action-bottom">
            <button class="ss-btn-cancel" onclick="window.location.href='manajemen.html'">Batal</button>
            <button class="ss-btn-cancel" type="reset">Reset Form</button>
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