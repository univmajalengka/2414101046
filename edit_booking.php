<?php
include 'db.php';
if (!isset($_GET['id'])) die('ID booking tidak ditemukan');
$id = intval($_GET['id']);
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $nohp = mysqli_real_escape_string($conn, $_POST['nohp']);
    $tanggal = $_POST['tanggal'];
    $jam = $_POST['jam'];
    $layanan = mysqli_real_escape_string($conn, $_POST['layanan']);
    $update = mysqli_query($conn, "UPDATE booking SET nama='$nama', nohp='$nohp', tanggal='$tanggal', jam='$jam', layanan='$layanan' WHERE id=$id");
    if ($update) {
        $msg = '<span style=\'color:green;\'>Data booking berhasil diupdate!</span>';
    } else {
        $msg = '<span style=\'color:red;\'>Gagal update data!</span>';
    }
}
$data = mysqli_query($conn, "SELECT * FROM booking WHERE id=$id");
$row = mysqli_fetch_assoc($data);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <title>Edit Booking</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <div style="max-width:400px;margin:48px auto;background:#fff;padding:32px;border-radius:12px;box-shadow:0 4px 24px rgba(0,0,0,0.08);">
        <h2>Edit Booking</h2>
        <?php if ($msg) echo $msg; ?>
        <form method="post" style="display:flex;flex-direction:column;gap:1rem;">
            <input type="text" name="nama" value="<?php echo htmlspecialchars($row['nama']); ?>" required>
            <input type="text" name="nohp" value="<?php echo htmlspecialchars($row['nohp']); ?>" required>
            <input type="date" name="tanggal" value="<?php echo htmlspecialchars($row['tanggal']); ?>" required>
            <input type="time" name="jam" value="<?php echo htmlspecialchars($row['jam']); ?>" required>
            <select name="layanan" required>
                <option value="Haircut" <?php if($row['layanan']=='Haircut')echo'selected';?>>Haircut</option>
                <option value="Shaving & Beard Trim" <?php if($row['layanan']=='Shaving & Beard Trim')echo'selected';?>>Shaving & Beard Trim</option>
                <option value="Styling" <?php if($row['layanan']=='Styling')echo'selected';?>>Styling</option>
            </select>
            <button type="submit" style="background:#222;color:#fff;padding:0.75rem;border:none;border-radius:6px;font-weight:600;cursor:pointer;">Simpan Perubahan</button>
        </form>
    </div>
</body>
</html>
