<?php
require_once '../config/koneksi.php';
require_once '../config/helper.php';
cek_login();
cek_role('sales');

$aksi = isset($_GET['aksi']) ? $_GET['aksi'] : '';

if ($aksi == 'simpan') {
    $id_user        = $_SESSION['id_user'];
    $nama_pelanggan = mysqli_real_escape_string($koneksi, $_POST['nama_pelanggan']);
    $metode_bayar   = mysqli_real_escape_string($koneksi, $_POST['metode_bayar']);
    $komisi_persen  = $_SESSION['komisi_persen'];
    
    $uang_diterima  = isset($_POST['uang_diterima']) ? (float) $_POST['uang_diterima'] : 0;
    $dari_pesanan   = isset($_POST['dari_pesanan']) && $_POST['dari_pesanan'] == '1';

    $id_barang_arr = $_POST['id_barang'];
    $jumlah_arr    = $_POST['jumlah'];

    // Hitung total
    $total_bayar = 0;
    $items = [];
    foreach ($id_barang_arr as $index => $id_barang) {
        $jumlah = (int) $jumlah_arr[$index];
        if ($jumlah <= 0) continue;

        $q = mysqli_query($koneksi, "SELECT harga, stok FROM barang WHERE id_barang = '$id_barang'");
        $b = mysqli_fetch_assoc($q);

        if ($jumlah > $b['stok']) {
            redirect('../views/transaksi/kasir.php', 'Stok tidak cukup untuk salah satu barang!');
        }

        $subtotal = $b['harga'] * $jumlah;
        $total_bayar += $subtotal;

        $items[] = [
            'id_barang' => $id_barang,
            'jumlah'    => $jumlah,
            'subtotal'  => $subtotal
        ];
    }

    if (empty($items)) {
        redirect('../views/transaksi/kasir.php', 'Pilih minimal 1 barang!');
    }

    // Validasi pembayaran
    $kembalian = 0;
    
    if ($dari_pesanan) {
        // Dari pesanan katalog = sudah dibayar
        $uang_diterima = $total_bayar;
        $kembalian = 0;
    } elseif ($metode_bayar == 'Cash') {
        // Transaksi langsung dengan cash
        if ($uang_diterima < $total_bayar) {
            redirect('../views/transaksi/kasir.php', 'Uang diterima kurang dari total bayar!');
        }
        $kembalian = $uang_diterima - $total_bayar;
    } else {
        // Transaksi langsung non-cash
        $uang_diterima = $total_bayar;
        $kembalian = 0;
    }

    $kode = 'TRX-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));

    // Simpan transaksi
    $query_trx = "INSERT INTO transaksi (kode_transaksi, id_user, nama_pelanggan, metode_bayar, total_bayar, uang_diterima, kembalian) 
                  VALUES ('$kode', '$id_user', '$nama_pelanggan', '$metode_bayar', '$total_bayar', '$uang_diterima', '$kembalian')";
    mysqli_query($koneksi, $query_trx);
    $id_transaksi = mysqli_insert_id($koneksi);

    foreach ($items as $item) {
        mysqli_query($koneksi, "INSERT INTO detail_transaksi (id_transaksi, id_barang, jumlah, subtotal) 
                                VALUES ('$id_transaksi', '{$item['id_barang']}', '{$item['jumlah']}', '{$item['subtotal']}')");
        mysqli_query($koneksi, "UPDATE barang SET stok = stok - {$item['jumlah']} WHERE id_barang = '{$item['id_barang']}'");
    }

    // Komisi
    $jumlah_komisi = $total_bayar * ($komisi_persen / 100);
    mysqli_query($koneksi, "INSERT INTO komisi (id_transaksi, id_user, jumlah_komisi) 
                            VALUES ('$id_transaksi', '$id_user', '$jumlah_komisi')");

    // Update status pesanan kalau dari katalog
    if (isset($_POST['id_pesanan']) && !empty($_POST['id_pesanan'])) {
        $id_pesanan = $_POST['id_pesanan'];
        mysqli_query($koneksi, "UPDATE pesanan SET status = 'Selesai' WHERE id_pesanan = '$id_pesanan'");
        unset($_SESSION['prefill_pesanan']);
    }

    // Redirect
    if ($dari_pesanan) {
        // Dari pesanan = langsung struk (udah bayar)
        redirect('../views/transaksi/struk.php?id=' . $id_transaksi, 'Transaksi berhasil! Komisi: ' . rupiah($jumlah_komisi));
    } elseif ($metode_bayar == 'Cash') {
        // Cash = langsung struk
        redirect('../views/transaksi/struk.php?id=' . $id_transaksi, 'Transaksi berhasil! Kembalian: ' . rupiah($kembalian));
    } else {
        // Non-cash = lewat simulasi pembayaran
        redirect('../views/transaksi/pembayaran.php?id=' . $id_transaksi, 'Transaksi berhasil! Komisi: ' . rupiah($jumlah_komisi));
    }
}
?>