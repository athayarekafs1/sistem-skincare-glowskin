<?php
require_once '../config/koneksi.php';
require_once '../config/helper.php';
cek_login();
cek_role('admin');

$aksi = isset($_GET['aksi']) ? $_GET['aksi'] : '';

// ==========================================
// PROSES TAMBAH BARANG
// ==========================================
if ($aksi == 'tambah') {
    $nama      = mysqli_real_escape_string($koneksi, $_POST['nama_barang']);
    $kategori  = mysqli_real_escape_string($koneksi, $_POST['kategori']);
    $harga     = $_POST['harga'];
    $stok      = $_POST['stok'];
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);

    $nama_file = $_FILES['gambar']['name'];
    $tmp_file  = $_FILES['gambar']['tmp_name'];
    $ext       = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));
    $nama_baru = uniqid() . '.' . $ext;
    $folder    = '../public/uploads/' . $nama_baru;

    // Validasi ekstensi
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    if (!in_array($ext, $allowed)) {
        redirect('../views/barang/tambah.php', 'Format gambar harus JPG/PNG/WEBP!');
    }

    if (move_uploaded_file($tmp_file, $folder)) {
        $query = "INSERT INTO barang (nama_barang, kategori, harga, stok, gambar, deskripsi) 
                  VALUES ('$nama', '$kategori', '$harga', '$stok', '$nama_baru', '$deskripsi')";
        
        if (mysqli_query($koneksi, $query)) {
            redirect('../views/barang/index.php', 'Barang berhasil ditambahkan!');
        } else {
            redirect('../views/barang/tambah.php', 'Gagal simpan: ' . mysqli_error($koneksi));
        }
    } else {
        redirect('../views/barang/tambah.php', 'Gagal upload gambar. Cek folder public/uploads!');
    }
}

// ==========================================
// PROSES HAPUS BARANG
// ==========================================
if ($aksi == 'hapus') {
    $id = $_GET['id'];
    
    $q = mysqli_query($koneksi, "SELECT gambar FROM barang WHERE id_barang = '$id'");
    $data = mysqli_fetch_assoc($q);
    if ($data['gambar'] && file_exists('../public/uploads/' . $data['gambar'])) {
        unlink('../public/uploads/' . $data['gambar']);
    }

    mysqli_query($koneksi, "DELETE FROM barang WHERE id_barang = '$id'");
    redirect('../views/barang/index.php', 'Barang berhasil dihapus!');
}
// ==========================================
// PROSES EDIT BARANG
// ==========================================
if ($aksi == 'edit') {
    $id         = $_POST['id_barang'];
    $nama       = mysqli_real_escape_string($koneksi, $_POST['nama_barang']);
    $kategori   = mysqli_real_escape_string($koneksi, $_POST['kategori']);
    $harga      = $_POST['harga'];
    $stok       = $_POST['stok'];
    $deskripsi  = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $gambar_lama = $_POST['gambar_lama'];

    $nama_baru = $gambar_lama; // default: pakai gambar lama

    // Kalau ada upload gambar baru
    if (!empty($_FILES['gambar']['name'])) {
        $nama_file = $_FILES['gambar']['name'];
        $tmp_file  = $_FILES['gambar']['tmp_name'];
        $ext       = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));
        $allowed   = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($ext, $allowed)) {
            redirect('../views/barang/edit.php?id=' . $id, 'Format gambar harus JPG/PNG/WEBP!');
        }

        $nama_baru = uniqid() . '.' . $ext;
        $folder    = '../public/uploads/' . $nama_baru;

        if (move_uploaded_file($tmp_file, $folder)) {
            // Hapus gambar lama
            if ($gambar_lama && file_exists('../public/uploads/' . $gambar_lama)) {
                unlink('../public/uploads/' . $gambar_lama);
            }
        } else {
            redirect('../views/barang/edit.php?id=' . $id, 'Gagal upload gambar baru!');
        }
    }

    $query = "UPDATE barang SET 
              nama_barang = '$nama', 
              kategori = '$kategori', 
              harga = '$harga', 
              stok = '$stok', 
              gambar = '$nama_baru', 
              deskripsi = '$deskripsi' 
              WHERE id_barang = '$id'";

    if (mysqli_query($koneksi, $query)) {
        redirect('../views/barang/index.php', 'Barang berhasil diupdate!');
    } else {
        redirect('../views/barang/edit.php?id=' . $id, 'Gagal update: ' . mysqli_error($koneksi));
    }
}
?>