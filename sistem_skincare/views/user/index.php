<?php
require_once '../../config/koneksi.php';
require_once '../../config/helper.php';
cek_login();
cek_role('admin');

$result = mysqli_query($koneksi, "SELECT * FROM users ORDER BY role ASC, id_user DESC");

// Statistik
$total_admin = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM users WHERE role = 'admin'"))['total'];
$total_sales = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM users WHERE role = 'sales'"))['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola User - GlowSkin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .page-card { border: none; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); padding: 25px; background: white; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .page-header h4 { margin: 0; color: #d81b60; font-weight: bold; }
        .avatar { width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #ec407a, #d81b60); color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; }
    </style>
</head>
<body>

<?php include '../partials/sidebar.php'; ?>

<div class="main-content">
    <div class="page-card">
        <div class="page-header">
            <h4>👥 Kelola User</h4>
            <a href="tambah.php" class="btn btn-danger btn-sm">+ Tambah User</a>
        </div>

        <?php if (isset($_SESSION['pesan'])): ?>
            <div class="alert alert-success"><?= $_SESSION['pesan']; unset($_SESSION['pesan']); ?></div>
        <?php endif; ?>

        <!-- Statistik -->
        <div class="row mb-4">
            <div class="col-md-6 mb-3">
                <div class="stat-mini d-flex justify-content-between align-items-center" 
                     style="background: linear-gradient(135deg, #ab47bc, #8e24aa); color: white; padding: 15px 20px; border-radius: 12px;">
                    <div>
                        <h6 style="margin: 0; opacity: 0.9; font-size: 0.8rem;">Total Admin</h6>
                        <h3 style="margin: 0; font-weight: bold;"><?= $total_admin; ?></h3>
                    </div>
                    <div style="font-size: 2rem; opacity: 0.4;">👑</div>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="stat-mini d-flex justify-content-between align-items-center" 
                     style="background: linear-gradient(135deg, #26a69a, #00897b); color: white; padding: 15px 20px; border-radius: 12px;">
                    <div>
                        <h6 style="margin: 0; opacity: 0.9; font-size: 0.8rem;">Total Sales</h6>
                        <h3 style="margin: 0; font-weight: bold;"><?= $total_sales; ?></h3>
                    </div>
                    <div style="font-size: 2rem; opacity: 0.4;">🛒</div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>User</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Komisi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar"><?= strtoupper(substr($row['nama_lengkap'], 0, 1)); ?></div>
                                <div>
                                    <b><?= htmlspecialchars($row['nama_lengkap']); ?></b>
                                    <?php if ($row['id_user'] == $_SESSION['id_user']): ?>
                                        <span class="badge bg-info text-dark" style="font-size: 0.65rem;">Anda</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($row['username']); ?></td>
                        <td>
                            <?php if ($row['role'] == 'admin'): ?>
                                <span class="badge bg-primary">👑 Admin</span>
                            <?php else: ?>
                                <span class="badge bg-success">🛒 Sales</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($row['role'] == 'sales'): ?>
                                <b><?= $row['komisi_persen']; ?>%</b>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="edit.php?id=<?= $row['id_user']; ?>" class="btn btn-warning btn-sm">✏️ Edit</a>
                            <?php if ($row['id_user'] != $_SESSION['id_user']): ?>
                            <a href="../../controllers/UserController.php?aksi=hapus&id=<?= $row['id_user']; ?>" 
                               class="btn btn-danger btn-sm" 
                               onclick="return confirm('Yakin hapus user ini?')">🗑️ Hapus</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>