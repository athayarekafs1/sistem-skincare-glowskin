<?php
session_start();

// Cek apakah user sudah login
function cek_login() {
    if (!isset($_SESSION['id_user'])) {
        header("Location: /sistem_skincare/views/auth/login.php");
        exit;
    }
}

// Cek role (admin/sales)
function cek_role($role) {
    if ($_SESSION['role'] != $role) {
        header("Location: /sistem_skincare/views/auth/login.php");
        exit;
    }
}

// Format rupiah
function rupiah($angka) {
    return "Rp " . number_format($angka, 0, ',', '.');
}

// Redirect dengan pesan
function redirect($url, $pesan = null) {
    if ($pesan) {
        $_SESSION['pesan'] = $pesan;
    }
    header("Location: " . $url);
    exit;
}
?>