<?php
require_once '../../config/koneksi.php';
require_once '../../config/helper.php';
cek_login();

$id_transaksi = $_GET['id'];
$query = "SELECT t.* FROM transaksi t WHERE t.id_transaksi = '$id_transaksi'";
$trx = mysqli_fetch_assoc(mysqli_query($koneksi, $query));

if (!$trx) { header("Location: ../dashboard.php"); exit; }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pembayaran Berhasil - GlowSkin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html, body { 
            margin: 0; padding: 0; height: 100vh; overflow: hidden;
            background: linear-gradient(135deg, #fce4ec, #f8bbd0);
            font-family: 'Segoe UI', sans-serif;
        }
        .success-wrapper {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 15px;
        }
        .success-card { 
            border: none; 
            border-radius: 20px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.1); 
            width: 100%;
            max-width: 440px;
            background: white; 
            text-align: center; 
            padding: 40px 25px;
        }
        .check-circle {
            width: 100px; height: 100px; border-radius: 50%;
            background: linear-gradient(135deg, #66bb6a, #43a047);
            color: white; display: flex; align-items: center; justify-content: center;
            font-size: 3.5rem; margin: 0 auto 20px;
            animation: pop 0.6s ease-out;
        }
        @keyframes pop {
            0% { transform: scale(0); opacity: 0; }
            70% { transform: scale(1.1); }
            100% { transform: scale(1); opacity: 1; }
        }
    </style>
</head>
<body>
<div class="success-wrapper">
    <div class="success-card">
        <div class="check-circle">✓</div>
        <h3 style="color: #43a047; font-weight: bold;">Pembayaran Berhasil!</h3>
        <p class="text-muted">Transaksi <b><?= $trx['kode_transaksi']; ?></b> telah dikonfirmasi.</p>
        <hr>
        <p class="mb-1">Total: <b><?= rupiah($trx['total_bayar']); ?></b></p>
        <p class="mb-1">Metode: <b><?= $trx['metode_bayar']; ?></b></p>
        <p class="text-muted small mt-3 mb-2">Mengarahkan ke struk...</p>
        <div class="progress" style="height: 6px; border-radius: 10px;">
            <div class="progress-bar bg-success" id="pb" style="width: 0%; transition: width 2.5s linear;"></div>
        </div>
    </div>
</div>

<script>
setTimeout(function() { document.getElementById('pb').style.width = '100%'; }, 100);
setTimeout(function() {
    window.location.href = 'struk.php?id=<?= $id_transaksi; ?>';
}, 3000);
</script>
</body>
</html>