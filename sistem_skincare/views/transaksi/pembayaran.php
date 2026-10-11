<?php
require_once '../../config/koneksi.php';
require_once '../../config/helper.php';
cek_login();

$id_transaksi = $_GET['id'];
$query = "SELECT t.*, u.nama_lengkap FROM transaksi t 
          JOIN users u ON t.id_user = u.id_user WHERE t.id_transaksi = '$id_transaksi'";
$trx = mysqli_fetch_assoc(mysqli_query($koneksi, $query));

if (!$trx) { header("Location: ../dashboard.php"); exit; }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pembayaran - GlowSkin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        html, body { 
            margin: 0; padding: 0; height: 100vh; overflow: hidden;
            background: linear-gradient(135deg, #fce4ec, #f8bbd0);
            font-family: 'Segoe UI', sans-serif;
        }
        .pay-wrapper {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 15px;
        }
        .pay-card { 
            border: none; 
            border-radius: 20px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.1); 
            width: 100%;
            max-width: 440px;
            background: white; 
            text-align: center; 
            padding: 20px 25px;
            max-height: 95vh;
            overflow-y: auto;
        }
        .pay-icon { font-size: 40px; margin-bottom: 5px; }
        .pay-title { color: #d81b60; font-weight: bold; margin-bottom: 5px; font-size: 1.3rem; }
        .pay-code { color: #888; font-size: 0.8rem; margin-bottom: 10px; }
        .total-show { 
            background: #fce4ec; 
            padding: 10px; 
            border-radius: 12px; 
            margin: 10px 0; 
        }
        .total-show small { font-size: 0.7rem; color: #888; }
        .total-show h4 { color: #d81b60; font-weight: bold; margin: 3px 0 0; font-size: 1.3rem; }
        .qr-img { width: 130px; height: 130px; border-radius: 10px; border: 3px solid #f8bbd0; margin: 8px 0; }
        .timer-circle { 
            width: 70px; height: 70px; border-radius: 50%; 
            background: linear-gradient(135deg, #ec407a, #d81b60);
            color: white; display: flex; align-items: center; justify-content: center;
            font-size: 1.8rem; font-weight: bold; margin: 10px auto;
            box-shadow: 0 5px 20px rgba(216,27,96,0.4);
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.08); }
            100% { transform: scale(1); }
        }
        .timer-text { font-size: 0.75rem; color: #888; margin: 0; }
        .progress { height: 6px; border-radius: 10px; margin-top: 8px; }
        .info-box { font-size: 0.78rem; padding: 8px 12px; margin: 8px 0; }
    </style>
</head>
<body>
<div class="pay-wrapper">
    <div class="pay-card">
        <div class="pay-icon">💳</div>
        <div class="pay-title">Menunggu Pembayaran</div>
        <div class="pay-code">Kode: <b><?= $trx['kode_transaksi']; ?></b></div>

        <div class="total-show">
            <small>Total yang harus dibayar</small>
            <h4><?= rupiah($trx['total_bayar']); ?></h4>
        </div>

        <p class="mb-1 small text-muted">Metode: <b><?= $trx['metode_bayar']; ?></b></p>

        <?php if ($trx['metode_bayar'] == 'QRIS'): ?>
            <p class="small text-muted mb-1">Scan QRIS di bawah:</p>
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?= urlencode($trx['kode_transaksi'] . '-' . $trx['total_bayar']); ?>" 
                 alt="QRIS" class="qr-img">
        <?php elseif ($trx['metode_bayar'] == 'Transfer Bank'): ?>
            <div class="alert alert-info info-box text-start">
                <b>Transfer ke:</b><br>
                Bank BCA - 1234567890<br>
                a.n. GlowSkin Skincare
            </div>
        <?php elseif ($trx['metode_bayar'] == 'E-Wallet'): ?>
            <div class="alert alert-info info-box text-start">
                <b>Kirim ke E-Wallet:</b><br>
                OVO / Dana / Gopay<br>
                No. HP: 0812-3456-7890
            </div>
        <?php else: ?>
            <div class="alert alert-info info-box">
                💵 Pembayaran tunai di kasir.
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
        window.location.href = 'pembayaran_sukses.php?id=<?= $id_transaksi; ?>';
    }
}, 1000);
</script>
</body>
</html>