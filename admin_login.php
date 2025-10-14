<?php
include 'db.php';
session_start();
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    $result = mysqli_query($conn, "SELECT * FROM admin WHERE username='$username'");
    if ($row = mysqli_fetch_assoc($result)) {
        if ($row['password'] === md5($password)) {
            $_SESSION['admin_username'] = $row['username'];
            $_SESSION['admin_id'] = $row['id'];
            header('Location: admin.php');
            exit;
        } else {
            $msg = '<span style=\'color:red;\'>Password salah!</span>';
        }
    } else {
        $msg = '<span style=\'color:red;\'>Username tidak ditemukan!</span>';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <title>Login Admin - OM's Barbershop</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <div style="min-height:100vh;display:flex;align-items:center;justify-content:center;background:#f7f7f7;">
        <div style="background:#fff;padding:2rem 2.5rem;border-radius:12px;box-shadow:0 4px 24px rgba(0,0,0,0.08);text-align:center;max-width:350px;width:100%;">
            <h2 style="font-family:'Inter',sans-serif;color:#222;font-weight:600;margin-bottom:1.5rem;">Login Admin</h2>
            <?php if ($msg) echo $msg; ?>
            <form method="post" action="admin_login.php" style="display:flex;flex-direction:column;gap:1rem;">
                <input type="text" name="username" placeholder="Username" required style="padding:0.75rem;border-radius:6px;border:1px solid #e0e0e0;background:#fafafa;">
                <input type="password" name="password" placeholder="Password" required style="padding:0.75rem;border-radius:6px;border:1px solid #e0e0e0;background:#fafafa;">
                <button type="submit" style="background:#222;color:#fff;padding:0.75rem;border:none;border-radius:6px;font-weight:600;cursor:pointer;">Login</button>
            </form>
        </div>
    </div>
</body>
</html>
