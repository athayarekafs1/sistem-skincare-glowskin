<?php
require_once '../config/koneksi.php';
require_once '../config/helper.php';

$id_pesanan = $_GET['id'];

// Update status pesanan jadi "Dibayar"
mysqli_query($koneksi, "UPDATE pesanan SET status = 'Dibayar' WHERE id_pesanan = '$id_pesanan'");

$query = mysqli_query($koneksi, "SELECT * FROM pesanan WHERE id_pesanan = '$id_pesanan'");
$pesanan = mysqli_fetch_assoc($query);

if (!$pesanan) { header("Location: katalog.php"); exit; }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pembayaran Berhasil - GlowSkin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html, body { margin: 0; padding: 0; height: 100vh; overflow: hidden; background: linear-gradient(135deg, #fce4ec, #f8bbd0); font-family: 'Segoe UI', sans-serif; }
        .success-wrapper { height: 100vh; display: flex; align-items: center; justify-content: center; padding: 15px; }
        .success-card { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); width: 100%; max-width: 440px; background: white; text-align: center; padding: 30px 25px; }
        .check-circle { width: 100px; height: 100px; border-radius: 50%; background: linear-gradient(135deg, #66bb6a, #43a047); color: white; display: flex; align-items: center; justify-content: center; font-size: 3.5rem; margin: 0 auto 20px; animation: pop 0.6s ease-out; }
        @keyframes pop { 0% { transform: scale(0); opacity: 0; } 70% { transform: scale(1.1); } 100% { transform: scale(1); opacity: 1; } }
    </style>
</head>
<body>
<div class="success-wrapper">
    <div class="success-card">
        <div class="check-circle">✓</div>
        <h3 style="color: #43a047; font-weight: bold;">Pembayaran Berhasil!</h3>
        <p class="text-muted">Terima kasih, <b><?= $pesanan['nama_pemesan']; ?></b>. Pesanan Anda sedang kami proses.</p>
        <hr>
        <p class="mb-1">Total: <b><?= rupiah($pesanan['total_bayar']); ?></b></p>
        <p class="mb-1">Metode: <b><?= $pesanan['metode_bayar']; ?></b></p>
        <hr>
        <p class="small text-muted">Sales kami akan segera mengirim pesanan Anda ke alamat:</p>
        <p class="small"><b><?= $pesanan['alamat']; ?></b></p>
        <a href="katalog.php" class="btn btn-danger w-100 mt-2">Kembali ke Katalog</a>
    </div>
</div>
</body>
</html>