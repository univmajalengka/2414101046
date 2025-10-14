<?php
include 'db.php';
if (!isset($_GET['id'])) die('ID member tidak ditemukan');
$id = intval($_GET['id']);
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $update = mysqli_query($conn, "UPDATE member SET username='$username', nama='$nama' WHERE id=$id");
    if ($update) {
        header('Location: admin.php?menu=member');
        exit;
    } else {
        $msg = '<span style=\'color:red;\'>Gagal update data!</span>';
    }
}
$data = mysqli_query($conn, "SELECT * FROM member WHERE id=$id");
$row = mysqli_fetch_assoc($data);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <title>Edit Member</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <div style="max-width:400px;margin:48px auto;background:#fff;padding:32px;border-radius:12px;box-shadow:0 4px 24px rgba(0,0,0,0.08);">
        <h2>Edit Member</h2>
        <?php if ($msg) echo $msg; ?>
        <form method="post" style="display:flex;flex-direction:column;gap:1rem;">
            <input type="text" name="username" value="<?php echo htmlspecialchars($row['username']); ?>" required>
            <input type="text" name="nama" value="<?php echo htmlspecialchars($row['nama']); ?>" required>
            <button type="submit" style="background:#222;color:#fff;padding:0.75rem;border:none;border-radius:6px;font-weight:600;cursor:pointer;">Simpan Perubahan</button>
        </form>
    </div>
</body>
</html>
