<?php
require_once '../config/koneksi.php';
require_once '../config/helper.php';
cek_login();
cek_role('admin');

$aksi = isset($_GET['aksi']) ? $_GET['aksi'] : '';

// ==========================================
// PROSES BAYAR KOMISI (Pending → Dibayar)
// ==========================================
if ($aksi == 'bayar') {
    $id_komisi = $_GET['id'];

    // Cek komisi ada & statusnya Pending
    $cek = mysqli_query($koneksi, "SELECT * FROM komisi WHERE id_komisi = '$id_komisi'");
    $data = mysqli_fetch_assoc($cek);

    if (!$data) {
        redirect('../views/komisi/index.php', 'Komisi tidak ditemukan!');
    }

    if ($data['status'] == 'Dibayar') {
        redirect('../views/komisi/index.php', 'Komisi ini sudah dibayar sebelumnya!');
    }

    // Update status jadi Dibayar
    $query = "UPDATE komisi SET status = 'Dibayar' WHERE id_komisi = '$id_komisi'";
    
    if (mysqli_query($koneksi, $query)) {
        redirect('../views/komisi/index.php', 'Komisi berhasil ditandai sebagai Dibayar!');
    } else {
        redirect('../views/komisi/index.php', 'Gagal update status: ' . mysqli_error($koneksi));
    }
}
?>