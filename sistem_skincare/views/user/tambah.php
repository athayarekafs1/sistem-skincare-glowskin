<?php
require_once '../../config/koneksi.php';
require_once '../../config/helper.php';
cek_login();
cek_role('admin');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah User - GlowSkin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .page-card { border: none; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); padding: 30px; background: white; max-width: 700px; margin: auto; }
        .page-card h4 { color: #d81b60; font-weight: bold; margin-bottom: 25px; }
    </style>
</head>
<body>

<?php include '../partials/sidebar.php'; ?>

<div class="main-content">
    <div class="page-card">
        <h4>➕ Tambah User Baru</h4>
        <form action="../../controllers/UserController.php?aksi=tambah" method="POST">
            <div class="mb-3">
                <label>Nama Lengkap</label>
                <input type="text" name="nama_lengkap" class="form-control" required>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Password</label>
                    <input type="text" name="password" class="form-control" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Role</label>
                    <select name="role" class="form-control" id="roleSelect" required>
                        <option value="sales">🛒 Sales</option>
                        <option value="admin">👑 Admin</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3" id="komisiField">
                    <label>Komisi (%)</label>
                    <input type="number" name="komisi_persen" class="form-control" value="5" step="0.01" min="0" max="100">
                    <small class="text-muted">Hanya untuk sales.</small>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="index.php" class="btn btn-secondary w-50">Batal</a>
                <button type="submit" class="btn btn-danger w-50">Simpan User</button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('roleSelect').addEventListener('change', function() {
    document.getElementById('komisiField').style.display = this.value === 'admin' ? 'none' : 'block';
});
</script>
</body>
</html>