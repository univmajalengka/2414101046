<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bromo_tours";

$conn = new mysqli($servername, $username, $password);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$conn->select_db($dbname);

if (isset($_GET['admin']) && $_GET['admin'] === '1') {
    $_SESSION['is_admin'] = true;
} elseif (isset($_GET['admin_logout'])) {
    unset($_SESSION['is_admin']);
}

$isAdmin = $_SESSION['is_admin'] ?? false;
$filterPhone = null;
if (isset($_GET['phone']) && preg_match('/^[0-9]{10,13}$/', $_GET['phone'])) {
    $filterPhone = $_GET['phone'];
}

$pesananList = array();
if ($filterPhone) {
    $stmt = $conn->prepare("SELECT * FROM pesanan WHERE nomor_hp = ? ORDER BY created_at DESC");
    if ($stmt) {
        $stmt->bind_param('s', $filterPhone);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $pesananList[] = $row;
            }
        }
        $stmt->close();
    }
} else {
    $query = "SELECT * FROM pesanan ORDER BY created_at DESC";
    $result = $conn->query($query);
    if (!$result) {
        die("Query Error: " . $conn->error);
    }
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $pesananList[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pesanan - Bromo Tours</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .admin-container {
            max-width: 1200px;
            margin: 100px auto;
            padding: 2rem;
        }

        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .admin-header h2 {
            color: var(--primary-color);
            font-size: 2rem;
        }

        .admin-buttons {
            display: flex;
            gap: 1rem;
        }

        .btn {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background: #a01733;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: #666;
            color: white;
        }

        .btn-secondary:hover {
            background: #555;
        }

        .message {
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1.5rem;
            text-align: center;
            animation: slideInDown 0.5s ease;
        }

        .message.success {
            background: #4caf50;
            color: white;
        }

        .message.error {
            background: #f44336;
            color: white;
        }

        .table-container {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: linear-gradient(135deg, var(--primary-color) 0%, #ff6b6b 100%);
            color: white;
        }

        th {
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid var(--primary-color);
        }

        td {
            padding: 1rem;
            border-bottom: 1px solid #eee;
        }

        tbody tr:hover {
            background: var(--light-color);
            transition: background 0.3s ease;
        }

        .table-actions {
            display: flex;
            gap: 0.5rem;
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
            border-radius: 3px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
        }

        .btn-edit {
            background: #2196F3;
            color: white;
        }

        .btn-edit:hover {
            background: #1976D2;
            transform: translateY(-2px);
        }

        .btn-delete {
            background: #f44336;
            color: white;
        }

        .btn-delete:hover {
            background: #da190b;
            transform: translateY(-2px);
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #999;
        }

        .empty-state p {
            font-size: 1.1rem;
            margin-bottom: 1rem;
        }

        .price-format {
            font-weight: 600;
            color: var(--primary-color);
        }

        .status-badge {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
        }

        .status-confirmed {
            background: #4caf50;
            color: white;
        }

        .status-pending {
            background: #ff9800;
            color: white;
        }

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

        .btn-confirm:hover {
            background: #da190b;
        }

        .btn-cancel:hover {
            background: #777;
        }

        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            .admin-header {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }

            .admin-buttons {
                width: 100%;
            }

            .admin-buttons a {
                flex: 1;
                text-align: center;
            }

            .table-wrapper {
                font-size: 0.9rem;
            }

            th, td {
                padding: 0.7rem;
            }

            .btn-sm {
                padding: 0.4rem 0.8rem;
                font-size: 0.75rem;
            }

            .table-actions {
                flex-direction: column;
                gap: 0.3rem;
            }
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .stat-card h3 {
            color: #999;
            font-size: 0.9rem;
            text-transform: uppercase;
            margin-bottom: 0.5rem;
        }

        .stat-card .stat-number {
            font-size: 2rem;
            color: var(--primary-color);
            font-weight: bold;
        }

        .search-form {
            margin-bottom: 1.5rem;
            display: flex;
            gap: 1rem;
        }

        .search-form input {
            flex: 1;
            padding: 0.8rem;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }

        .search-form button {
            padding: 0.8rem 1.5rem;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .search-form button:hover {
            background: #a01733;
        }
    </style>
</head>
<body>
    <header class="navbar">
        <div class="navbar-container">
            <div class="logo">
                <h1>🏔️ Bromo Tours</h1>
            </div>
            <nav class="nav-menu">
                <div class="nav-highlight"></div>
                <a href="index.php" class="nav-link">← Kembali ke Beranda</a>
            </nav>
        </div>
    </header>

    <main>
        <div class="admin-container">
            <div class="admin-header">
                <h2>📋 Daftar Pesanan</h2>
                    <div class="admin-buttons">
                        <a href="pemesanan.php" class="btn btn-primary">+ Tambah Pesanan Baru</a>
                        <a href="index.php" class="btn btn-secondary">← Kembali</a>
                        <?php if ($isAdmin): ?>
                            <a href="daftar_pesanan.php?admin_logout=1" class="btn btn-secondary">Logout Admin</a>
                        <?php else: ?>
                            <a href="daftar_pesanan.php?admin=1" class="btn btn-secondary">Login Admin</a>
                        <?php endif; ?>
                    </div>
            </div>

            <?php
            if (isset($_GET['delete']) && $_GET['delete'] === 'success') {
                echo '<div class="message success">✓ Pesanan berhasil dihapus!</div>';
            }
            if (isset($_GET['delete']) && $_GET['delete'] === 'error') {
                echo '<div class="message error">✗ Gagal menghapus pesanan!</div>';
            }
            if (isset($_GET['created'])) {
                echo '<div class="message success">✓ Pesanan berhasil dibuat!</div>';
            }
            if (isset($_GET['updated'])) {
                echo '<div class="message success">✓ Pesanan berhasil diperbarui!</div>';
            }
            ?>

            <?php if (isset($isAdmin) && $isAdmin): ?>
                <div class="stats">
                    <div class="stat-card">
                        <h3>Total Pesanan</h3>
                        <div class="stat-number"><?php echo count($pesananList); ?></div>
                    </div>
                    <div class="stat-card">
                        <h3>Total Pendapatan</h3>
                        <div class="stat-number">
                            Rp <?php 
                                $total = 0;
                                foreach ($pesananList as $pesanan) {
                                    $total += $pesanan['jumlah_tagihan'];
                                }
                                echo number_format($total, 0, ',', '.');
                            ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <form method="GET" class="search-form" style="margin-top:1rem;">
                <input type="text" name="phone" placeholder="Masukkan No HP untuk verifikasi" value="<?php echo htmlspecialchars($filterPhone ?? ''); ?>">
                <button type="submit">Lihat Pesanan Saya</button>
                <?php if (!isset($isAdmin) || !$isAdmin): ?>
                    <a href="daftar_pesanan.php?admin=1" class="btn btn-secondary" style="margin-left:0.5rem;">Login Admin</a>
                <?php else: ?>
                    <a href="daftar_pesanan.php" class="btn btn-secondary" style="margin-left:0.5rem;">Lihat Semua</a>
                <?php endif; ?>
            </form>

            <div class="table-container">
                <?php if (count($pesananList) > 0): ?>
                    <div class="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Pemesan</th>
                                    <th>Nomor HP</th>
                                    <th>Tanggal Pesan</th>
                                    <th>Durasi</th>
                                    <th>Jumlah Peserta</th>
                                    <th>Harga Paket</th>
                                    <th>Total Tagihan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = 1;
                                foreach ($pesananList as $pesanan): 
                                ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td><?php echo htmlspecialchars($pesanan['nama_pemesan']); ?></td>
                                        <td><?php echo htmlspecialchars($pesanan['nomor_hp']); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($pesanan['tanggal_pesan'])); ?></td>
                                        <td><?php echo htmlspecialchars($pesanan['waktu_pelaksanaan']); ?></td>
                                        <td><?php echo $pesanan['jumlah_peserta']; ?> orang</td>
                                        <td class="price-format">Rp <?php echo number_format($pesanan['harga_paket'], 0, ',', '.'); ?></td>
                                        <td class="price-format">Rp <?php echo number_format($pesanan['jumlah_tagihan'], 0, ',', '.'); ?></td>
                                        <td>
                                            <div class="table-actions">
                                                <?php if (isset($isAdmin) && $isAdmin): ?>
                                                    <a href="pemesanan.php?edit=<?php echo $pesanan['id']; ?>" class="btn-sm btn-edit">Edit</a>
                                                    <button class="btn-sm btn-delete" onclick="showDeleteModal(<?php echo $pesanan['id']; ?>, '<?php echo htmlspecialchars($pesanan['nama_pemesan']); ?>')">Hapus</button>
                                                <?php else: ?>
                                                    <span class="status-badge status-pending">Terdaftar</span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <p>📭 Tidak ada pesanan saat ini</p>
                        <a href="pemesanan.php" class="btn btn-primary">+ Buat Pesanan Baru</a>
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

        setTimeout(function() {
            const messages = document.querySelectorAll('.message');
            messages.forEach(msg => {
                msg.style.opacity = '0';
                msg.style.transition = 'opacity 0.5s ease';
                setTimeout(() => msg.remove(), 500);
            });
        }, 3000);
    </script>
</body>
</html>

<?php
$conn->close();
?>
