<?php
require_once '../../config/koneksi.php';
require_once '../../config/helper.php';
cek_login();

$role = $_SESSION['role'];

$query = "SELECT p.*, 
          GROUP_CONCAT(CONCAT(b.nama_barang, ' (', pd.jumlah, 'x)') SEPARATOR ', ') as detail_produk
          FROM pesanan p 
          LEFT JOIN pesanan_detail pd ON p.id_pesanan = pd.id_pesanan 
          LEFT JOIN barang b ON pd.id_barang = b.id_barang 
          GROUP BY p.id_pesanan
          ORDER BY 
            CASE p.status 
                WHEN 'Dibayar' THEN 1
                WHEN 'Baru' THEN 2
                WHEN 'Selesai' THEN 3
                WHEN 'Batal' THEN 4
            END ASC,
            p.tanggal DESC";
$result = mysqli_query($koneksi, $query);

$total_baru     = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM pesanan WHERE status = 'Baru'"))['total'];
$total_dibayar  = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM pesanan WHERE status = 'Dibayar'"))['total'];
$total_selesai  = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM pesanan WHERE status = 'Selesai'"))['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pesanan Masuk - GlowSkin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .page-card { 
            border: none; 
            border-radius: 15px; 
            box-shadow: 0 5px 15px rgba(0,0,0,0.05); 
            padding: 25px; 
            background: white; 
        }
        
        .page-header { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-bottom: 22px; 
        }
        .page-header h4 { 
            margin: 0; 
            color: #d81b60; 
            font-weight: bold; 
            font-size: 1.2rem;
        }
        
        /* STATISTIK */
        .stat-mini { 
            color: white; 
            padding: 18px 22px; 
            border-radius: 12px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        .stat-mini h6 { 
            margin: 0; 
            opacity: 0.95; 
            font-size: 0.78rem; 
            font-weight: 500;
        }
        .stat-mini h3 { 
            margin: 4px 0 0; 
            font-weight: bold; 
            font-size: 1.6rem; 
        }
        .stat-mini .icon { font-size: 2.2rem; opacity: 0.4; }
        
        /* TABEL */
        .table-pesanan {
            margin: 0;
            font-size: 0.83rem;
        }
        .table-pesanan thead th {
            background: #fce4ec;
            color: #880e4f;
            font-size: 0.73rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            padding: 11px 12px;
            border: none;
            white-space: nowrap;
        }
        .table-pesanan tbody td {
            padding: 11px 12px;
            vertical-align: middle;
            border-bottom: 1px solid #f5f5f5;
            font-size: 0.8rem;
        }
        .table-pesanan tbody tr:hover { background: #fff9fb; }
        .table-pesanan tbody tr:last-child td { border-bottom: none; }
        
        .kolom-pelanggan b { 
            color: #333; 
            font-size: 0.83rem; 
            display: block;
        }
        .kolom-pelanggan small { 
            color: #999; 
            font-size: 0.7rem; 
        }
        
        .kolom-produk { 
            font-size: 0.78rem; 
            color: #555;
            line-height: 1.5;
            max-width: 220px;
        }
        
        .badge-status {
            padding: 5px 10px;
            border-radius: 8px;
            font-size: 0.7rem;
            font-weight: 600;
            white-space: nowrap;
        }
    </style>
</head>
<body>

<?php include '../partials/sidebar.php'; ?>

<div class="main-content">
    <div class="page-card">
        <div class="page-header">
            <h4>📋 Daftar Pesanan Masuk</h4>
            <span class="badge bg-secondary" style="font-size: 0.8rem; padding: 8px 15px;">
                Total: <?= mysqli_num_rows($result); ?> pesanan
            </span>
        </div>

        <?php if (isset($_SESSION['pesan'])): ?>
            <div class="alert alert-success py-2"><?= $_SESSION['pesan']; unset($_SESSION['pesan']); ?></div>
        <?php endif; ?>

        <!-- STATISTIK (3 KARTU) -->
        <div class="row mb-4 g-3">
            <div class="col-md-4">
                <div class="stat-mini" style="background: linear-gradient(135deg, #78909c, #546e7a);">
                    <div>
                        <h6>Belum Bayar</h6>
                        <h3><?= $total_baru; ?></h3>
                    </div>
                    <div class="icon">⏳</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-mini" style="background: linear-gradient(135deg, #ef5350, #c62828);">
                    <div>
                        <h6>Dibayar</h6>
                        <h3><?= $total_dibayar; ?></h3>
                    </div>
                    <div class="icon">💰</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-mini" style="background: linear-gradient(135deg, #66bb6a, #43a047);">
                    <div>
                        <h6>Selesai</h6>
                        <h3><?= $total_selesai; ?></h3>
                    </div>
                    <div class="icon">✅</div>
                </div>
            </div>
        </div>

        <!-- TABEL -->
        <div class="table-responsive">
            <table class="table table-pesanan align-middle">
                <thead>
                    <tr>
                        <th style="width: 40px;">No</th>
                        <th style="width: 100px;">Tanggal</th>
                        <th style="width: 150px;">Pelanggan</th>
                        <th style="width: 120px;">No. HP</th>
                        <th>Produk Dipesan</th>
                        <th style="width: 110px;" class="text-end">Total</th>
                        <th style="width: 120px;">Status</th>
                        <th style="width: 100px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><small><?= date('d/m/y H:i', strtotime($row['tanggal'])); ?></small></td>
                        <td class="kolom-pelanggan">
                            <b><?= htmlspecialchars($row['nama_pemesan']); ?></b>
                            <small><?= htmlspecialchars(substr($row['alamat'], 0, 25)); ?>...</small>
                        </td>
                        <td><small><?= htmlspecialchars($row['no_hp']); ?></small></td>
                        <td class="kolom-produk"><?= $row['detail_produk'] ?? '<span class="text-muted">-</span>'; ?></td>
                        <td class="text-end"><b><?= rupiah($row['total_bayar']); ?></b></td>
                        <td>
                            <?php if ($row['status'] == 'Baru'): ?>
                                <span class="badge-status" style="background: #e0e0e0; color: #555;">⏳ Belum Bayar</span>
                            <?php elseif ($row['status'] == 'Dibayar'): ?>
                                <span class="badge-status" style="background: #ffcdd2; color: #c62828;">💰 Dibayar</span>
                            <?php elseif ($row['status'] == 'Selesai'): ?>
                                <span class="badge-status" style="background: #c8e6c9; color: #2e7d32;">✅ Selesai</span>
                            <?php else: ?>
                                <span class="badge-status" style="background: #e0e0e0; color: #555;">❌ Batal</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php if ($row['status'] == 'Dibayar' && $role == 'sales'): ?>
                                <a href="../../controllers/PesananController.php?aksi=proses&id=<?= $row['id_pesanan']; ?>" 
                                   class="btn btn-success btn-sm"
                                   style="font-size: 0.72rem; padding: 5px 10px;"
                                   onclick="return confirm('Proses pesanan ini?')">➡️ Proses</a>
                            <?php elseif ($row['status'] == 'Dibayar' && $role == 'admin'): ?>
                                <small class="text-muted">Menunggu sales</small>
                            <?php elseif ($row['status'] == 'Baru'): ?>
                                <small class="text-muted">Belum bayar</small>
                            <?php elseif ($row['status'] == 'Selesai'): ?>
                                <span class="text-success small">✓ Selesai</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <?php if (mysqli_num_rows($result) == 0): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">Belum ada pesanan masuk.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>