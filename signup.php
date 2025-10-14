<?php
include 'db.php';
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = mysqli_real_escape_string($conn, $_POST['username']);
  $password = md5($_POST['password']);
  $nama = mysqli_real_escape_string($conn, $_POST['nama']);
  $cek = mysqli_query($conn, "SELECT id FROM member WHERE username='$username'");
  if (mysqli_num_rows($cek) > 0) {
    $msg = '<span style="color:red;">Username sudah digunakan!</span>';
  } else {
    $insert = mysqli_query($conn, "INSERT INTO member (username, password, nama) VALUES ('$username', '$password', '$nama')");
    if ($insert) {
      $msg = '<span style="color:green;">Pendaftaran berhasil! Silakan login.</span>';
    } else {
      $msg = '<span style="color:red;">Gagal mendaftar!</span>';
    }
  }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Daftar Member - OM's Barbershop</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <div style="min-height:100vh;display:flex;align-items:center;justify-content:center;background:#f7f7f7;">
    <div style="background:#fff;padding:2rem 2.5rem;border-radius:12px;box-shadow:0 4px 24px rgba(0,0,0,0.08);text-align:center;max-width:350px;width:100%;">
      <h2 style="font-family:'Inter',sans-serif;color:#222;font-weight:600;margin-bottom:1.5rem;">Daftar Member Baru</h2>
      <?php if ($msg) echo $msg; ?>
      <form method="post" action="signup.php" style="display:flex;flex-direction:column;gap:1rem;">
        <input type="text" name="username" placeholder="Username" required style="padding:0.75rem;border-radius:6px;border:1px solid #e0e0e0;background:#fafafa;">
        <input type="password" name="password" placeholder="Password" required style="padding:0.75rem;border-radius:6px;border:1px solid #e0e0e0;background:#fafafa;">
        <input type="text" name="nama" placeholder="Nama Lengkap" required style="padding:0.75rem;border-radius:6px;border:1px solid #e0e0e0;background:#fafafa;">
        <button type="submit" style="background:#222;color:#fff;padding:0.75rem;border:none;border-radius:6px;font-weight:600;cursor:pointer;">Daftar</button>
      </form>
      <p style="margin-top:1rem;color:#555;">Sudah punya akun? <a href="login.php" style="color:#222;font-weight:500;text-decoration:underline;">Login di sini</a></p>
    </div>
  </div>
</body>
</html>
