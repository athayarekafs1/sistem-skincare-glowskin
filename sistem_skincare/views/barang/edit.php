<?php
require_once '../../config/koneksi.php';
require_once '../../config/helper.php';
cek_login();
cek_role('admin');

$id = $_GET['id'];
$query = mysqli_query($koneksi, "SELECT * FROM barang WHERE id_barang = '$id'");
$barang = mysqli_fetch_assoc($query);

if (!$barang) { header("Location: index.php"); exit; }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Barang - GlowSkin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .page-card { border: none; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); padding: 30px; background: white; max-width: 700px; margin: auto; }
        .page-card h4 { color: #d81b60; font-weight: bold; margin-bottom: 25px; }
        .preview-img { width: 100px; height: 100px; object-fit: cover; border-radius: 10px; margin-bottom: 10px; }
    </style>
</head>
<body>

<?php include '../partials/sidebar.php'; ?>

<div class="main-content">
    <div class="page-card">
        <h4>✏️ Edit Produk Skincare</h4>
        <form action="../../controllers/BarangController.php?aksi=edit" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id_barang" value="<?= $barang['id_barang']; ?>">
            <input type="hidden" name="gambar_lama" value="<?= $barang['gambar']; ?>">

            <div class="mb-3">
                <label>Nama Barang</label>
                <input type="text" name="nama_barang" class="form-control" value="<?= htmlspecialchars($barang['nama_barang']); ?>" required>
            </div>
            <div class="mb-3">
                <label>Kategori</label>
                <input type="text" name="kategori" class="form-control" value="<?= htmlspecialchars($barang['kategori']); ?>" required>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Harga (Rp)</label>
                    <input type="number" name="harga" class="form-control" value="<?= $barang['harga']; ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Stok</label>
                    <input type="number" name="stok" class="form-control" value="<?= $barang['stok']; ?>" required>
                </div>
            </div>
            <div class="mb-3">
                <label>Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="3"><?= htmlspecialchars($barang['deskripsi']); ?></textarea>
            </div>
            <div class="mb-3">
                <label>Foto Produk</label><br>
                <?php if ($barang['gambar'] && file_exists('../../public/uploads/' . $barang['gambar'])): ?>
                    <img src="/sistem_skincare/public/uploads/<?= $barang['gambar']; ?>" class="preview-img">
                <?php else: ?>
                    <p class="text-muted small">Belum ada foto.</p>
                <?php endif; ?>
                <input type="file" name="gambar" class="form-control" accept="image/*">
                <small class="text-muted">Kosongkan jika tidak ingin ganti foto.</small>
            </div>
            <div class="d-flex gap-2">
                <a href="index.php" class="btn btn-secondary w-50">Batal</a>
                <button type="submit" class="btn btn-danger w-50">Update Barang</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>