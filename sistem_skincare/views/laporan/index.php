<?php
require_once '../../config/koneksi.php';
require_once '../../config/helper.php';
cek_login();
cek_role('admin');

// Ambil filter sales dari URL
$filter_sales = isset($_GET['sales']) ? $_GET['sales'] : 'semua';

// Bikin WHERE clause
$where = "";
if ($filter_sales != 'semua') {
    $where = "WHERE t.id_user = '$filter_sales'";
}

// Query transaksi
$query = "SELECT t.*, u.nama_lengkap 
          FROM transaksi t 
          JOIN users u ON t.id_user = u.id_user 
          $where
          ORDER BY t.tanggal DESC";
$result = mysqli_query($koneksi, $query);

$total_semua = 0;
$data_laporan = [];
while ($row = mysqli_fetch_assoc($result)) {
    $total_semua += $row['total_bayar'];
    $data_laporan[] = $row;
}

$total_transaksi = count($data_laporan);

// Total produk terjual (sesuai filter)
if ($filter_sales != 'semua') {
    $total_produk_terjual = mysqli_fetch_assoc(mysqli_query($koneksi, 
        "SELECT SUM(d.jumlah) as total FROM detail_transaksi d 
         JOIN transaksi t ON d.id_transaksi = t.id_transaksi 
         WHERE t.id_user = '$filter_sales'"))['total'] ?? 0;
} else {
    $total_produk_terjual = mysqli_fetch_assoc(mysqli_query($koneksi, 
        "SELECT SUM(jumlah) as total FROM detail_transaksi"))['total'] ?? 0;
}

// Sales terbaik (kalau filter semua)
if ($filter_sales == 'semua') {
    $sales_terbaik = mysqli_fetch_assoc(mysqli_query($koneksi, 
        "SELECT u.nama_lengkap, SUM(t.total_bayar) as total 
         FROM transaksi t JOIN users u ON t.id_user = u.id_user 
         GROUP BY t.id_user ORDER BY total DESC LIMIT 1"));
} else {
    $sales_terbaik = mysqli_fetch_assoc(mysqli_query($koneksi, 
        "SELECT u.nama_lengkap FROM users u WHERE u.id_user = '$filter_sales'"));
}

// Ambil daftar sales (buat dropdown)
$sales_list = [];
$q_sales = mysqli_query($koneksi, "SELECT id_user, nama_lengkap FROM users WHERE role = 'sales' ORDER BY nama_lengkap ASC");
while ($s = mysqli_fetch_assoc($q_sales)) {
    $sales_list[] = $s;
}

// Nama sales terpilih (buat judul)
$nama_sales_terpilih = '';
foreach ($sales_list as $s) {
    if ($s['id_user'] == $filter_sales) {
        $nama_sales_terpilih = $s['nama_lengkap'];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan - GlowSkin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .page-card { border: none; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); padding: 25px; background: white; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px; }
        .page-header h4 { margin: 0; color: #d81b60; font-weight: bold; }
        .stat-card { color: white; border-radius: 12px; padding: 18px; }
        .stat-card h6 { margin: 0 0 5px 0; opacity: 0.9; font-size: 0.8rem; }
        .stat-card h3 { margin: 0; font-weight: bold; font-size: 1.4rem; }
        
        .filter-bar {
            background: #fafafa;
            border-radius: 10px;
            padding: 12px 15px;
            margin-bottom: 18px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
        }
        .filter-bar label {
            font-size: 0.8rem;
            font-weight: 600;
            color: #666;
            margin: 0;
        }
        .filter-bar select {
            font-size: 0.82rem;
            padding: 6px 12px;
            border-radius: 8px;
            border: 1.5px solid #e0e0e0;
            background: white;
        }
        .filter-bar select:focus {
            border-color: #d81b60;
            outline: none;
        }
        
        .print-only { display: none; }
        
        @media print {
            .no-print, .sidebar { display: none !important; }
            .print-only { display: block !important; }
            .main-content { margin-left: 0 !important; }
            body { background: white !important; }
            .page-card { box-shadow: none !important; padding: 0; }
        }
    </style>
</head>
<body>

<?php include '../partials/sidebar.php'; ?>

<div class="main-content">
    <div class="page-card">

        <!-- TOMBOL AKSI -->
        <div class="page-header no-print">
            <h4>📊 Laporan Penjualan <?= $filter_sales != 'semua' ? '- ' . htmlspecialchars($nama_sales_terpilih) : ''; ?></h4>
            <div>
                <button onclick="window.print()" class="btn btn-danger btn-sm">🖨️ Cetak PDF</button>
                <a href="export_excel.php?sales=<?= $filter_sales; ?>" class="btn btn-success btn-sm">📗 Export Excel</a>
            </div>
        </div>

        <!-- HEADER PRINT -->
        <div class="print-only" style="text-align: center; margin-bottom: 20px;">
            <h3 style="margin: 0; color: #d81b60;">🌸 GlowSkin Skincare</h3>
            <p style="margin: 5px 0;">Laporan Penjualan <?= $filter_sales != 'semua' ? '- ' . htmlspecialchars($nama_sales_terpilih) : ''; ?></p>
            <p style="margin: 0; font-size: 0.85rem; color: #666;">
                Dicetak: <?= date('d/m/Y H:i'); ?>
            </p>
            <hr>
        </div>

        <!-- FILTER BAR -->
        <div class="filter-bar no-print">
            <label>Filter Sales:</label>
            <form method="GET" style="display: inline-flex; gap: 8px; margin: 0;">
                <select name="sales" onchange="this.form.submit()">
                    <option value="semua" <?= $filter_sales == 'semua' ? 'selected' : ''; ?>>Semua Sales</option>
                    <?php foreach ($sales_list as $s): ?>
                        <option value="<?= $s['id_user']; ?>" <?= $filter_sales == $s['id_user'] ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($s['nama_lengkap']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <a href="index.php" class="btn btn-outline-secondary btn-sm" style="font-size: 0.78rem; padding: 6px 12px;">Reset</a>
            </form>
        </div>

        <!-- KARTU STATISTIK -->
        <div class="row mb-4 no-print">
            <div class="col-md-3 mb-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #ec407a, #d81b60);">
                    <h6>Total Penjualan</h6>
                    <h3><?= rupiah($total_semua); ?></h3>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #ab47bc, #8e24aa);">
                    <h6>Total Transaksi</h6>
                    <h3><?= $total_transaksi; ?></h3>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #26a69a, #00897b);">
                    <h6>Produk Terjual</h6>
                    <h3><?= $total_produk_terjual; ?> pcs</h3>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #ff7043, #e64a19);">
                    <h6><?= $filter_sales == 'semua' ? 'Sales Terbaik' : 'Nama Sales'; ?></h6>
                    <h3 style="font-size: 1rem;"><?= $sales_terbaik['nama_lengkap'] ?? '-'; ?></h3>
                </div>
            </div>
        </div>

        <!-- TABEL RINGKASAN PRINT -->
        <div class="print-only" style="margin-bottom: 20px;">
            <table class="table table-bordered" style="width: 100%; font-size: 0.9rem;">
                <tr>
                    <td style="width: 50%; background: #fce4ec;"><b>Total Penjualan</b></td>
                    <td><?= rupiah($total_semua); ?></td>
                </tr>
                <tr>
                    <td style="background: #fce4ec;"><b>Total Transaksi</b></td>
                    <td><?= $total_transaksi; ?> transaksi</td>
                </tr>
                <tr>
                    <td style="background: #fce4ec;"><b>Produk Terjual</b></td>
                    <td><?= $total_produk_terjual; ?> pcs</td>
                </tr>
                <tr>
                    <td style="background: #fce4ec;"><b><?= $filter_sales == 'semua' ? 'Sales Terbaik' : 'Nama Sales'; ?></b></td>
                    <td><?= $sales_terbaik['nama_lengkap'] ?? '-'; ?></td>
                </tr>
            </table>
        </div>

        <!-- TABEL DETAIL -->
        <h6 class="mb-2 no-print">📋 Detail Transaksi:</h6>
        <div class="table-responsive">
            <table class="table table-hover align-middle" style="font-size: 0.85rem;">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Kode Transaksi</th>
                        <th>Tanggal</th>
                        <?php if ($filter_sales == 'semua'): ?><th>Sales</th><?php endif; ?>
                        <th>Pelanggan</th>
                        <th>Metode</th>
                        <th class="text-end">Total Bayar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($data_laporan as $row): ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= $row['kode_transaksi']; ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($row['tanggal'])); ?></td>
                        <?php if ($filter_sales == 'semua'): ?>
                            <td><?= $row['nama_lengkap']; ?></td>
                        <?php endif; ?>
                        <td><?= $row['nama_pelanggan']; ?></td>
                        <td><span class="badge bg-secondary"><?= $row['metode_bayar']; ?></span></td>
                        <td class="text-end"><b><?= rupiah($row['total_bayar']); ?></b></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($data_laporan)): ?>
                    <tr>
                        <td colspan="<?= $filter_sales == 'semua' ? 7 : 6; ?>" class="text-center text-muted py-3">
                            Belum ada transaksi.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr class="table-light">
                        <th colspan="<?= $filter_sales == 'semua' ? 6 : 5; ?>" class="text-end">GRAND TOTAL</th>
                        <th class="text-end"><?= rupiah($total_semua); ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

</body>
</html>