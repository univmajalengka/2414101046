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
if ($conn->query($sql) === TRUE) {
}

$conn->select_db($dbname);

$createTable = "CREATE TABLE IF NOT EXISTS pesanan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_pemesan VARCHAR(100) NOT NULL,
    nomor_hp VARCHAR(15) NOT NULL,
    tanggal_pesan DATE NOT NULL,
    waktu_pelaksanaan VARCHAR(50) NOT NULL,
    pelayanan VARCHAR(100) NOT NULL,
    jumlah_peserta INT NOT NULL,
    harga_paket INT NOT NULL,
    jumlah_tagihan INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

$conn->query($createTable);

$editId = isset($_GET['edit']) ? $_GET['edit'] : null;
$editData = null;

if ($editId) {
    $stmt = $conn->prepare("SELECT * FROM pesanan WHERE id = ?");
    $stmt->bind_param("i", $editId);
    $stmt->execute();
    $result = $stmt->get_result();
    $editData = $result->fetch_assoc();
    $stmt->close();
}

$hargaLayanan = array(
    'transport' => 300000,
    'pemandu' => 200000,
    'makan' => 150000,
    'akomodasi' => 500000,
    'fotografi' => 250000
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pemesanan - Bromo Tours</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .booking-form-container {
            max-width: 700px;
            margin: 100px auto;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }

        .booking-form-container h2 {
            color: var(--primary-color);
            margin-bottom: 2rem;
            text-align: center;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--dark-color);
            font-weight: 600;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 5px rgba(196, 30, 58, 0.3);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }

        .checkbox-group {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 0.5rem;
        }

        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .checkbox-item input[type="checkbox"] {
            width: auto;
            cursor: pointer;
        }

        .checkbox-item label {
            margin: 0;
            font-weight: 400;
            cursor: pointer;
        }

        .price-display {
            background: var(--light-color);
            padding: 1rem;
            border-radius: 5px;
            margin-top: 0.5rem;
            font-weight: bold;
            color: var(--primary-color);
        }

        .form-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-submit {
            background: var(--primary-color);
            color: white;
        }

        .btn-submit:hover {
            background: #a01733;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(196, 30, 58, 0.3);
        }

        .btn-back {
            background: #ccc;
            color: var(--dark-color);
        }

        .btn-back:hover {
            background: #999;
        }

        .error-message {
            color: #d32f2f;
            font-size: 0.9rem;
            margin-top: 0.3rem;
            display: none;
        }

        .success-message {
            background: #4caf50;
            color: white;
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1.5rem;
            text-align: center;
            animation: slideInDown 0.5s ease;
        }

        .info-box {
            background: #e3f2fd;
            border-left: 4px solid var(--primary-color);
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: 5px;
            font-size: 0.9rem;
            color: #1565c0;
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

        .calculation-breakdown {
            background: #f9f9f9;
            padding: 1rem;
            border-radius: 5px;
            margin-top: 1rem;
            font-size: 0.95rem;
        }

        .calculation-breakdown p {
            margin: 0.3rem 0;
            color: #555;
        }

        .calculation-total {
            border-top: 2px solid var(--primary-color);
            padding-top: 0.5rem;
            margin-top: 0.5rem;
            font-weight: bold;
            color: var(--primary-color);
        }

        .two-column {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        @media (max-width: 600px) {
            .booking-form-container {
                margin: 90px 1rem;
            }

            .form-buttons,
            .two-column {
                grid-template-columns: 1fr;
            }
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
        <div class="booking-form-container">
            <h2><?php echo $editId ? 'Edit Pesanan' : 'Form Pemesanan Paket Wisata'; ?></h2>

            <div class="info-box">
                ℹ️ Harap isi semua field yang diperlukan. Harga akan dihitung otomatis berdasarkan layanan yang dipilih dan jumlah peserta.
            </div>

            <?php
            if (isset($_GET['error'])) {
                $err = $_GET['error'];
                $msg = '';
                if ($err === 'incomplete') $msg = 'Form belum lengkap. Harap isi semua field yang diperlukan.';
                elseif ($err === 'invalid_phone') $msg = 'Nomor HP tidak valid. Masukkan 10-13 digit angka.';
                elseif ($err === 'invalid_date') $msg = 'Tanggal tidak valid.';
                elseif ($err === 'invalid_id') $msg = 'ID pesanan tidak valid.';
                elseif ($err === 'forbidden') $msg = 'Anda tidak memiliki izin untuk mengubah data ini.';
                elseif ($err === 'database') $msg = 'Terjadi kesalahan pada server. Silakan coba lagi.';
                else $msg = 'Terjadi kesalahan: ' . htmlspecialchars($err);

                echo '<div class="message error">' . $msg . '</div>';
            }
            ?>

            <?php
            if (isset($_GET['success'])) {
                echo '<div class="success-message">✓ Pesanan berhasil ' . (isset($_GET['edit']) ? 'diperbarui' : 'disimpan') . '! Anda akan diarahkan ke halaman daftar pesanan.</div>';
                echo '<script>setTimeout(function() { window.location.href = "daftar_pesanan.php"; }, 2000);</script>';
            }
            ?>

            <form id="bookingForm" method="POST" action="proses_pesanan.php">
                <?php if ($editId): ?>
                    <input type="hidden" name="id" value="<?php echo $editData['id']; ?>">
                    <input type="hidden" name="action" value="update">
                <?php else: ?>
                    <input type="hidden" name="action" value="create">
                <?php endif; ?>

                <div class="form-group">
                    <label for="nama">Nama Pemesan *</label>
                    <input type="text" id="nama" name="nama_pemesan" 
                           value="<?php echo $editData ? htmlspecialchars($editData['nama_pemesan']) : ''; ?>"
                           placeholder="Masukkan nama lengkap Anda" required>
                    <div class="error-message" id="error-nama">Nama harus diisi</div>
                </div>

                <div class="form-group">
                    <label for="hp">Nomor HP/Telp *</label>
                    <input type="tel" id="hp" name="nomor_hp" 
                           value="<?php echo $editData ? htmlspecialchars($editData['nomor_hp']) : ''; ?>"
                           placeholder="Contoh: 081234567890" required>
                    <div class="error-message" id="error-hp">Nomor HP harus diisi (10-13 digit)</div>
                </div>

                <div class="form-group">
                    <label for="tanggal">Tanggal Pesan *</label>
                    <input type="date" id="tanggal" name="tanggal_pesan" 
                           value="<?php echo $editData ? htmlspecialchars($editData['tanggal_pesan']) : ''; ?>"
                           required>
                    <div class="error-message" id="error-tanggal">Tanggal pesan harus diisi</div>
                </div>

                <div class="form-group">
                    <label for="waktu">Waktu Pelaksanaan Perjalanan *</label>
                    <select id="waktu" name="waktu_pelaksanaan" required>
                        <option value="">-- Pilih Durasi Perjalanan --</option>
                        <option value="1 Hari" <?php echo ($editData && $editData['waktu_pelaksanaan'] == '1 Hari') ? 'selected' : ''; ?>>1 Hari</option>
                        <option value="2 Hari 1 Malam" <?php echo ($editData && $editData['waktu_pelaksanaan'] == '2 Hari 1 Malam') ? 'selected' : ''; ?>>2 Hari 1 Malam</option>
                        <option value="3 Hari 2 Malam" <?php echo ($editData && $editData['waktu_pelaksanaan'] == '3 Hari 2 Malam') ? 'selected' : ''; ?>>3 Hari 2 Malam</option>
                        <option value="4 Hari 3 Malam" <?php echo ($editData && $editData['waktu_pelaksanaan'] == '4 Hari 3 Malam') ? 'selected' : ''; ?>>4 Hari 3 Malam</option>
                    </select>
                    <div class="error-message" id="error-waktu">Waktu pelaksanaan harus dipilih</div>
                </div>

                <div class="form-group">
                    <label>Pilihan Layanan *</label>
                    <div class="checkbox-group">
                        <?php foreach ($hargaLayanan as $key => $harga): 
                            $labels = array(
                                'transport' => 'Transportasi (Rp ' . number_format($harga, 0, ',', '.') . ')',
                                'pemandu' => 'Pemandu Wisata (Rp ' . number_format($harga, 0, ',', '.') . ')',
                                'makan' => 'Makan & Minum (Rp ' . number_format($harga, 0, ',', '.') . ')',
                                'akomodasi' => 'Akomodasi (Rp ' . number_format($harga, 0, ',', '.') . ')',
                                'fotografi' => 'Fotografer (Rp ' . number_format($harga, 0, ',', '.') . ')'
                            );
                        ?>
                            <div class="checkbox-item">
                                <input type="checkbox" id="service_<?php echo $key; ?>" 
                                       name="pelayanan[]" value="<?php echo $key; ?>" 
                                       data-price="<?php echo $harga; ?>"
                                       <?php 
                                       if ($editData) {
                                           $services = explode(',', $editData['pelayanan']);
                                           echo in_array($key, $services) ? 'checked' : '';
                                       }
                                       ?>>
                                <label for="service_<?php echo $key; ?>"><?php echo $labels[$key]; ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="error-message" id="error-pelayanan">Pilih minimal satu layanan</div>
                </div>

                <div class="form-group">
                    <label for="peserta">Jumlah Peserta *</label>
                    <input type="number" id="peserta" name="jumlah_peserta" 
                           value="<?php echo $editData ? htmlspecialchars($editData['jumlah_peserta']) : '1'; ?>"
                           min="1" max="50" placeholder="Jumlah peserta" required>
                    <div class="error-message" id="error-peserta">Jumlah peserta harus diisi (minimal 1)</div>
                </div>

                <div class="form-group">
                    <label for="hargapaket">Harga Paket Perjalanan *</label>
                    <input type="hidden" id="hargapaket" name="harga_paket" value="0" required>
                    <div class="price-display" id="hargapaket-display">Rp 0</div>
                    <div class="calculation-breakdown" id="calculation">
                        <p>Perhitungan:</p>
                        <div id="calculation-items"></div>
                        <div class="calculation-total" id="calculation-total">Total: Rp 0</div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="tagihan">Jumlah Tagihan (Hari × Peserta × Harga Paket) *</label>
                    <input type="hidden" id="tagihan" name="jumlah_tagihan" value="0" required>
                    <div class="price-display" id="tagihan-display">Rp 0</div>
                </div>

                <div class="form-buttons">
                    <a href="index.php" class="btn btn-back">← Kembali</a>
                    <button type="submit" class="btn btn-submit"><?php echo $editId ? 'Perbarui Pesanan' : 'Simpan Pesanan'; ?></button>
                </div>

                <div style="margin-top: 1rem; text-align: center;">
                    <a href="daftar_pesanan.php" style="color: var(--primary-color); text-decoration: none; font-weight: 600;">
                        Lihat Daftar Pesanan →
                    </a>
                </div>
            </form>
        </div>
    </main>

    <script>
        const pelayananCheckboxes = document.querySelectorAll('input[name="pelayanan[]"]');
        const jumlahPesertaInput = document.getElementById('peserta');
        const waktuInput = document.getElementById('waktu');
        const hargaPaketInput = document.getElementById('hargapaket');
        const tagihanhInput = document.getElementById('tagihan');
        const hargaDisplay = document.getElementById('hargapaket-display');
        const tagihanDisplay = document.getElementById('tagihan-display');
        const calculationItems = document.getElementById('calculation-items');
        const calculationTotal = document.getElementById('calculation-total');
        const calculationFormula = document.getElementById('calculation-formula');

        function calculatePrices() {
            let hargaPaket = 0;
            const selectedServices = [];

            pelayananCheckboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    const price = parseInt(checkbox.getAttribute('data-price'));
                    hargaPaket += price;
                    selectedServices.push(checkbox.value);
                }
            });

            hargaPaketInput.value = hargaPaket;
            hargaDisplay.textContent = 'Rp ' + hargaPaket.toLocaleString('id-ID');

            calculationItems.innerHTML = '';
            selectedServices.forEach(service => {
                const checkbox = document.querySelector(`input[name="pelayanan[]"][value="${service}"]`);
                const price = parseInt(checkbox.getAttribute('data-price'));
                const labels = {
                    'transport': 'Transportasi',
                    'pemandu': 'Pemandu Wisata',
                    'makan': 'Makan & Minum',
                    'akomodasi': 'Akomodasi',
                    'fotografi': 'Fotografer'
                };
                calculationItems.innerHTML += `<p>+ ${labels[service]}: Rp ${price.toLocaleString('id-ID')}</p>`;
            });
            calculationTotal.innerHTML = `Total: Rp ${hargaPaket.toLocaleString('id-ID')}`;

            calculateTotalBilling();
        }

        function calculateTotalBilling() {
            const waktuPelaksanaan = waktuInput.value;
            const jumlahPeserta = parseInt(jumlahPesertaInput.value) || 1;
            const hargaPaket = parseInt(hargaPaketInput.value) || 0;

            let hari = 1;
            if (waktuPelaksanaan === '2 Hari 1 Malam') {
                hari = 2;
            } else if (waktuPelaksanaan === '3 Hari 2 Malam') {
                hari = 3;
            } else if (waktuPelaksanaan === '4 Hari 3 Malam') {
                hari = 4;
            }

            const jumlahTagihan = hari * jumlahPeserta * hargaPaket;

            tagihanhInput.value = jumlahTagihan;
            tagihanDisplay.textContent = 'Rp ' + jumlahTagihan.toLocaleString('id-ID');
            if (calculationFormula) {
                calculationFormula.textContent = `${hari} × ${jumlahPeserta} × Rp ${hargaPaket.toLocaleString('id-ID')} = Rp ${jumlahTagihan.toLocaleString('id-ID')}`;
            }
        }

        pelayananCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', calculatePrices);
        });

        jumlahPesertaInput.addEventListener('change', calculateTotalBilling);
        waktuInput.addEventListener('change', calculateTotalBilling);

        document.getElementById('bookingForm').addEventListener('submit', function(e) {
            e.preventDefault();

            let isValid = true;
            const nama = document.getElementById('nama').value.trim();
            const hp = document.getElementById('hp').value.trim();
            const tanggal = document.getElementById('tanggal').value;
            const waktu = document.getElementById('waktu').value;
            const peserta = document.getElementById('peserta').value;
            const selectedServices = Array.from(pelayananCheckboxes).filter(cb => cb.checked).length;

            document.querySelectorAll('.error-message').forEach(msg => msg.style.display = 'none');

            if (!nama) {
                document.getElementById('error-nama').style.display = 'block';
                isValid = false;
            }

            if (!hp || hp.length < 10 || hp.length > 13 || !/^\d+$/.test(hp)) {
                document.getElementById('error-hp').style.display = 'block';
                isValid = false;
            }

            if (!tanggal) {
                document.getElementById('error-tanggal').style.display = 'block';
                isValid = false;
            }

            if (!waktu) {
                document.getElementById('error-waktu').style.display = 'block';
                isValid = false;
            }

            if (!peserta || peserta < 1) {
                document.getElementById('error-peserta').style.display = 'block';
                isValid = false;
            }

            if (selectedServices === 0) {
                document.getElementById('error-pelayanan').style.display = 'block';
                isValid = false;
            }

            if (isValid) {
                this.submit();
            }
        });

        calculatePrices();
    </script>
</body>
</html>
