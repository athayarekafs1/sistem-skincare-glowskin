<?php
require_once '../config/koneksi.php';
require_once '../config/helper.php';
cek_login();
cek_role('admin');

$aksi = isset($_GET['aksi']) ? $_GET['aksi'] : '';

// ==========================================
// PROSES TAMBAH USER
// ==========================================
if ($aksi == 'tambah') {
    $nama     = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];
    $role     = $_POST['role'];
    $komisi   = ($role == 'admin') ? 0 : $_POST['komisi_persen'];

    // Cek username sudah dipakai belum
    $cek = mysqli_query($koneksi, "SELECT * FROM users WHERE username = '$username'");
    if (mysqli_num_rows($cek) > 0) {
        redirect('../views/user/tambah.php', 'Username sudah dipakai!');
    }

    $query = "INSERT INTO users (nama_lengkap, username, password, role, komisi_persen) 
              VALUES ('$nama', '$username', '$password', '$role', '$komisi')";

    if (mysqli_query($koneksi, $query)) {
        redirect('../views/user/index.php', 'User berhasil ditambahkan!');
    } else {
        redirect('../views/user/tambah.php', 'Gagal simpan: ' . mysqli_error($koneksi));
    }
}

// ==========================================
// PROSES EDIT USER
// ==========================================
if ($aksi == 'edit') {
    $id       = $_POST['id_user'];
    $nama     = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];
    $role     = $_POST['role'];
    $komisi   = ($role == 'admin') ? 0 : $_POST['komisi_persen'];

    // Cek username dipakai user lain
    $cek = mysqli_query($koneksi, "SELECT * FROM users WHERE username = '$username' AND id_user != '$id'");
    if (mysqli_num_rows($cek) > 0) {
        redirect('../views/user/edit.php?id=' . $id, 'Username sudah dipakai user lain!');
    }

    // Kalau password diisi, update. Kalau kosong, biarkan.
    if (!empty($password)) {
        $query = "UPDATE users SET nama_lengkap='$nama', username='$username', password='$password', role='$role', komisi_persen='$komisi' WHERE id_user='$id'";
    } else {
        $query = "UPDATE users SET nama_lengkap='$nama', username='$username', role='$role', komisi_persen='$komisi' WHERE id_user='$id'";
    }

    if (mysqli_query($koneksi, $query)) {
        redirect('../views/user/index.php', 'User berhasil diupdate!');
    } else {
        redirect('../views/user/edit.php?id=' . $id, 'Gagal update: ' . mysqli_error($koneksi));
    }
}

// ==========================================
// PROSES HAPUS USER
// ==========================================
if ($aksi == 'hapus') {
    $id = $_GET['id'];

    // Jangan biarkan hapus diri sendiri
    if ($id == $_SESSION['id_user']) {
        redirect('../views/user/index.php', 'Tidak bisa hapus akun sendiri!');
    }

    mysqli_query($koneksi, "DELETE FROM users WHERE id_user = '$id'");
    redirect('../views/user/index.php', 'User berhasil dihapus!');
}
?>