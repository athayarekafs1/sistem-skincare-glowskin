<?php
require_once '../config/koneksi.php';
require_once '../config/helper.php';
cek_login();

$role = $_SESSION['role'];
$nama = $_SESSION['nama_lengkap'];
$id_user = $_SESSION['id_user'];

// Statistik umum
$total_barang    = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM barang"))['total'];
$total_transaksi = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM transaksi"))['total'];
$total_penjualan = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT SUM(total_bayar) as total FROM transaksi"))['total'] ?? 0;
$total_pesanan   = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM pesanan WHERE status = 'Baru'"))['total'];

// Statistik khusus sales
if ($role == 'sales') {
    $total_komisi_saya = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT SUM(jumlah_komisi) as total FROM komisi WHERE id_user = '$id_user'"))['total'] ?? 0;
    $transaksi_saya    = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM transaksi WHERE id_user = '$id_user'"))['total'];
}

// Grafik penjualan 7 hari
$data_penjualan = [];
$label_hari = [];
for ($i = 6; $i >= 0; $i--) {
    $tanggal = date('Y-m-d', strtotime("-$i days"));
    $label_hari[] = date('d M', strtotime($tanggal));
    $q = mysqli_query($koneksi, "SELECT SUM(total_bayar) as total FROM transaksi WHERE DATE(tanggal) = '$tanggal'");
    $hasil = mysqli_fetch_assoc($q)['total'] ?? 0;
    $data_penjualan[] = (int) $hasil;
}

// Grafik komisi per sales
$q_komisi = mysqli_query($koneksi, "SELECT u.nama_lengkap, SUM(k.jumlah_komisi) as total 
                                     FROM komisi k 
                                     JOIN users u ON k.id_user = u.id_user 
                                     GROUP BY k.id_user");
$label_sales = [];
$data_komisi = [];
while ($row = mysqli_fetch_assoc($q_komisi)) {
    $label_sales[] = $row['nama_lengkap'];
    $data_komisi[] = (int) $row['total'];
}

// Top 5 produk
$q_top = mysqli_query($koneksi, "SELECT b.nama_barang, SUM(d.jumlah) as total_terjual 
                                  FROM detail_transaksi d 
                                  JOIN barang b ON d.id_barang = b.id_barang 
                                  GROUP BY d.id_barang 
                                  ORDER BY total_terjual DESC LIMIT 5");
$top_produk = [];
while ($row = mysqli_fetch_assoc($q_top)) { $top_produk[] = $row; }

// 5 transaksi terbaru
$q_recent = mysqli_query($koneksi, "SELECT t.*, u.nama_lengkap FROM transaksi t 
                                     JOIN users u ON t.id_user = u.id_user 
                                     ORDER BY t.tanggal DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - GlowSkin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .stat-card { color: white; border-radius: 15px; padding: 20px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); position: relative; overflow: hidden; }
        .stat-card h2 { font-size: 1.6rem; font-weight: bold; margin: 5px 0 0; }
        .stat-card p { margin: 0; opacity: 0.9; font-size: 0.85rem; }
        .stat-card .icon { position: absolute; right: 15px; top: 15px; font-size: 2.5rem; opacity: 0.3; }
        .chart-card { border: none; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); padding: 20px; background: white; height: 100%; }
        .chart-card h6 { color: #d81b60; font-weight: bold; margin-bottom: 15px; }
        .table-sm td, .table-sm th { padding: 8px 10px; font-size: 0.85rem; }
        .badge-method { font-size: 0.7rem; }
        
        /* ========================================== */
        /* RESPONSIVE HP */
        /* ========================================== */
        @media (max-width: 768px) {
            /* Tabel bisa scroll horizontal */
            .table-responsive {
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch;
                margin: 0 -15px;
                padding: 0 15px;
            }
            .table {
                font-size: 0.7rem !important;
                min-width: 500px !important;
            }
            .table th, .table td {
                padding: 6px 8px !important;
                white-space: nowrap;
            }
            
            /* Stat card lebih kecil */
            .stat-card { padding: 12px !important; margin-bottom: 10px; }
            .stat-card h2 { font-size: 1rem !important; }
            .stat-card p { font-size: 0.7rem !important; }
            .stat-card .icon { font-size: 1.5rem !important; }
            
            /* Chart */
            canvas { max-height: 220px !important; }
            .chart-card { padding: 12px !important; margin-bottom: 12px; }
            .chart-card h6 { font-size: 0.85rem !important; }
            
            /* Judul */
            h3 { font-size: 1.1rem !important; }
            h4 { font-size: 1rem !important; }
        }
        
        /* HP KECIL */
        @media (max-width: 480px) {
            .stat-card h2 { font-size: 0.9rem !important; }
            .chart-card { padding: 10px !important; }
        }
    </style>
</head>
<body>

<?php include 'partials/sidebar.php'; ?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="mb-1">Selamat Datang, <?= $nama; ?>! 👋</h3>
            <p class="text-muted mb-0">Berikut ringkasan bisnis GlowSkin hari ini</p>
        </div>
        <div class="text-end">
            <small class="text-muted"><?= date('l, d F Y'); ?></small>
        </div>
    </div>

    <!-- KARTU STATISTIK -->
    <div class="row mb-4">
        <?php if ($role == 'admin'): ?>
            <div class="col-md-3 mb-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #ec407a, #d81b60);">
                    <div class="icon">💰</div>
                    <p>Total Penjualan</p>
                    <h2><?= rupiah($total_penjualan); ?></h2>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #ab47bc, #8e24aa);">
                    <div class="icon">🛒</div>
                    <p>Total Transaksi</p>
                    <h2><?= $total_transaksi; ?></h2>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #26a69a, #00897b);">
                    <div class="icon">📦</div>
                    <p>Jenis Produk</p>
                    <h2><?= $total_barang; ?></h2>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #ff7043, #e64a19);">
                    <div class="icon">📋</div>
                    <p>Pesanan Baru</p>
                    <h2><?= $total_pesanan; ?></h2>
                </div>
            </div>
        <?php else: ?>
            <div class="col-md-4 mb-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #ec407a, #d81b60);">
                    <div class="icon">💰</div>
                    <p>Total Komisi Saya</p>
                    <h2><?= rupiah($total_komisi_saya); ?></h2>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #ab47bc, #8e24aa);">
                    <div class="icon">🛒</div>
                    <p>Transaksi Saya</p>
                    <h2><?= $transaksi_saya; ?></h2>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #26a69a, #00897b);">
                    <div class="icon">📊</div>
                    <p>Persentase Komisi</p>
                    <h2><?= $_SESSION['komisi_persen']; ?>%</h2>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($total_pesanan > 0 && $role == 'admin'): ?>
    <div class="alert alert-warning d-flex justify-content-between align-items-center">
        <div>📋 Ada <b><?= $total_pesanan; ?></b> pesanan baru menunggu diproses!</div>
        <a href="pesanan/index.php" class="btn btn-sm btn-warning">Lihat Pesanan</a>
    </div>
    <?php endif; ?>

    <!-- GRAFIK -->
    <div class="row mb-4">
        <div class="col-md-8 mb-3">
            <div class="chart-card">
                <h6>📈 Grafik Penjualan 7 Hari Terakhir</h6>
                <canvas id="chartPenjualan" height="90"></canvas>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="chart-card">
                <h6>💎 Komisi per Sales</h6>
                <canvas id="chartKomisi" height="180"></canvas>
            </div>
        </div>
    </div>

    <!-- TABEL -->
    <div class="row">
        <!-- Top 5 Produk -->
        <div class="col-md-6 mb-3">
            <div class="chart-card">
                <h6>🏆 Top 5 Produk Terlaris</h6>
                <div class="table-responsive">
                    <?php if (empty($top_produk)): ?>
                        <p class="text-muted small">Belum ada data penjualan.</p>
                    <?php else: ?>
                    <table class="table table-sm table-hover">
                        <thead class="table-light">
                            <tr><th>#</th><th>Produk</th><th class="text-end">Terjual</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($top_produk as $i => $p): ?>
                            <tr>
                                <td><?= $i + 1; ?></td>
                                <td><?= htmlspecialchars($p['nama_barang']); ?></td>
                                <td class="text-end"><b><?= $p['total_terjual']; ?> pcs</b></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Transaksi Terbaru -->
        <div class="col-md-6 mb-3">
            <div class="chart-card">
                <h6>🕐 5 Transaksi Terbaru</h6>
                <div class="table-responsive">
                    <?php if (mysqli_num_rows($q_recent) == 0): ?>
                        <p class="text-muted small">Belum ada transaksi.</p>
                    <?php else: ?>
                    <table class="table table-sm table-hover">
                        <thead class="table-light">
                            <tr><th>Kode</th><th>Sales</th><th>Metode</th><th class="text-end">Total</th></tr>
                        </thead>
                        <tbody>
                            <?php while ($r = mysqli_fetch_assoc($q_recent)): ?>
                            <tr>
                                <td><small><?= $r['kode_transaksi']; ?></small></td>
                                <td><small><?= $r['nama_lengkap']; ?></small></td>
                                <td><span class="badge bg-secondary badge-method"><?= $r['metode_bayar']; ?></span></td>
                                <td class="text-end"><b><?= rupiah($r['total_bayar']); ?></b></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
// Grafik Penjualan (Batang)
const ctxPenjualan = document.getElementById('chartPenjualan').getContext('2d');
new Chart(ctxPenjualan, {
    type: 'bar',
    data: {
        labels: <?= json_encode($label_hari); ?>,
        datasets: [{
            label: 'Penjualan (Rp)',
            data: <?= json_encode($data_penjualan); ?>,
            backgroundColor: 'rgba(216, 27, 96, 0.7)',
            borderColor: '#d81b60',
            borderWidth: 2,
            borderRadius: 8
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: { legend: { display: false } },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { callback: function(value) { return 'Rp ' + value.toLocaleString('id-ID'); } }
            }
        }
    }
});

// Grafik Komisi (Donat)
const ctxKomisi = document.getElementById('chartKomisi').getContext('2d');
new Chart(ctxKomisi, {
    type: 'doughnut',
    data: {
        labels: <?= json_encode($label_sales); ?>,
        datasets: [{
            data: <?= json_encode($data_komisi); ?>,
            backgroundColor: ['#ec407a', '#ab47bc', '#26a69a', '#ff7043', '#42a5f5', '#ffca28']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: { legend: { position: 'bottom', labels: { font: { size: 11 } } } }
    }
});
</script>

</body>
</html>