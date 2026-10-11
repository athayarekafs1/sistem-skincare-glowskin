<?php
require_once '../config/koneksi.php';
require_once '../config/helper.php';

$id_pesanan = $_GET['id'];
$query = mysqli_query($koneksi, "SELECT * FROM pesanan WHERE id_pesanan = '$id_pesanan'");
$pesanan = mysqli_fetch_assoc($query);

if (!$pesanan) {
    header("Location: katalog.php");
    exit;
}

$detail = mysqli_query($koneksi, "SELECT pd.*, b.nama_barang 
                                   FROM pesanan_detail pd 
                                   JOIN barang b ON pd.id_barang = b.id_barang 
                                   WHERE pd.id_pesanan = '$id_pesanan'");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pembayaran - GlowSkin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        html, body { 
            margin: 0; padding: 0; min-height: 100vh;
            background: linear-gradient(135deg, #fce4ec, #f8bbd0);
        }
        .pay-wrapper { 
            min-height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            padding: 20px 15px;
        }
        .pay-card { 
            border: none; 
            border-radius: 15px; 
            box-shadow: 0 8px 25px rgba(0,0,0,0.08); 
            width: 100%;
            max-width: 420px;  /* Diperkecil dari 480px */
            background: white; 
            padding: 20px 22px; 
            text-align: center;
        }
        .pay-icon { font-size: 38px; margin-bottom: 3px; }
        .pay-title { color: #d81b60; font-weight: bold; margin-bottom: 3px; font-size: 1.15rem; }
        .pay-code { color: #888; font-size: 0.75rem; margin-bottom: 12px; }
        
        .total-show { 
            background: #fce4ec; 
            padding: 10px; 
            border-radius: 10px; 
            text-align: center; 
            margin: 10px 0; 
        }
        .total-show small { font-size: 0.7rem; color: #888; }
        .total-show h4 { color: #d81b60; font-weight: bold; margin: 3px 0 0; font-size: 1.25rem; }
        
        /* QRIS wrapper */
        .qr-wrapper { 
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            margin: 12px 0;
            padding: 12px;
            background: #fff5f8;
            border-radius: 10px;
        }
        .qr-wrapper p {
            margin: 0 0 8px 0;
            font-size: 0.75rem;
            color: #888;
        }
        .qr-img { 
            width: 160px; 
            height: 160px; 
            border-radius: 8px; 
            border: 3px solid #f8bbd0; 
            background: white;
            padding: 4px;
            display: block;
            margin: 0 auto;
            object-fit: contain;
        }
        
        .timer-circle { 
            width: 60px; height: 60px; 
            border-radius: 50%; 
            background: linear-gradient(135deg, #ec407a, #d81b60);
            color: white; 
            display: flex; 
            align-items: center; 
            justify-content: center;
            font-size: 1.5rem; 
            font-weight: bold; 
            margin: 12px auto 6px;
            box-shadow: 0 5px 15px rgba(216,27,96,0.4);
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.08); }
            100% { transform: scale(1); }
        }
        .timer-text { font-size: 0.7rem; color: #888; margin: 0; text-align: center; }
        .progress { height: 5px; border-radius: 10px; margin-top: 8px; }
        
        .info-box { 
            background: #fff5f8; 
            padding: 10px 12px; 
            border-radius: 8px; 
            font-size: 0.78rem; 
            text-align: left; 
            margin: 8px 0; 
        }
        
        .detail-list { font-size: 0.75rem; max-height: 100px; overflow-y: auto; margin-top: 5px; }
        .detail-row { display: flex; justify-content: space-between; padding: 4px 0; border-bottom: 1px dashed #eee; }
        .detail-row:last-child { border-bottom: none; }
    </style>
</head>
<body>

<div class="pay-wrapper">
    <div class="pay-card">
        <div class="pay-icon">💳</div>
        <div class="pay-title">Menunggu Pembayaran</div>
        <div class="pay-code">Kode: <b>ORD-<?= str_pad($pesanan['id_pesanan'], 5, '0', STR_PAD_LEFT); ?></b></div>

        <div class="total-show">
            <small>Total yang harus dibayar</small>
            <h4><?= rupiah($pesanan['total_bayar']); ?></h4>
        </div>

        <!-- DETAIL PRODUK -->
        <div class="info-box">
            <b style="color: #d81b60; font-size: 0.8rem;">🛍️ Detail Pesanan:</b>
            <div class="detail-list">
                <?php while ($d = mysqli_fetch_assoc($detail)): ?>
                <div class="detail-row">
                    <span><?= htmlspecialchars($d['nama_barang']); ?> (<?= $d['jumlah']; ?>x)</span>
                    <b><?= rupiah($d['subtotal']); ?></b>
                </div>
                <?php endwhile; ?>
            </div>
        </div>

        <!-- METODE PEMBAYARAN -->
        <?php if ($pesanan['metode_bayar'] == 'QRIS'): ?>
            <div class="qr-wrapper">
                <p>📷 Scan QRIS di bawah untuk membayar</p>
                <img src="uploads/qris.png" alt="QRIS" class="qr-img" 
                     onerror="this.src='https://via.placeholder.com/160x160/f8bbd0/d81b60?text=QRIS';">
                <p style="margin-top: 8px; margin-bottom: 0; font-size: 0.7rem;">a.n. GlowSkin Skincare</p>
            </div>
        <?php elseif ($pesanan['metode_bayar'] == 'Transfer Bank'): ?>
            <div class="info-box">
                <b style="color: #d81b60;">🏦 Transfer ke:</b><br>
                Bank BCA<br>
                No. Rek: <b>1234567890</b><br>
                a.n. GlowSkin Skincare
            </div>
        <?php else: ?>
            <div class="info-box">
                <b style="color: #d81b60;">📱 Kirim ke E-Wallet:</b><br>
                OVO / Dana / Gopay<br>
                No. HP: <b>0812-3456-7890</b><br>
                a.n. GlowSkin
            </div>
        <?php endif; ?>

        <div class="timer-circle" id="timer">8</div>
        <p class="timer-text">Pembayaran akan diverifikasi otomatis</p>

        <div class="progress">
            <div class="progress-bar bg-danger" id="progressBar" style="width: 0%; transition: width 1s linear;"></div>
        </div>
    </div>
</div>

<script>
let detik = 8;
const timerEl = document.getElementById('timer');
const progressEl = document.getElementById('progressBar');

const interval = setInterval(function() {
    detik--;
    timerEl.innerText = detik;
    progressEl.style.width = ((8 - detik) / 8 * 100) + '%';
    if (detik <= 0) {
        clearInterval(interval);
        window.location.href = 'pembayaran_sukses.php?id=<?= $id_pesanan; ?>';
    }
}, 1000);
</script>
</body>
</html>