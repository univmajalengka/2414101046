<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bromo_tours";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_phone'])) {
    $phone = trim($_POST['login_phone']);
    if (preg_match('/^[0-9]{10,13}$/', $phone)) {
        $_SESSION['user_phone'] = $phone;
        header('Location: pesanan_saya.php');
        exit;
    } else {
        $message = 'Nomor HP tidak valid (10-13 digit).';
    }
}

if (isset($_GET['logout'])) {
    unset($_SESSION['user_phone']);
    header('Location: pesanan_saya.php');
    exit;
}

$userPhone = $_SESSION['user_phone'] ?? null;

$orders = [];
if ($userPhone) {
    $stmt = $conn->prepare("SELECT * FROM pesanan WHERE nomor_hp = ? ORDER BY created_at DESC");
    $stmt->bind_param('s', $userPhone);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $orders[] = $row;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Saya - Bromo Tours</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .container { max-width: 1000px; margin: 100px auto; padding: 2rem; }
        .login-box { background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        .orders-table { margin-top: 1rem; }
        .btn { padding: 0.6rem 1rem; border-radius: 6px; border: none; cursor: pointer; }
        .btn-primary { background: var(--primary-color); color: white; }
        .btn-danger { background: #f44336; color: white; }
        .modal {
            display: none;
            position: fixed;
            z-index: 1001;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
        }

        .modal.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            text-align: center;
            max-width: 400px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
        }

        .modal-content h3 {
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .modal-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 1.5rem;
        }

        .modal-buttons form {
            display: inline;
        }

        .btn-confirm {
            background: #f44336;
            color: white;
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }

        .btn-cancel {
            background: #999;
            color: white;
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }

        .btn-confirm:hover { background: #da190b; }
        .btn-cancel:hover { background: #777; }
    </style>
</head>
<body>
    <header class="navbar">
        <div class="navbar-container">
            <div class="logo"><h1>🏔️ Bromo Tours</h1></div>
            <nav class="nav-menu"><div class="nav-highlight"></div><a href="index.php" class="nav-link">← Kembali ke Beranda</a></nav>
        </div>
    </header>

    <main>
        <div class="container">
            <div class="login-box">
                <h2>Pesanan Saya</h2>
                <?php if ($message): ?>
                    <div class="message error"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>

                <?php if (isset($_GET['created'])): ?>
                    <div class="message success">✓ Pesanan berhasil dibuat.</div>
                <?php endif; ?>
                <?php if (isset($_GET['updated'])): ?>
                    <div class="message success">✓ Pesanan berhasil diperbarui.</div>
                <?php endif; ?>

                <?php if (!$userPhone): ?>
                    <form method="POST">
                        <label>Masukkan Nomor HP untuk melihat pesanan Anda:</label>
                        <input type="text" name="login_phone" placeholder="Contoh: 081234567890" style="width:100%; padding:0.8rem; margin-top:0.5rem; border:2px solid #ddd; border-radius:6px;" required>
                        <div style="margin-top:0.8rem;"><button class="btn btn-primary" type="submit">Login</button></div>
                    </form>
                <?php else: ?>
                    <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem;">
                        <p style="margin:0;">Menampilkan pesanan untuk nomor: <strong><?php echo htmlspecialchars($userPhone); ?></strong></p>
                        <p style="margin:0;"><a href="pesanan_saya.php?logout=1" class="btn" style="background:#999;color:white;border-radius:6px;padding:0.5rem 0.8rem;">Logout</a></p>
                    </div>

                    <div class="orders-table">
                        <?php if (count($orders) === 0): ?>
                            <div style="text-align: center; padding: 2rem;">
                                <p style="margin-bottom: 1.5rem;">Anda belum memiliki pesanan.</p>
                                <a href="pemesanan.php" class="btn btn-primary" style="display: inline-block; padding: 0.8rem 1.5rem; text-decoration: none;">Buat Pesanan Baru</a>
                            </div>
                        <?php else: ?>
                            <table style="width:100%; border-collapse:collapse;">
                                <thead>
                                    <tr style="background:var(--primary-color); color:white;">
                                        <th style="padding:0.8rem; text-align:left;">No</th>
                                        <th style="padding:0.8rem; text-align:left;">Nama</th>
                                        <th style="padding:0.8rem; text-align:left;">Tanggal</th>
                                        <th style="padding:0.8rem; text-align:left;">Durasi</th>
                                        <th style="padding:0.8rem; text-align:left;">Jumlah</th>
                                        <th style="padding:0.8rem; text-align:left;">Total</th>
                                        <th style="padding:0.8rem; text-align:left;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 1; foreach ($orders as $order): ?>
                                        <tr style="border-bottom:1px solid #eee;">
                                            <td style="padding:0.6rem;"><?php echo $i++; ?></td>
                                            <td style="padding:0.6rem;"><?php echo htmlspecialchars($order['nama_pemesan']); ?></td>
                                            <td style="padding:0.6rem;"><?php echo date('d/m/Y', strtotime($order['tanggal_pesan'])); ?></td>
                                            <td style="padding:0.6rem;"><?php echo htmlspecialchars($order['waktu_pelaksanaan']); ?></td>
                                            <td style="padding:0.6rem;"><?php echo $order['jumlah_peserta']; ?> orang</td>
                                            <td style="padding:0.6rem;">Rp <?php echo number_format($order['jumlah_tagihan'],0,',','.'); ?></td>
                                            <td style="padding:0.6rem;">
                                                <a class="btn-sm btn-edit" href="pemesanan.php?edit=<?php echo $order['id']; ?>">Ubah</a>
                                                 <button type="button" class="btn-sm btn-delete" style="margin-left:0.4rem;" onclick="showDeleteModal(<?php echo $order['id']; ?>, '<?php echo htmlspecialchars($order['nama_pemesan']); ?>')">Hapus</button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <h3>Konfirmasi Hapus</h3>
            <p>Apakah Anda yakin ingin menghapus pesanan dari <span id="modalName"></span>?</p>
            <p style="color: #f44336; font-weight: bold; font-size: 0.9rem; margin-top: 1rem;">⚠️ Tindakan ini tidak dapat dibatalkan!</p>
            <div class="modal-buttons">
                <form id="deleteForm" method="POST" action="proses_pesanan.php" style="display: inline;">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" id="deleteId" value="">
                    <button type="submit" class="btn-confirm">Hapus</button>
                </form>
                <button class="btn-cancel" onclick="closeDeleteModal()">Batal</button>
            </div>
        </div>
    </div>

    <script>
        function showDeleteModal(id, name) {
            document.getElementById('deleteId').value = id;
            document.getElementById('modalName').textContent = name;
            document.getElementById('deleteModal').classList.add('show');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.remove('show');
        }

        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });
    </script>
</body>
</html>

<?php $conn->close(); ?>
