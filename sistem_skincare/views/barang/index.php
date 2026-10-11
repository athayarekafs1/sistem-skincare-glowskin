<?php
require_once '../../config/koneksi.php';
require_once '../../config/helper.php';
cek_login();

$result = mysqli_query($koneksi, "SELECT * FROM barang ORDER BY id_barang DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Barang - GlowSkin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table img { width: 60px; height: 60px; object-fit: cover; border-radius: 8px; }
        .page-card { border: none; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); padding: 25px; background: white; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .page-header h4 { margin: 0; color: #d81b60; font-weight: bold; }
    </style>
</head>
<body>

<?php include '../partials/sidebar.php'; ?>

<div class="main-content">
    <div class="page-card">
        <div class="page-header">
            <h4>📦 Daftar Produk Skincare</h4>
            <div>
                <?php if ($_SESSION['role'] == 'admin'): ?>
                    <a href="tambah.php" class="btn btn-danger btn-sm">+ Tambah Barang</a>
                <?php endif; ?>
            </div>
        </div>

        <?php if (isset($_SESSION['pesan'])): ?>
            <div class="alert alert-success"><?= $_SESSION['pesan']; unset($_SESSION['pesan']); ?></div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Gambar</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <?php if ($_SESSION['role'] == 'admin'): ?>
                        <th>Aksi</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td>
                            <?php if ($row['gambar'] && file_exists('../../public/uploads/' . $row['gambar'])): ?>
                                <img src="/sistem_skincare/public/uploads/<?= $row['gambar']; ?>">
                            <?php else: ?>
                                <span class="text-muted small">No Image</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($row['nama_barang']); ?></td>
                        <td><?= htmlspecialchars($row['kategori']); ?></td>
                        <td><?= rupiah($row['harga']); ?></td>
                        <td>
                            <?php if ($row['stok'] > 10): ?>
                                <span class="badge bg-success"><?= $row['stok']; ?></span>
                            <?php elseif ($row['stok'] > 0): ?>
                                <span class="badge bg-warning text-dark"><?= $row['stok']; ?></span>
                            <?php else: ?>
                                <span class="badge bg-danger">Habis</span>
                            <?php endif; ?>
                        </td>
                        <?php if ($_SESSION['role'] == 'admin'): ?>
                        <td>
                            <a href="edit.php?id=<?= $row['id_barang']; ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="../../controllers/BarangController.php?aksi=hapus&id=<?= $row['id_barang']; ?>" 
                               class="btn btn-danger btn-sm" 
                               onclick="return confirm('Yakin hapus barang ini?')">Hapus</a>
                        </td>
                        <?php endif; ?>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>