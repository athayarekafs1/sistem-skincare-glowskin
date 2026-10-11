<?php
require_once '../config/koneksi.php';
require_once '../config/helper.php';

$id_barang = $_POST['id_barang'];
$jumlah = (int) $_POST['jumlah'];

if ($jumlah > 0 && isset($_SESSION['keranjang'][$id_barang])) {
    $_SESSION['keranjang'][$id_barang] = $jumlah;
}

header("Location: keranjang.php");
exit;
?>