<?php
require_once '../config/koneksi.php';
require_once '../config/helper.php';

// KONDISI 1: Dari PESAN LANGSUNG (ada ?id=X)
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_barang = $_GET['id'];
    
    // Ambil produk
    $q = mysqli_query($koneksi, "SELECT * FROM barang WHERE id_barang = '$id_barang' AND stok > 0");
    $b = mysqli_fetch_assoc($q);
    
    if (!$b) {
        header("Location: katalog.php");
        exit;
    }
    
    // Bikin "keranjang sementara" cuma 1 produk
    $keranjang = [$id_barang => 1];
    
} else {
    // KONDISI 2: Dari KERANJANG
    $keranjang = isset($_SESSION['keranjang']) ? $_SESSION['keranjang'] : [];
    
    if (empty($keranjang)) {
        header("Location: katalog.php");
        exit;
    }
}

// Ambil detail produk
$items = [];
$total = 0;
foreach ($keranjang as $id_barang => $jumlah) {
    $q = mysqli_query($koneksi, "SELECT * FROM barang WHERE id_barang = '$id_barang'");
    $b = mysqli_fetch_assoc($q);
    if ($b) {
        $subtotal = $b['harga'] * $jumlah;
        $total += $subtotal;
        $items[] = [
            'id_barang' => $b['id_barang'],
            'nama_barang' => $b['nama_barang'],
            'kategori' => $b['kategori'],
            'harga' => $b['harga'],
            'jumlah' => $jumlah,
            'subtotal' => $subtotal
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Checkout - GlowSkin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Poppins', sans-serif; }
        body { background: #fff5f8; padding: 30px 15px; }
        .checkout-card { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); max-width: 900px; margin: auto; background: white; padding: 30px; }
        .checkout-card h3 { color: #d81b60; font-weight: bold; text-align: center; margin-bottom: 25px; }
        .section-title { color: #d81b60; font-weight: 600; font-size: 0.95rem; margin-bottom: 12px; padding-bottom: 8px; border-bottom: 2px solid #fce4ec; }
        .form-label { font-weight: 500; font-size: 0.85rem; color: #333; }
        .form-control { font-size: 0.9rem; padding: 10px 15px; border-radius: 10px; border: 1px solid #ddd; }
        .form-control:focus { border-color: #d81b60; box-shadow: 0 0 0 0.2rem rgba(216,27,96,0.15); }
        .produk-list-item { padding: 10px 0; border-bottom: 1px solid #f5f5f5; display: flex; justify-content: space-between; font-size: 0.85rem; }
        .produk-list-item:last-child { border-bottom: none; }
        .produk-list-item b { color: #333; }
        .produk-list-item .subtotal { color: #d81b60; font-weight: bold; }
        .total-box { background: linear-gradient(135deg, #ec407a, #d81b60); color: white; padding: 15px 20px; border-radius: 12px; display: flex; justify-content: space-between; align-items: center; margin-top: 15px; }
        .total-box small { font-size: 0.75rem; opacity: 0.9; }
        .total-box h4 { margin: 0; font-weight: bold; }
        .metode-option { display: flex; gap: 10px; flex-wrap: wrap; }
        .metode-option input[type="radio"] { display: none; }
        .metode-option label { flex: 1; min-width: 120px; padding: 12px; border: 2px solid #eee; border-radius: 10px; text-align: center; cursor: pointer; font-size: 0.85rem; transition: all 0.3s; }
        .metode-option label:hover { border-color: #f8bbd0; background: #fff5f8; }
        .metode-option input[type="radio"]:checked + label { border-color: #d81b60; background: #fce4ec; color: #d81b60; font-weight: 600; }
        .btn-bayar { background: linear-gradient(135deg, #ec407a, #d81b60); color: white; border: none; border-radius: 12px; padding: 14px; font-weight: 600; font-size: 1rem; width: 100%; margin-top: 15px; }
        .btn-bayar:hover { background: linear-gradient(135deg, #d81b60, #ad1457); color: white; }
    </style>
</head>
<body>

<div class="container">
    <div class="checkout-card">
        <h3>📋 Checkout Pesanan</h3>

        <form action="proses_checkout.php" method="POST">
            <!-- Kirim data produk sebagai hidden input -->
            <?php foreach ($items as $item): ?>
                <input type="hidden" name="id_barang[]" value="<?= $item['id_barang']; ?>">
                <input type="hidden" name="jumlah[]" value="<?= $item['jumlah']; ?>">
            <?php endforeach; ?>

            <div class="row">
                <!-- KIRI: Form Data Pelanggan -->
                <div class="col-md-7">
                    <div class="section-title">📝 Data Pelanggan</div>
                    
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap *</label>
                        <input type="text" name="nama" class="form-control" placeholder="Contoh: Rianty" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">No. HP / WhatsApp *</label>
                        <input type="text" name="no_hp" class="form-control" placeholder="Contoh: 081234567890" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat Pengiriman *</label>
                        <textarea name="alamat" class="form-control" rows="3" placeholder="Tulis alamat lengkap..." required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan (Opsional)</label>
                        <input type="text" name="catatan" class="form-control" placeholder="">
                    </div>

                    <div class="section-title mt-4">💳 Metode Pembayaran</div>
                    <div class="metode-option">
                        <input type="radio" name="metode_bayar" id="qris" value="QRIS" checked>
                        <label for="qris">📷 QRIS</label>
                        <input type="radio" name="metode_bayar" id="transfer" value="Transfer Bank">
                        <label for="transfer">🏦 Transfer Bank</label>
                        <input type="radio" name="metode_bayar" id="ewallet" value="E-Wallet">
                        <label for="ewallet">📱 E-Wallet</label>
                    </div>
                </div>

                <!-- KANAN: Ringkasan Pesanan -->
                <div class="col-md-5">
                    <div class="section-title">🛍️ Ringkasan Pesanan</div>
                    
                    <div style="background: #fff5f8; padding: 15px; border-radius: 10px;">
                        <?php foreach ($items as $item): ?>
                        <div class="produk-list-item">
                            <div>
                                <b><?= htmlspecialchars($item['nama_barang']); ?></b><br>
                                <small class="text-muted"><?= $item['jumlah']; ?> x <?= rupiah($item['harga']); ?></small>
                            </div>
                            <div class="subtotal"><?= rupiah($item['subtotal']); ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="total-box">
                        <div>
                            <small>TOTAL BAYAR</small>
                            <h4><?= rupiah($total); ?></h4>
                        </div>
                    </div>

                    <?php if (!isset($_GET['id'])): ?>
                        <a href="keranjang.php" class="btn btn-outline-secondary w-100 mt-3" style="border-radius: 10px;">✏️ Ubah Keranjang</a>
                    <?php endif; ?>
                </div>
            </div>

            <button type="submit" class="btn-bayar">💳 Bayar Sekarang</button>
            <a href="katalog.php" class="btn btn-secondary w-100 mt-2">⬅️ Kembali ke Katalog</a>
        </form>
    </div>
</div>

</body>
</html>