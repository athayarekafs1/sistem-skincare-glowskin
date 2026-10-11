<?php
require_once '../../config/koneksi.php';
require_once '../../config/helper.php';
cek_login();

$id_transaksi = $_GET['id'];
$query = "SELECT t.*, u.nama_lengkap FROM transaksi t 
          JOIN users u ON t.id_user = u.id_user WHERE t.id_transaksi = '$id_transaksi'";
$trx = mysqli_fetch_assoc(mysqli_query($koneksi, $query));

if (!$trx) { header("Location: ../dashboard.php"); exit; }

$query_detail = "SELECT d.*, b.nama_barang FROM detail_transaksi d 
                 JOIN barang b ON d.id_barang = b.id_barang WHERE d.id_transaksi = '$id_transaksi'";
$detail = mysqli_query($koneksi, $query_detail);

// Cek apakah transaksi ini dari pesanan (katalog)
$alamat_pelanggan = '-';
$no_hp_pelanggan = '-';
if (isset($_SESSION['prefill_pesanan']) && $_SESSION['prefill_pesanan']['id_pesanan']) {
    // Ambil dari pesanan kalau ada
    $id_pesanan = $_SESSION['prefill_pesanan']['id_pesanan'];
    $q_pesanan = mysqli_query($koneksi, "SELECT alamat, no_hp FROM pesanan WHERE id_pesanan = '$id_pesanan'");
    if ($data_pesanan = mysqli_fetch_assoc($q_pesanan)) {
        $alamat_pelanggan = $data_pesanan['alamat'];
        $no_hp_pelanggan = $data_pesanan['no_hp'];
    }
}

// Kalau nggak ada di session, coba cari di tabel pesanan berdasarkan nama & total
if ($alamat_pelanggan == '-') {
    $nama_pel = mysqli_real_escape_string($koneksi, $trx['nama_pelanggan']);
    $q_cari = mysqli_query($koneksi, "SELECT alamat, no_hp FROM pesanan 
                                       WHERE nama_pemesan = '$nama_pel' 
                                       ORDER BY id_pesanan DESC LIMIT 1");
    if ($data_cari = mysqli_fetch_assoc($q_cari)) {
        $alamat_pelanggan = $data_cari['alamat'];
        $no_hp_pelanggan = $data_cari['no_hp'];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk - GlowSkin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        html, body { 
            margin: 0; padding: 0; min-height: 100vh;
            background: linear-gradient(135deg, #fce4ec, #f8bbd0);
            font-family: 'Segoe UI', sans-serif;
        }
        .struk-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 15px;
        }
        .struk-card { 
            border: none; 
            border-radius: 15px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.1); 
            width: 100%;
            max-width: 420px;
            background: white;
            overflow: hidden;
            max-height: 95vh;
            display: flex;
            flex-direction: column;
        }
        .struk-header { 
            background: linear-gradient(135deg, #ec407a, #d81b60); 
            color: white; 
            padding: 15px 20px; 
            text-align: center; 
        }
        .struk-header h5 { margin: 0; font-weight: bold; font-size: 1.1rem; }
        .struk-header small { opacity: 0.9; font-size: 0.75rem; }
        .struk-body { 
            padding: 15px 20px;
            overflow-y: auto;
            flex: 1;
        }
        .struk-info p { margin: 3px 0; font-size: 0.82rem; }
        .struk-line { border-top: 1.5px dashed #ddd; margin: 10px 0; }
        .struk-total { 
            background: #fce4ec; 
            padding: 10px 15px; 
            border-radius: 8px; 
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 8px;
        }
        .struk-total small { font-size: 0.75rem; color: #888; }
        .struk-total h5 { margin: 0; color: #d81b60; font-weight: bold; font-size: 1.1rem; }
        .table-struk { font-size: 0.8rem; margin-bottom: 0; }
        .table-struk th, .table-struk td { padding: 5px 8px; }
        .btn-print { padding: 8px; font-size: 0.85rem; font-weight: 600; }
        .cash-info {
            background: #e8f5e9;
            padding: 8px 12px;
            border-radius: 8px;
            margin-top: 8px;
            display: flex;
            justify-content: space-between;
            font-size: 0.82rem;
        }
        .cash-info .label { color: #666; font-size: 0.72rem; }
        .cash-info .value { font-weight: bold; color: #2e7d32; font-size: 0.95rem; }
        
        /* INFO PENGIRIMAN */
        .info-pengiriman {
            background: #fff3e0;
            border-left: 4px solid #f57c00;
            padding: 10px 12px;
            border-radius: 6px;
            margin: 8px 0;
            font-size: 0.8rem;
        }
        .info-pengiriman .label {
            color: #f57c00;
            font-weight: 600;
            font-size: 0.72rem;
            text-transform: uppercase;
            margin-bottom: 3px;
            display: block;
        }
        .info-pengiriman .value {
            color: #333;
            font-weight: 500;
        }
        
        @media print {
            html, body { background: white !important; height: auto !important; }
            .struk-wrapper { min-height: auto; padding: 0; }
            .struk-card { box-shadow: none; max-height: none; max-width: 100%; border-radius: 0; }
            .no-print { display: none !important; }
            .struk-body { overflow: visible !important; }
        }
    </style>
</head>
<body>

<div class="struk-wrapper">
    <div class="struk-card">
        <div class="struk-header">
            <h5>🧾 STRUK TRANSAKSI</h5>
            <small>🌸 GlowSkin Skincare</small>
        </div>

        <div class="struk-body">
            <?php if ($trx['metode_bayar'] == 'Cash'): ?>
                <div class="alert alert-success py-1 px-2 small mb-2">
                    ✅ <b>Pembayaran Tunai Diterima</b>
                </div>
            <?php else: ?>
                <div class="alert alert-success py-1 px-2 small mb-2">
                    ✅ <b>Pembayaran Berhasil</b> via <?= $trx['metode_bayar']; ?>
                </div>
            <?php endif; ?>

            <div class="struk-info">
                <p><b>Kode:</b> <?= $trx['kode_transaksi']; ?></p>
                <p><b>Pelanggan:</b> <?= htmlspecialchars($trx['nama_pelanggan']); ?></p>
                <p><b>Sales:</b> <?= htmlspecialchars($trx['nama_lengkap']); ?></p>
                <p><b>Metode:</b> <span class="badge bg-secondary"><?= $trx['metode_bayar']; ?></span></p>
                <p><b>Tanggal:</b> <?= date('d/m/Y H:i', strtotime($trx['tanggal'])); ?></p>
            </div>

            <!-- INFO PENGIRIMAN (ALAMAT & NO HP) -->
            <?php if ($alamat_pelanggan != '-' || $no_hp_pelanggan != '-'): ?>
            <div class="info-pengiriman">
                <span class="label">📦 Info Pengiriman</span>
                <?php if ($no_hp_pelanggan != '-'): ?>
                    <div class="value">📞 <?= htmlspecialchars($no_hp_pelanggan); ?></div>
                <?php endif; ?>
                <?php if ($alamat_pelanggan != '-'): ?>
                    <div class="value">📍 <?= htmlspecialchars($alamat_pelanggan); ?></div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <div class="struk-line"></div>

            <table class="table table-struk">
                <thead>
                    <tr>
                        <th>Barang</th>
                        <th class="text-center">Qty</th>
                        <th class="text-end">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($d = mysqli_fetch_assoc($detail)): ?>
                    <tr>
                        <td><?= htmlspecialchars($d['nama_barang']); ?></td>
                        <td class="text-center"><?= $d['jumlah']; ?></td>
                        <td class="text-end"><?= rupiah($d['subtotal']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <div class="struk-line"></div>

            <div class="struk-total">
                <small>TOTAL BAYAR</small>
                <h5><?= rupiah($trx['total_bayar']); ?></h5>
            </div>

            <?php if ($trx['metode_bayar'] == 'Cash'): ?>
            <div class="cash-info">
                <div>
                    <div class="label">Uang Diterima</div>
                    <div class="value"><?= rupiah($trx['uang_diterima']); ?></div>
                </div>
                <div class="text-end">
                    <div class="label">Kembalian</div>
                    <div class="value" style="color: #2e7d32;"><?= rupiah($trx['kembalian']); ?></div>
                </div>
            </div>
            <?php endif; ?>

            <div class="struk-line"></div>

            <div class="text-center mb-2">
                <small class="text-muted" style="font-size: 0.75rem;">Terima kasih telah berbelanja di GlowSkin! 🌸</small>
            </div>

            <div class="d-flex gap-2 no-print">
                <button onclick="window.print()" class="btn btn-outline-danger w-50 btn-print">🖨️ Cetak</button>
                <a href="../komisi/index.php" class="btn btn-danger w-50 btn-print">💰 Lihat Komisi</a>
            </div>
            <a href="kasir.php" class="btn btn-secondary w-100 mt-2 btn-print no-print">🛒 Transaksi Baru Lagi</a>
        </div>
    </div>
</div>

</body>
</html>