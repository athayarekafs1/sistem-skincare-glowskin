<?php
require_once '../../config/koneksi.php';
require_once '../../config/helper.php';
cek_login();

$id_user = $_SESSION['id_user'];
$role    = $_SESSION['role'];

// Ambil filter dari URL
$filter_status = isset($_GET['filter']) ? $_GET['filter'] : 'semua';
$filter_sales  = isset($_GET['sales']) ? $_GET['sales'] : 'semua';

// Bikin WHERE clause
$where = [];

// Kalau sales, cuma lihat komisi sendiri
if ($role == 'sales') {
    $where[] = "k.id_user = '$id_user'";
}

// Filter status
if ($filter_status == 'pending') {
    $where[] = "k.status = 'Pending'";
} elseif ($filter_status == 'dibayar') {
    $where[] = "k.status = 'Dibayar'";
}

// Filter sales (khusus admin)
if ($role == 'admin' && $filter_sales != 'semua') {
    $where[] = "k.id_user = '$filter_sales'";
}

$where_sql = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

$query = "SELECT k.*, u.nama_lengkap, t.kode_transaksi 
          FROM komisi k 
          JOIN users u ON k.id_user = u.id_user 
          JOIN transaksi t ON k.id_transaksi = t.id_transaksi 
          $where_sql
          ORDER BY k.id_komisi DESC";
$result = mysqli_query($koneksi, $query);

$total_komisi = 0;
$total_pending = 0;
$total_dibayar = 0;
$data_komisi = [];
while ($row = mysqli_fetch_assoc($result)) {
    $total_komisi += $row['jumlah_komisi'];
    if ($row['status'] == 'Pending') $total_pending += $row['jumlah_komisi'];
    else $total_dibayar += $row['jumlah_komisi'];
    $data_komisi[] = $row;
}

// Ambil daftar sales (buat dropdown filter - khusus admin)
$sales_list = [];
if ($role == 'admin') {
    $q_sales = mysqli_query($koneksi, "SELECT id_user, nama_lengkap FROM users WHERE role = 'sales' ORDER BY nama_lengkap ASC");
    while ($s = mysqli_fetch_assoc($q_sales)) {
        $sales_list[] = $s;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Komisi - GlowSkin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .page-card { border: none; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); padding: 25px; background: white; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px; }
        .page-header h4 { margin: 0; color: #d81b60; font-weight: bold; }
        .total-card { color: white; padding: 20px 25px; border-radius: 12px; display: flex; justify-content: space-between; align-items: center; height: 100%; }
        .total-card h6 { margin: 0; opacity: 0.9; font-size: 0.8rem; }
        .total-card h3 { margin: 5px 0 0; font-weight: bold; font-size: 1.4rem; }
        
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
            font-size: 0.78rem;
            font-weight: 600;
            color: #666;
            margin: 0;
        }
        .filter-bar select, .filter-bar .btn-group {
            font-size: 0.82rem;
        }
        .filter-bar select {
            padding: 5px 10px;
            border-radius: 8px;
            border: 1.5px solid #e0e0e0;
            background: white;
        }
        .filter-bar select:focus {
            border-color: #d81b60;
            outline: none;
        }
        .btn-filter {
            font-size: 0.75rem;
            padding: 5px 12px;
            border-radius: 8px;
            font-weight: 600;
        }
    </style>
</head>
<body>

<?php include '../partials/sidebar.php'; ?>

<div class="main-content">
    <div class="page-card">
        <div class="page-header">
            <h4>💰 Riwayat Komisi <?= $role == 'sales' ? 'Saya' : 'Semua Sales'; ?></h4>
        </div>

        <?php if (isset($_SESSION['pesan'])): ?>
            <div class="alert alert-success py-2"><?= $_SESSION['pesan']; unset($_SESSION['pesan']); ?></div>
        <?php endif; ?>

        <!-- FILTER BAR -->
        <div class="filter-bar">
            <!-- Filter Status -->
            <label>Status:</label>
            <div class="btn-group">
                <a href="?filter=semua&sales=<?= $filter_sales; ?>" 
                   class="btn <?= $filter_status == 'semua' ? 'btn-danger' : 'btn-outline-danger'; ?> btn-filter">Semua</a>
                <a href="?filter=pending&sales=<?= $filter_sales; ?>" 
                   class="btn <?= $filter_status == 'pending' ? 'btn-warning' : 'btn-outline-warning'; ?> btn-filter">⏳ Pending</a>
                <a href="?filter=dibayar&sales=<?= $filter_sales; ?>" 
                   class="btn <?= $filter_status == 'dibayar' ? 'btn-success' : 'btn-outline-success'; ?> btn-filter">✅ Dibayar</a>
            </div>

            <?php if ($role == 'admin'): ?>
            <!-- Filter Sales (khusus admin) -->
            <label class="ms-2">Sales:</label>
            <form method="GET" style="display: inline-flex; gap: 5px; margin: 0;">
                <input type="hidden" name="filter" value="<?= $filter_status; ?>">
                <select name="sales" onchange="this.form.submit()">
                    <option value="semua" <?= $filter_sales == 'semua' ? 'selected' : ''; ?>>Semua Sales</option>
                    <?php foreach ($sales_list as $s): ?>
                        <option value="<?= $s['id_user']; ?>" <?= $filter_sales == $s['id_user'] ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($s['nama_lengkap']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
            <?php endif; ?>
        </div>

        <!-- KARTU STATISTIK -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="total-card" style="background: linear-gradient(135deg, #ec407a, #d81b60);">
                    <div>
                        <h6>Total Komisi</h6>
                        <h3><?= rupiah($total_komisi); ?></h3>
                    </div>
                    <div style="font-size: 2.5rem; opacity: 0.4;">💎</div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="total-card" style="background: linear-gradient(135deg, #ffb300, #ff8f00);">
                    <div>
                        <h6>Komisi Pending</h6>
                        <h3><?= rupiah($total_pending); ?></h3>
                    </div>
                    <div style="font-size: 2.5rem; opacity: 0.4;">⏳</div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="total-card" style="background: linear-gradient(135deg, #66bb6a, #43a047);">
                    <div>
                        <h6>Komisi Dibayar</h6>
                        <h3><?= rupiah($total_dibayar); ?></h3>
                    </div>
                    <div style="font-size: 2.5rem; opacity: 0.4;">✅</div>
                </div>
            </div>
        </div>

        <!-- TABEL -->
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Kode Transaksi</th>
                        <?php if ($role == 'admin'): ?><th>Sales</th><?php endif; ?>
                        <th class="text-end">Jumlah Komisi</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <?php if ($role == 'admin'): ?><th>Aksi</th><?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($data_komisi as $row): ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= $row['kode_transaksi']; ?></td>
                        <?php if ($role == 'admin'): ?>
                            <td><?= $row['nama_lengkap']; ?></td>
                        <?php endif; ?>
                        <td class="text-end"><b><?= rupiah($row['jumlah_komisi']); ?></b></td>
                        <td>
                            <?php if ($row['status'] == 'Pending'): ?>
                                <span class="badge bg-warning text-dark">⏳ Pending</span>
                            <?php else: ?>
                                <span class="badge bg-success">✅ Dibayar</span>
                            <?php endif; ?>
                        </td>
                        <td><?= date('d/m/Y', strtotime($row['tanggal_komisi'])); ?></td>
                        <?php if ($role == 'admin'): ?>
                        <td>
                            <?php if ($row['status'] == 'Pending'): ?>
                                <a href="../../controllers/KomisiController.php?aksi=bayar&id=<?= $row['id_komisi']; ?>" 
                                   class="btn btn-success btn-sm"
                                   onclick="return confirm('Tandai komisi ini sudah dibayar?')">
                                   💵 Bayar
                                </a>
                            <?php else: ?>
                                <span class="text-muted small">-</span>
                            <?php endif; ?>
                        </td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($data_komisi)): ?>
                    <tr>
                        <td colspan="<?= $role == 'admin' ? 7 : 5; ?>" class="text-center text-muted py-4">
                            Belum ada komisi.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>