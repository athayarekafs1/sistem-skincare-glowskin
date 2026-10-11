<?php
require_once '../config/koneksi.php';
require_once '../config/helper.php';

$nama    = mysqli_real_escape_string($koneksi, $_POST['nama']);
$no_hp   = mysqli_real_escape_string($koneksi, $_POST['no_hp']);
$alamat  = mysqli_real_escape_string($koneksi, $_POST['alamat']);
$catatan = mysqli_real_escape_string($koneksi, $_POST['catatan'] ?? '');
$metode  = mysqli_real_escape_string($koneksi, $_POST['metode_bayar']);

$id_barang_arr = $_POST['id_barang'];
$jumlah_arr    = $_POST['jumlah'];

if (empty($id_barang_arr)) {
    header("Location: katalog.php");
    exit;
}

$total = 0;
$items = [];
foreach ($id_barang_arr as $index => $id_barang) {
    $jumlah = (int) $jumlah_arr[$index];
    if ($jumlah <= 0) continue;

    $q = mysqli_query($koneksi, "SELECT * FROM barang WHERE id_barang = '$id_barang'");
    $b = mysqli_fetch_assoc($q);
    
    if ($b) {
        $subtotal = $b['harga'] * $jumlah;
        $total += $subtotal;
        $items[] = [
            'id_barang' => $b['id_barang'],
            'jumlah' => $jumlah,
            'subtotal' => $subtotal
        ];
    }
}

if (empty($items)) {
    header("Location: katalog.php");
    exit;
}

$id_barang_utama = $items[0]['id_barang'];
$jumlah_utama = $items[0]['jumlah'];

$query = "INSERT INTO pesanan (id_barang, nama_pemesan, no_hp, alamat, jumlah, total_bayar, metode_bayar, catatan, status) 
          VALUES ('$id_barang_utama', '$nama', '$no_hp', '$alamat', '$jumlah_utama', '$total', '$metode', '$catatan', 'Baru')";
mysqli_query($koneksi, $query);
$id_pesanan = mysqli_insert_id($koneksi);

foreach ($items as $item) {
    mysqli_query($koneksi, "INSERT INTO pesanan_detail (id_pesanan, id_barang, jumlah, subtotal) 
                            VALUES ('$id_pesanan', '{$item['id_barang']}', '{$item['jumlah']}', '{$item['subtotal']}')");
}

unset($_SESSION['keranjang']);

header("Location: pembayaran_pesanan.php?id=" . $id_pesanan);
exit;
?>