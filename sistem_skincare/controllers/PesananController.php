<?php
require_once '../config/koneksi.php';
require_once '../config/helper.php';
cek_login();

$aksi = isset($_GET['aksi']) ? $_GET['aksi'] : '';

// ==========================================
// PROSES PESANAN → JADI TRANSAKSI (Sales)
// ==========================================
if ($aksi == 'proses') {
    cek_role('sales');

    $id_pesanan = $_GET['id'];
    $q = mysqli_query($koneksi, "SELECT * FROM pesanan WHERE id_pesanan = '$id_pesanan'");
    $pesanan = mysqli_fetch_assoc($q);

    if (!$pesanan) {
        redirect('../views/pesanan/index.php', 'Pesanan tidak ditemukan!');
    }

    if ($pesanan['status'] != 'Dibayar') {
        redirect('../views/pesanan/index.php', 'Pesanan belum dibayar atau sudah diproses!');
    }

    // Simpan data pesanan ke session (buat prefill di kasir)
    $_SESSION['prefill_pesanan'] = $pesanan;

    // Redirect ke kasir dengan parameter pesanan
    redirect('../views/transaksi/kasir.php?pesanan=' . $id_pesanan, 'Silakan konfirmasi transaksi.');
}

// ==========================================
// TANDAI PESANAN SELESAI
// ==========================================
if ($aksi == 'selesai') {
    cek_login();
    
    $id_pesanan = $_GET['id'];
    mysqli_query($koneksi, "UPDATE pesanan SET status = 'Selesai' WHERE id_pesanan = '$id_pesanan'");
    redirect('../views/pesanan/index.php', 'Pesanan ditandai selesai!');
}

// ==========================================
// BATALKAN PESANAN
// ==========================================
if ($aksi == 'batal') {
    cek_login();
    
    $id_pesanan = $_GET['id'];
    mysqli_query($koneksi, "UPDATE pesanan SET status = 'Batal' WHERE id_pesanan = '$id_pesanan'");
    redirect('../views/pesanan/index.php', 'Pesanan dibatalkan.');
}
?>