<?php
// Hubungkan ke database
include 'config/db.php'; 

$pesan = "";

if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // 1. Enkripsi Password (PENTING BIAR AMAN)
    // Kita pakai password_hash bawaan PHP.
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // 2. Masukkan ke Database
    $query = "INSERT INTO admins (username, password) VALUES ('$username', '$hashed_password')";
    
    if (mysqli_query($conn, $query)) {
        $pesan = "✅ Akun berhasil dibuat! Silakan <a href='login.php'>Login di sini</a>";
    } else {
        $pesan = "❌ Gagal: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Buat Akun Admin (Sementara)</title>
    <style>
        body { font-family: monospace; padding: 50px; background: #f0f0f0; }
        form { background: white; padding: 20px; border: 1px solid black; width: 300px; }
        input { width: 90%; padding: 5px; margin-bottom: 10px; display: block; }
        button { padding: 10px; cursor: pointer; background: black; color: white; border: none; }
    </style>
</head>
<body>

    <h2>🛠️ Register Admin (Mode Darurat)</h2>
    
    <?php if ($pesan) echo "<p style='font-weight:bold;'>$pesan</p>"; ?>

    <form method="POST" action="">
        <label>Username Baru:</label>
        <input type="text" name="username" required>

        <label>Password Baru:</label>
        <input type="text" name="password" required>

        <button type="submit" name="submit">SIMPAN KE DATABASE</button>
    </form>

</body>
</html>