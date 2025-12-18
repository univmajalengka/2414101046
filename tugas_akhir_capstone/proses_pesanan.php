<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bromo_tours";

$conn = new mysqli($servername, $username, $password);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
$conn->query($sql);

$conn->select_db($dbname);

$createTable = "CREATE TABLE IF NOT EXISTS pesanan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_pemesan VARCHAR(100) NOT NULL,
    nomor_hp VARCHAR(15) NOT NULL,
    tanggal_pesan DATE NOT NULL,
    waktu_pelaksanaan VARCHAR(50) NOT NULL,
    pelayanan VARCHAR(255) NOT NULL,
    jumlah_peserta INT NOT NULL,
    harga_paket INT NOT NULL,
    jumlah_tagihan INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

$conn->query($createTable);
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'create';

    if ($action === 'delete') {
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        if ($id <= 0) {
            header('Location: daftar_pesanan.php?delete=error');
            exit;
        }

        $stmtCheck = $conn->prepare("SELECT nomor_hp FROM pesanan WHERE id = ?");
        $stmtCheck->bind_param('i', $id);
        $stmtCheck->execute();
        $res = $stmtCheck->get_result();
        $row = $res->fetch_assoc();
        $stmtCheck->close();

        $ownerPhone = $row['nomor_hp'] ?? null;
        $isAdmin = $_SESSION['is_admin'] ?? false;
        $userPhone = $_SESSION['user_phone'] ?? null;

        if (!$isAdmin && $userPhone !== $ownerPhone) {
            header('Location: daftar_pesanan.php?delete=forbidden');
            exit;
        }

        $stmtDel = $conn->prepare("DELETE FROM pesanan WHERE id = ?");
        $stmtDel->bind_param('i', $id);
        if ($stmtDel->execute()) {
            $stmtDel->close();
            if ($isAdmin) header('Location: daftar_pesanan.php?delete=success');
            else header('Location: pesanan_saya.php');
            exit;
        } else {
            $stmtDel->close();
            header('Location: daftar_pesanan.php?delete=error');
            exit;
        }
    }
    
    $nama_pemesan = isset($_POST['nama_pemesan']) ? trim($_POST['nama_pemesan']) : '';
    $nomor_hp = isset($_POST['nomor_hp']) ? trim($_POST['nomor_hp']) : '';
    $tanggal_pesan = isset($_POST['tanggal_pesan']) ? trim($_POST['tanggal_pesan']) : '';
    $waktu_pelaksanaan = isset($_POST['waktu_pelaksanaan']) ? trim($_POST['waktu_pelaksanaan']) : '';
    $pelayanan = isset($_POST['pelayanan']) ? implode(',', $_POST['pelayanan']) : '';
    $jumlah_peserta = isset($_POST['jumlah_peserta']) ? intval($_POST['jumlah_peserta']) : 0;
    $harga_paket = isset($_POST['harga_paket']) ? intval($_POST['harga_paket']) : 0;
    $jumlah_tagihan = isset($_POST['jumlah_tagihan']) ? intval($_POST['jumlah_tagihan']) : 0;

    if (empty($nama_pemesan) || empty($nomor_hp) || empty($tanggal_pesan) || 
        empty($waktu_pelaksanaan) || empty($pelayanan) || $jumlah_peserta <= 0) {
        header('Location: pemesanan.php?error=incomplete');
        exit;
    }

    if (!preg_match('/^[0-9]{10,13}$/', $nomor_hp)) {
        header('Location: pemesanan.php?error=invalid_phone');
        exit;
    }

    $date_obj = DateTime::createFromFormat('Y-m-d', $tanggal_pesan);
    if (!$date_obj || $date_obj->format('Y-m-d') !== $tanggal_pesan) {
        header('Location: pemesanan.php?error=invalid_date');
        exit;
    }

    try {
        if ($action === 'update') {
            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            
            if ($id <= 0) {
                header('Location: pemesanan.php?error=invalid_id');
                exit;
            }

            $stmtOwner = $conn->prepare("SELECT nomor_hp FROM pesanan WHERE id = ?");
            $stmtOwner->bind_param('i', $id);
            $stmtOwner->execute();
            $resOwner = $stmtOwner->get_result();
            $rowOwner = $resOwner->fetch_assoc();
            $stmtOwner->close();

            $ownerPhone = $rowOwner['nomor_hp'] ?? null;
            $isAdmin = $_SESSION['is_admin'] ?? false;
            $userPhone = $_SESSION['user_phone'] ?? null;

            if (!$isAdmin && $userPhone !== $ownerPhone) {
                header('Location: pemesanan.php?error=forbidden');
                exit;
            }

            $stmt = $conn->prepare("UPDATE pesanan SET 
                nama_pemesan = ?, 
                nomor_hp = ?, 
                tanggal_pesan = ?, 
                waktu_pelaksanaan = ?, 
                pelayanan = ?, 
                jumlah_peserta = ?, 
                harga_paket = ?, 
                jumlah_tagihan = ? 
                WHERE id = ?");

            if (!$stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }

            $stmt->bind_param("sssssiiii", 
                $nama_pemesan, $nomor_hp, $tanggal_pesan, 
                $waktu_pelaksanaan, $pelayanan, $jumlah_peserta, 
                $harga_paket, $jumlah_tagihan, $id);

            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }

            $stmt->close();
            $isAdmin = $_SESSION['is_admin'] ?? false;
            if ($isAdmin) {
                header('Location: daftar_pesanan.php?admin=1&updated=1');
            } else {
                header('Location: pesanan_saya.php?updated=1');
            }
        } else {
            $stmt = $conn->prepare("INSERT INTO pesanan 
                (nama_pemesan, nomor_hp, tanggal_pesan, waktu_pelaksanaan, pelayanan, jumlah_peserta, harga_paket, jumlah_tagihan) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

            if (!$stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }

            $stmt->bind_param("sssssiii", 
                $nama_pemesan, $nomor_hp, $tanggal_pesan, 
                $waktu_pelaksanaan, $pelayanan, $jumlah_peserta, 
                $harga_paket, $jumlah_tagihan);

            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }

            $new_id = $stmt->insert_id;
            $stmt->close();
            $_SESSION['user_phone'] = $nomor_hp;
            header('Location: pesanan_saya.php?created=1');
        }
        exit;
    } catch (Exception $e) {
        error_log("Error: " . $e->getMessage());
        header('Location: pemesanan.php?error=database');
        exit;
    }
}

$conn->close();
?>
