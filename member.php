<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <title>Dashboard Member</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <header style="background:rgba(255,255,255,0.85);backdrop-filter:blur(6px);border-bottom:1px solid #eee;padding:0.5rem 0;margin-bottom:2rem;box-shadow:0 2px 8px rgba(0,0,0,0.04);">
        <nav style="display:flex;justify-content:center;align-items:center;gap:1.2rem;text-align:center;">
            <a href="index.php#hero">Home</a>
            <a href="member.php">Dashboard</a>
            <a href="logout.php" style="color:#e74c3c;font-weight:600;">Logout</a>
        </nav>
    </header>
    <main style="max-width:700px;margin:48px auto 0 auto;">
        <div style="background:#fff;padding:2rem 2.5rem;border-radius:16px;box-shadow:0 4px 24px rgba(0,0,0,0.08);margin-bottom:2rem;">
            <h2 style="font-family:'Inter',sans-serif;color:#222;font-weight:700;margin-bottom:1rem;">Halo, <?php echo htmlspecialchars($_SESSION['nama']); ?> 👋</h2>
            <p style="color:#555;margin-bottom:2rem;">Selamat datang di dashboard member OM's Barbershop.<br>Di sini kamu bisa mengatur profil dan melakukan booking janji temu.</p>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:2rem;justify-content:center;align-items:start;">
                <div style="min-width:220px;">
                    <h3 style="margin-bottom:1rem;">Edit Profil</h3>
                    <?php
                    include 'db.php';
                    $msg = '';
                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_profil'])) {
                        $nama = mysqli_real_escape_string($conn, $_POST['nama']);
                        $id = $_SESSION['id'];
                        $update = mysqli_query($conn, "UPDATE member SET nama='$nama' WHERE id=$id");
                        if ($update) {
                            $_SESSION['nama'] = $nama;
                            $msg = '<span style=\'color:green;\'>Profil berhasil diupdate!</span>';
                        } else {
                            $msg = '<span style=\'color:red;\'>Gagal update profil!</span>';
                        }
                    }
                    ?>
                    <?php if ($msg) echo $msg; ?>
                    <form method="post" style="margin-bottom:2rem;display:flex;flex-direction:column;gap:1rem;max-width:300px;">
                        <input type="text" name="nama" value="<?php echo htmlspecialchars($_SESSION['nama']); ?>" required style="padding:0.75rem;border-radius:6px;border:1px solid #e0e0e0;background:#fafafa;">
                        <button type="submit" name="edit_profil" style="background:#222;color:#fff;padding:0.75rem;border:none;border-radius:6px;font-weight:600;cursor:pointer;">Simpan Perubahan</button>
                    </form>
                </div>
                <div style="min-width:220px;">
                    <h3 style="margin-bottom:1rem;">Booking Janji Temu</h3>
                    <?php
                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking'])) {
                        $nama = mysqli_real_escape_string($conn, $_SESSION['username']);
                        $nohp = mysqli_real_escape_string($conn, $_POST['nohp']);
                        $tanggal = $_POST['tanggal'];
                        $jam = $_POST['jam'];
                        $layanan = mysqli_real_escape_string($conn, $_POST['layanan']);
                        $insert = mysqli_query($conn, "INSERT INTO booking (nama, nohp, tanggal, jam, layanan) VALUES ('$nama', '$nohp', '$tanggal', '$jam', '$layanan')");
                        if ($insert) {
                            echo '<span style=\'color:green;\'>Booking berhasil!</span>';
                        } else {
                            echo '<span style=\'color:red;\'>Gagal booking!</span>';
                        }
                    }
                    ?>
                    <form method="post" style="display:flex;flex-direction:column;gap:1rem;max-width:300px;margin:0 auto;">
                        <input type="text" name="nohp" placeholder="No. HP / WA" required style="padding:0.75rem;border-radius:6px;border:1px solid #e0e0e0;background:#fafafa;">
                        <input type="date" name="tanggal" required style="padding:0.75rem;border-radius:6px;border:1px solid #e0e0e0;background:#fafafa;">
                        <input type="time" name="jam" required style="padding:0.75rem;border-radius:6px;border:1px solid #e0e0e0;background:#fafafa;">
                        <select name="layanan" required style="padding:0.75rem;border-radius:6px;border:1px solid #e0e0e0;background:#fafafa;">
                            <option value="">Pilih Layanan</option>
                            <option value="Haircut">Haircut</option>
                            <option value="Shaving & Beard Trim">Shaving & Beard Trim</option>
                            <option value="Styling">Styling</option>
                        </select>
                        <button type="submit" name="booking" style="background:#222;color:#fff;padding:0.75rem;border:none;border-radius:6px;font-weight:600;cursor:pointer;">Booking</button>
                    </form>
                </div>
            </div>
        </div>
        <div style="background:#fff;padding:1.5rem 2rem;border-radius:12px;box-shadow:0 2px 12px rgba(0,0,0,0.04);">
            <h3 style="margin-bottom:1rem;">Riwayat Booking Anda</h3>
            <table style="width:100%;border-collapse:collapse;text-align:center;">
                <tr style="background:#fafafa;">
                    <th style="padding:10px 0;width:33%;">Tanggal</th>
                    <th style="padding:10px 0;width:33%;">Jam</th>
                    <th style="padding:10px 0;width:34%;">Layanan</th>
                </tr>
                <?php
                $username = $_SESSION['username'];
                $result = mysqli_query($conn, "SELECT tanggal, jam, layanan FROM booking WHERE nama='$username' ORDER BY tanggal DESC, jam DESC");
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>"
                        ."<td style='padding:8px 0;border-bottom:1px solid #eee'>{$row['tanggal']}</td>"
                        ."<td style='padding:8px 0;border-bottom:1px solid #eee'>{$row['jam']}</td>"
                        ."<td style='padding:8px 0;border-bottom:1px solid #eee'>{$row['layanan']}</td>"
                        ."</tr>";
                }
                ?>
            </table>
        </div>
            <?php
            $username = $_SESSION['username'];
            $result = mysqli_query($conn, "SELECT tanggal, jam, layanan FROM booking WHERE nama='$username' ORDER BY tanggal DESC, jam DESC");
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>"
                    ."<td>{$row['tanggal']}</td>"
                    ."<td>{$row['jam']}</td>"
                    ."<td>{$row['layanan']}</td>"
                    ."</tr>";
            }
            ?>
        </table>
    </main>
</body>
</html>
