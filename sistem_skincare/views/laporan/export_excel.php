<?php
require_once '../../config/koneksi.php';
require_once '../../config/helper.php';
cek_login();
cek_role('admin');

// Ambil filter sales dari URL
$filter_sales = isset($_GET['sales']) ? $_GET['sales'] : 'semua';

// Bikin WHERE clause
$where = "";
$nama_sales = "Semua Sales";
if ($filter_sales != 'semua') {
    $where = "WHERE t.id_user = '$filter_sales'";
    
    $q_nama = mysqli_query($koneksi, "SELECT nama_lengkap FROM users WHERE id_user = '$filter_sales'");
    if ($d = mysqli_fetch_assoc($q_nama)) {
        $nama_sales = $d['nama_lengkap'];
    }
}

// Set header supaya browser download sebagai file Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Penjualan_GlowSkin_" . date('Ymd') . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

$query = "SELECT t.*, u.nama_lengkap 
          FROM transaksi t 
          JOIN users u ON t.id_user = u.id_user 
          $where
          ORDER BY t.tanggal DESC";
$result = mysqli_query($koneksi, $query);
?>
<table border="1">
    <thead>
        <tr>
            <th colspan="7" style="font-size: 16px; font-weight: bold; text-align: center;">
                LAPORAN PENJUALAN GLOWSKIN
            </th>
        </tr>
        <tr>
            <th colspan="7" style="text-align: center; background-color: #fce4ec;">
                <?= $filter_sales != 'semua' ? 'Sales: ' . $nama_sales : 'Semua Sales'; ?>
            </th>
        </tr>
        <tr>
            <th colspan="7" style="text-align: center;">
                Tanggal Cetak: <?= date('d/m/Y H:i'); ?>
            </th>
        </tr>
        <tr style="background-color: #d81b60; color: white;">
            <th>No</th>
            <th>Kode Transaksi</th>
            <th>Tanggal</th>
            <th>Sales</th>
            <th>Pelanggan</th>
            <th>Metode</th>
            <th>Total Bayar</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $no = 1; 
        $total = 0;
        while ($row = mysqli_fetch_assoc($result)): 
            $total += $row['total_bayar'];
        ?>
        <tr>
            <td><?= $no++; ?></td>
            <td><?= $row['kode_transaksi']; ?></td>
            <td><?= date('d/m/Y H:i', strtotime($row['tanggal'])); ?></td>
            <td><?= $row['nama_lengkap']; ?></td>
            <td><?= $row['nama_pelanggan']; ?></td>
            <td><?= $row['metode_bayar']; ?></td>
            <td><?= $row['total_bayar']; ?></td>
        </tr>
        <?php endwhile; ?>
        <?php if (mysqli_num_rows($result) == 0): ?>
        <tr>
            <td colspan="7" style="text-align: center; color: #999;">
                Belum ada transaksi.
            </td>
        </tr>
        <?php endif; ?>
    </tbody>
    <tfoot>
        <tr style="background-color: #fce4ec; font-weight: bold;">
            <td colspan="6" style="text-align: right;">GRAND TOTAL</td>
            <td><?= $total; ?></td>
        </tr>
    </tfoot>
</table>