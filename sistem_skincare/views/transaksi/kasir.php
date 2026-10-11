<?php
require_once '../../config/koneksi.php';
require_once '../../config/helper.php';
cek_login();
cek_role('sales');

$query_barang = mysqli_query($koneksi, "SELECT * FROM barang WHERE stok > 0 ORDER BY nama_barang ASC");
$barang_list = [];
while ($row = mysqli_fetch_assoc($query_barang)) { $barang_list[] = $row; }

$prefill = null;
$prefill_detail = [];

if (isset($_GET['pesanan']) && isset($_SESSION['prefill_pesanan'])) {
    $prefill = $_SESSION['prefill_pesanan'];
    
    $q_detail = mysqli_query($koneksi, "SELECT pd.*, b.id_barang, b.nama_barang 
                                        FROM pesanan_detail pd 
                                        JOIN barang b ON pd.id_barang = b.id_barang 
                                        WHERE pd.id_pesanan = '{$prefill['id_pesanan']}'");
    while ($d = mysqli_fetch_assoc($q_detail)) {
        $prefill_detail[$d['id_barang']] = $d['jumlah'];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kasir - GlowSkin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .kasir-card { border: none; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); background: white; overflow: hidden; }
        .kasir-header { background: linear-gradient(135deg, #ec407a, #d81b60); color: white; padding: 18px 25px; display: flex; justify-content: space-between; align-items: center; }
        .kasir-header h4 { margin: 0; font-weight: 600; font-size: 1.1rem; }
        .kasir-header .badge { background: rgba(255,255,255,0.2); padding: 6px 12px; font-size: 0.75rem; }
        .kasir-body { padding: 22px 25px; }
        
        .form-label { font-size: 0.8rem; font-weight: 600; color: #444; margin-bottom: 5px; }
        .form-control-sm, .form-select-sm { font-size: 0.85rem; padding: 8px 12px; border-radius: 8px; }
        .form-control-sm:focus, .form-select-sm:focus { border-color: #d81b60; box-shadow: 0 0 0 0.15rem rgba(216,27,96,0.15); }
        
        .tabel-wrapper { 
            max-height: 340px; 
            overflow-y: auto;
            border-radius: 10px; 
            border: 1px solid #f0f0f0;
            scrollbar-gutter: stable;
        }
        .tabel-barang { margin: 0; table-layout: fixed; width: 100%; }
        .tabel-barang thead { position: sticky; top: 0; z-index: 10; }
        .tabel-barang thead th { 
            background: #fce4ec; color: #880e4f; font-size: 0.78rem; font-weight: 600; 
            padding: 10px 12px; border: none; white-space: nowrap;
        }
        .tabel-barang tbody td { 
            padding: 8px 12px; font-size: 0.82rem; vertical-align: middle; 
            border-bottom: 1px solid #f5f5f5;
        }
        .tabel-barang tbody tr:hover { background: #fff5f8; }
        
        .input-qty-wrap {
            display: inline-flex; align-items: center; justify-content: center;
            width: 80px; height: 32px;
            border: 1.5px solid #ddd; border-radius: 8px;
            overflow: hidden; background: white;
            transition: border-color 0.2s;
        }
        .input-qty-wrap:focus-within {
            border-color: #d81b60;
            box-shadow: 0 0 0 3px rgba(216,27,96,0.1);
        }
        .input-qty-btn {
            width: 24px; height: 100%;
            border: none; background: #fce4ec; color: #d81b60;
            font-weight: bold; font-size: 0.9rem; cursor: pointer; padding: 0;
            line-height: 1; display: flex; align-items: center; justify-content: center;
            user-select: none;
        }
        .input-qty-btn:hover { background: #f8bbd0; color: #ad1457; }
        .input-qty-btn:active { background: #f48fb1; }
        .input-qty-field {
            flex: 1; width: 100%; border: none; text-align: center;
            font-size: 0.82rem; font-weight: 600; color: #333;
            background: transparent; padding: 0; outline: none;
            -moz-appearance: textfield;
        }
        .input-qty-field::-webkit-outer-spin-button,
        .input-qty-field::-webkit-inner-spin-button {
            -webkit-appearance: none; margin: 0;
        }
        
        .col-barang { width: 35%; }
        .col-harga { width: 18%; }
        .col-stok { width: 12%; text-align: center; }
        .col-jumlah { width: 18%; text-align: center; }
        .col-subtotal { width: 17%; text-align: right; }
        
        .total-box { background: linear-gradient(135deg, #ec407a, #d81b60); color: white; padding: 15px 22px; border-radius: 12px; display: flex; justify-content: space-between; align-items: center; margin-top: 15px; }
        .total-box .label { font-size: 0.75rem; opacity: 0.9; }
        .total-box h4 { margin: 0; font-weight: 700; font-size: 1.4rem; }
        
        .cash-box { background: #fff9c4; padding: 15px; border-radius: 12px; border: 2px dashed #fbc02d; margin-top: 12px; }
        .cash-box label { font-size: 0.8rem; font-weight: 600; color: #f57f17; }
        .kembalian-box { background: linear-gradient(135deg, #c8e6c9, #a5d6a7); padding: 12px 18px; border-radius: 10px; display: flex; justify-content: space-between; align-items: center; }
        .kembalian-box small { font-size: 0.75rem; color: #1b5e20; font-weight: 600; }
        .kembalian-box h5 { margin: 0; color: #1b5e20; font-weight: 700; font-size: 1.1rem; }
        
        .btn-simpan { background: linear-gradient(135deg, #ec407a, #d81b60); color: white; border: none; border-radius: 10px; padding: 12px; font-weight: 600; font-size: 0.95rem; width: 100%; margin-top: 15px; }
        .btn-simpan:hover { background: linear-gradient(135deg, #d81b60, #ad1457); color: white; }
        
        .badge-stok { background: #e0e0e0; color: #555; padding: 3px 8px; border-radius: 6px; font-size: 0.72rem; }
        
        /* INPUT UANG DENGAN PREFIX "Rp" */
        .input-rp-wrap {
            display: flex;
            align-items: center;
            border: 1.5px solid #e0e0e0;
            border-radius: 8px;
            overflow: hidden;
            background: white;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .input-rp-wrap:focus-within {
            border-color: #d81b60;
            box-shadow: 0 0 0 0.15rem rgba(216,27,96,0.15);
        }
        .input-rp-prefix {
            background: #fce4ec;
            color: #d81b60;
            font-weight: 700;
            font-size: 0.85rem;
            padding: 8px 12px;
            border-right: 1.5px solid #f8bbd0;
            user-select: none;
            white-space: nowrap;
        }
        .input-rp-field {
            flex: 1;
            border: none;
            padding: 8px 12px;
            font-size: 0.85rem;
            outline: none;
            background: transparent;
            font-weight: 600;
            color: #333;
        }
    </style>
</head>
<body>

<?php include '../partials/sidebar.php'; ?>

<div class="main-content">
    <div class="container">
        <div class="kasir-card">
            
            <div class="kasir-header">
                <h4>🛒 Kasir - Input Transaksi</h4>
                <span class="badge">👤 <?= $_SESSION['nama_lengkap']; ?> | 💎 Komisi <?= $_SESSION['komisi_persen']; ?>%</span>
            </div>

            <div class="kasir-body">
                <?php if ($prefill): ?>
                    <div class="alert alert-info py-2 small mb-3" style="font-size: 0.82rem;">
                        📋 <b>Pesanan dari katalog:</b> <?= htmlspecialchars($prefill['nama_pemesan']); ?> 
                        (<?= htmlspecialchars($prefill['no_hp']); ?>)
                        <br>💰 Sudah dibayar via <b><?= htmlspecialchars($prefill['metode_bayar'] ?? 'QRIS'); ?></b>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['pesan'])): ?>
                    <div class="alert alert-warning py-2 small mb-3"><?= $_SESSION['pesan']; unset($_SESSION['pesan']); ?></div>
                <?php endif; ?>

                <form action="../../controllers/TransaksiController.php?aksi=simpan" method="POST" onsubmit="return validasiForm()">
                    
                    <div class="row mb-3">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">👤 Nama Pelanggan</label>
                            <input type="text" name="nama_pelanggan" class="form-control form-control-sm" 
                                   value="<?= $prefill ? htmlspecialchars($prefill['nama_pemesan']) : ''; ?>" 
                                   placeholder="Contoh: Rina" required>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">💳 Metode Pembayaran</label>
                            <?php if ($prefill): ?>
                                <input type="text" class="form-control form-control-sm" 
                                       value="<?= htmlspecialchars($prefill['metode_bayar'] ?? 'QRIS'); ?> (Sudah Dibayar)" 
                                       readonly style="background: #e8f5e9; color: #2e7d32; font-weight: 600;">
                                <input type="hidden" name="metode_bayar" value="<?= htmlspecialchars($prefill['metode_bayar'] ?? 'QRIS'); ?>">
                                <input type="hidden" name="dari_pesanan" value="1">
                            <?php else: ?>
                                <select name="metode_bayar" id="metodeBayar" class="form-select form-select-sm" required onchange="toggleCash()">
                                    <option value="Cash">💵 Cash</option>
                                    <option value="Transfer Bank">🏦 Transfer Bank</option>
                                    <option value="E-Wallet">📱 E-Wallet</option>
                                    <option value="QRIS">📷 QRIS</option>
                                </select>
                            <?php endif; ?>
                        </div>
                    </div>

                    <label class="form-label mb-2">📦 Pilih Barang:</label>
                    <div class="tabel-wrapper">
                        <table class="table tabel-barang align-middle">
                            <thead>
                                <tr>
                                    <th class="col-barang">Barang</th>
                                    <th class="col-harga">Harga</th>
                                    <th class="col-stok">Stok</th>
                                    <th class="col-jumlah">Jumlah</th>
                                    <th class="col-subtotal">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($barang_list as $b): ?>
                                <?php 
                                    $qty_prefill = 0;
                                    if ($prefill && isset($prefill_detail[$b['id_barang']])) {
                                        $qty_prefill = $prefill_detail[$b['id_barang']];
                                    }
                                ?>
                                <tr>
                                    <td>
                                        <input type="hidden" name="id_barang[]" value="<?= $b['id_barang']; ?>">
                                        <b style="color: #333;"><?= htmlspecialchars($b['nama_barang']); ?></b>
                                    </td>
                                    <td class="harga" data-harga="<?= $b['harga']; ?>" style="color: #d81b60; font-weight: 600;">
                                        <?= rupiah($b['harga']); ?>
                                    </td>
                                    <td class="text-center"><span class="badge-stok"><?= $b['stok']; ?></span></td>
                                    <td class="text-center">
                                        <div class="input-qty-wrap">
                                            <button type="button" class="input-qty-btn btn-minus" data-target="qty-<?= $b['id_barang']; ?>">−</button>
                                            <input type="number" 
                                                   id="qty-<?= $b['id_barang']; ?>"
                                                   name="jumlah[]" 
                                                   class="input-qty-field jumlah" 
                                                   min="0" 
                                                   max="<?= $b['stok']; ?>" 
                                                   value="<?= $qty_prefill; ?>" 
                                                   inputmode="numeric" 
                                                   autocomplete="off">
                                            <button type="button" class="input-qty-btn btn-plus" data-target="qty-<?= $b['id_barang']; ?>">+</button>
                                        </div>
                                    </td>
                                    <td class="subtotal text-end" style="color: #d81b60; font-weight: 600;">Rp 0</td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="total-box">
                        <div>
                            <div class="label">TOTAL BAYAR</div>
                            <h4 id="totalBayar">Rp 0</h4>
                        </div>
                        <div style="font-size: 2.2rem; opacity: 0.5;">💰</div>
                    </div>

                    <?php if (!$prefill): ?>
                    <div class="cash-box" id="cashBox">
                        <div class="row align-items-end g-2">
                            <div class="col-md-6">
                                <label>💵 Uang Diterima</label>
                                <!-- INPUT DENGAN PREFIX "Rp" -->
                                <div class="input-rp-wrap">
                                    <span class="input-rp-prefix">Rp</span>
                                    <input type="text" 
                                           id="uangDiterima" 
                                           class="input-rp-field" 
                                           placeholder="0" 
                                           oninput="formatUang(this)"
                                           inputmode="numeric" 
                                           autocomplete="off">
                                </div>
                                <!-- Hidden input untuk kirim angka asli ke server -->
                                <input type="hidden" name="uang_diterima" id="uangDiterimaRaw" value="0">
                            </div>
                            <div class="col-md-6">
                                <div class="kembalian-box">
                                    <small>Kembalian</small>
                                    <h5 id="kembalian">Rp 0</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if ($prefill): ?>
                    <input type="hidden" name="id_pesanan" value="<?= $prefill['id_pesanan']; ?>">
                    <?php endif; ?>

                    <button type="submit" class="btn-simpan">💾 Simpan Transaksi</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let totalBayar = 0;

function hitungTotal() {
    totalBayar = 0;
    document.querySelectorAll('.tabel-barang tbody tr').forEach(function(row) {
        let hargaEl = row.querySelector('.harga');
        let jumlahEl = row.querySelector('.jumlah');
        if (!hargaEl || !jumlahEl) return;
        
        let harga = parseInt(hargaEl.dataset.harga);
        let jumlah = parseInt(jumlahEl.value) || 0;
        let subtotal = harga * jumlah;
        row.querySelector('.subtotal').innerText = 'Rp ' + subtotal.toLocaleString('id-ID');
        totalBayar += subtotal;
    });
    document.getElementById('totalBayar').innerText = 'Rp ' + totalBayar.toLocaleString('id-ID');
    hitungKembalian();
}

/* FORMAT UANG OTOMATIS DENGAN TITIK */
function formatUang(input) {
    let angka = input.value.replace(/\D/g, '');
    
    // Simpan angka asli ke hidden input
    let rawInput = document.getElementById('uangDiterimaRaw');
    if (rawInput) rawInput.value = angka || 0;
    
    // Format dengan titik ribuan
    if (angka) {
        input.value = parseInt(angka).toLocaleString('id-ID');
    } else {
        input.value = '';
    }
    
    hitungKembalian();
}

function hitungKembalian() {
    let rawInput = document.getElementById('uangDiterimaRaw');
    if (!rawInput) return;
    
    let uangDiterima = parseInt(rawInput.value) || 0;
    let kembalian = uangDiterima - totalBayar;
    let el = document.getElementById('kembalian');
    
    if (kembalian < 0) {
        el.innerText = '-Rp ' + Math.abs(kembalian).toLocaleString('id-ID');
        el.style.color = '#c62828';
    } else {
        el.innerText = 'Rp ' + kembalian.toLocaleString('id-ID');
        el.style.color = '#1b5e20';
    }
}

function toggleCash() {
    let selectEl = document.getElementById('metodeBayar');
    if (!selectEl) return;
    let metode = selectEl.value;
    let cashBox = document.getElementById('cashBox');
    let inputUang = document.getElementById('uangDiterima');
    let rawInput = document.getElementById('uangDiterimaRaw');
    
    if (metode === 'Cash') {
        cashBox.style.display = 'block';
        inputUang.required = true;
    } else {
        cashBox.style.display = 'none';
        inputUang.required = false;
        inputUang.value = '';
        if (rawInput) rawInput.value = 0;
        document.getElementById('kembalian').innerText = 'Rp 0';
    }
}

function validasiForm() {
    let selectEl = document.getElementById('metodeBayar');
    if (totalBayar === 0) {
        alert('Pilih minimal 1 barang!');
        return false;
    }
    if (selectEl && selectEl.value === 'Cash') {
        let rawInput = document.getElementById('uangDiterimaRaw');
        let uangDiterima = parseInt(rawInput.value) || 0;
        if (uangDiterima < totalBayar) {
            alert('Uang diterima kurang dari total bayar!');
            return false;
        }
    }
    return true;
}

document.querySelectorAll('.jumlah').forEach(function(input) {
    input.addEventListener('input', hitungTotal);
});

document.querySelectorAll('.btn-plus').forEach(function(btn) {
    btn.addEventListener('click', function() {
        let targetId = this.dataset.target;
        let input = document.getElementById(targetId);
        let max = parseInt(input.max) || 999;
        let val = parseInt(input.value) || 0;
        if (val < max) {
            input.value = val + 1;
            hitungTotal();
        }
    });
});

document.querySelectorAll('.btn-minus').forEach(function(btn) {
    btn.addEventListener('click', function() {
        let targetId = this.dataset.target;
        let input = document.getElementById(targetId);
        let val = parseInt(input.value) || 0;
        if (val > 0) {
            input.value = val - 1;
            hitungTotal();
        }
    });
});

window.addEventListener('load', function() {
    hitungTotal();
    toggleCash();
});
</script>
</body>
</html>