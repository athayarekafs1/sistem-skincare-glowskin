<?php
require_once '../config/koneksi.php';
require_once '../config/helper.php';

$id_barang = $_GET['id'];

if (isset($_SESSION['keranjang'][$id_barang])) {
    unset($_SESSION['keranjang'][$id_barang]);
}

header("Location: keranjang.php");
exit;
?>