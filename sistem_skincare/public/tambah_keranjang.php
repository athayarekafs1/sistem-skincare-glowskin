<?php
require_once '../config/koneksi.php';
require_once '../config/helper.php';

$id_barang = $_GET['id'];
$redirect  = isset($_GET['redirect']) ? $_GET['redirect'] : 'katalog';

// Cek barang ada & stok > 0
$cek = mysqli_query($koneksi, "SELECT * FROM barang WHERE id_barang = '$id_barang' AND stok > 0");
if (mysqli_num_rows($cek) == 0) {
    header("Location: katalog.php");
    exit;
}

// Inisialisasi keranjang
if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}

// Tambah produk ke keranjang
if (isset($_SESSION['keranjang'][$id_barang])) {
    $_SESSION['keranjang'][$id_barang]++;
} else {
    $_SESSION['keranjang'][$id_barang] = 1;
}

// Redirect sesuai parameter
if ($redirect == 'checkout') {
    // PESAN LANGSUNG → langsung ke halaman checkout
    header("Location: checkout.php");
} else {
    // + KERANJANG → balik ke katalog (biar bisa nambah produk)
    header("Location: katalog.php?added=1");
}
exit;
?>