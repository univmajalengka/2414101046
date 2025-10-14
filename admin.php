<?php
session_start();
include 'db.php';
if (!isset($_SESSION['admin_username'])) {
    header('Location: admin_login.php');
    exit;
}
if (isset($_GET['delete_booking'])) {
    $id = intval($_GET['delete_booking']);
    mysqli_query($conn, "DELETE FROM booking WHERE id=$id");
}
if (isset($_GET['delete_member'])) {
    $id = intval($_GET['delete_member']);
    mysqli_query($conn, "DELETE FROM member WHERE id=$id");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="style.css" />
    <style>
        body { background:#f7f7f7; }
        .container { max-width:900px; margin:48px auto; background:#fff; border-radius:12px; box-shadow:0 4px 24px rgba(0,0,0,0.08); padding:32px; }
        h2 { margin-top:0; }
        table { width:100%; border-collapse:collapse; margin-bottom:32px; }
        th, td { border:1px solid #eee; padding:8px 12px; text-align:left; }
        th { background:#fafafa; }
        a.btn { padding:6px 16px; border-radius:6px; background:#222; color:#fff; text-decoration:none; font-size:14px; margin-right:4px; }
        a.btn-danger { background:#e74c3c; }
        a.btn-edit { background:#2980b9; }
    </style>
</head>
<body>
    <header style="background:rgba(255,255,255,0.85);backdrop-filter:blur(6px);border-bottom:1px solid #eee;padding:0.5rem 0;margin-bottom:2rem;box-shadow:0 2px 8px rgba(0,0,0,0.04);">
        <nav style="display:flex;justify-content:center;align-items:center;gap:1.2rem;text-align:center;">
            <a href="index.php#hero">Home</a>
            <a href="admin.php">Dashboard Admin</a>
            <a href="logout.php" style="color:#e74c3c;font-weight:600;">Logout</a>
        </nav>
    </header>
    <div class="container" style="background:linear-gradient(135deg,#f7f7f7 60%,#e0e0e0 100%);padding:2.5rem 2rem;margin-top:48px;">
        <h1 style="font-family:'Inter',sans-serif;color:#222;font-weight:700;margin-bottom:2rem;text-align:center;">Dashboard Admin</h1>
        <div style="display:flex;justify-content:center;margin-bottom:2rem;gap:1rem;">
            <?php
            $menu = isset($_GET['menu']) ? $_GET['menu'] : 'booking';
            ?>
            <a href="?menu=booking" style="padding:0.7rem 2rem;border-radius:8px;
                background:<?php echo ($menu=='booking')?'#222':'#eee'; ?>;
                color:<?php echo ($menu=='booking')?'#fff':'#222'; ?>;
                font-weight:600;text-decoration:none;">Data Booking</a>
            <a href="?menu=member" style="padding:0.7rem 2rem;border-radius:8px;
                background:<?php echo ($menu=='member')?'#222':'#eee'; ?>;
                color:<?php echo ($menu=='member')?'#fff':'#222'; ?>;
                font-weight:600;text-decoration:none;">Data Member</a>
        </div>
        <?php if (!isset($_GET['menu']) || $_GET['menu']=='booking') { ?>
        <div style="background:#fff;padding:2rem 1.5rem;border-radius:12px;box-shadow:0 2px 12px rgba(0,0,0,0.04);">
            <h2 style="margin-bottom:1rem;">Data Booking</h2>
            <?php
            if (isset($_GET['verify_booking'])) {
                $id = intval($_GET['verify_booking']);
                mysqli_query($conn, "UPDATE booking SET status='Diterima' WHERE id=$id");
                echo '<div style="color:green;margin-bottom:1rem;">Booking telah diverifikasi!</div>';
            }
            ?>
            <table style="width:100%;border-collapse:collapse;box-shadow:0 2px 8px rgba(0,0,0,0.04);text-align:center;">
                <tr style="background:#fafafa;"><th>ID</th><th>Nama</th><th>No HP</th><th>Tanggal</th><th>Jam</th><th>Layanan</th><th>Status</th><th>Aksi</th></tr>
                <?php
                $result = mysqli_query($conn, "SELECT * FROM booking ORDER BY id DESC");
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>"
                        ."<td>{$row['id']}</td>"
                        ."<td>{$row['nama']}</td>"
                        ."<td>{$row['nohp']}</td>"
                        ."<td>{$row['tanggal']}</td>"
                        ."<td>{$row['jam']}</td>"
                        ."<td>{$row['layanan']}</td>"
                        ."<td>".($row['status']??'Menunggu')."</td>"
                        ."<td>"
                        .($row['status']=='Diterima'
                            ? '<span style=\'color:green;font-weight:600;\'>Diterima</span>'
                            : '<a class=\'btn btn-edit\' href=\'?verify_booking=' . $row['id'] . '\'>Verifikasi</a>'
                        )
                        ." <a class='btn btn-danger' href='?delete_booking={$row['id']}' onclick='return confirm(\'Hapus data ini?\')'>Hapus</a>"
                        ."</td>"
                        ."</tr>";
                }
                ?>
            </table>
        </div>
        <?php } else { ?>
        <div style="background:#fff;padding:2rem 1.5rem;border-radius:12px;box-shadow:0 2px 12px rgba(0,0,0,0.04);">
            <h2 style="margin-bottom:1rem;">Data Member</h2>
            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
                $username = mysqli_real_escape_string($conn, $_POST['username']);
                $password = md5($_POST['password']);
                $nama = mysqli_real_escape_string($conn, $_POST['nama']);
                $insert = mysqli_query($conn, "INSERT INTO member (username, password, nama) VALUES ('$username', '$password', '$nama')");
                if ($insert) {
                    echo '<div style="color:green;margin-bottom:1rem;">User berhasil ditambahkan!</div>';
                } else {
                    echo '<div style="color:red;margin-bottom:1rem;">Gagal menambah user!</div>';
                }
            }
            ?>
            <form method="post" style="margin-bottom:2rem;display:flex;gap:1rem;align-items:center;flex-wrap:wrap;">
                <input type="text" name="username" placeholder="Username" required style="padding:0.5rem 1rem;border-radius:6px;border:1px solid #e0e0e0;background:#fafafa;">
                <input type="password" name="password" placeholder="Password" required style="padding:0.5rem 1rem;border-radius:6px;border:1px solid #e0e0e0;background:#fafafa;">
                <input type="text" name="nama" placeholder="Nama Lengkap" required style="padding:0.5rem 1rem;border-radius:6px;border:1px solid #e0e0e0;background:#fafafa;">
                <button type="submit" name="add_user" style="background:#222;color:#fff;padding:0.5rem 1.5rem;border:none;border-radius:6px;font-weight:600;cursor:pointer;">Tambah User</button>
            </form>
            <table style="width:100%;border-collapse:collapse;box-shadow:0 2px 8px rgba(0,0,0,0.04);text-align:center;">
                <tr style="background:#fafafa;"><th>ID</th><th>Username</th><th>Nama Lengkap</th><th>Aksi</th></tr>
                <?php
                $result = mysqli_query($conn, "SELECT * FROM member ORDER BY id DESC");
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>"
                        ."<td>{$row['id']}</td>"
                        ."<td>{$row['username']}</td>"
                        ."<td>{$row['nama']}</td>"
                        ."<td><a class='btn btn-edit' href='edit_member.php?id={$row['id']}'>Edit</a> <a class='btn btn-danger' href='?delete_member={$row['id']}' onclick='return confirm(\'Hapus data ini?\')'>Hapus</a></td>"
                        ."</tr>";
                }
                ?>
            </table>
        </div>
        <?php } ?>
    </div>
</body>
</html>