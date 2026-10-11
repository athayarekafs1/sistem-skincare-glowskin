<?php
$current_page = basename($_SERVER['PHP_SELF']);
$current_dir  = basename(dirname($_SERVER['PHP_SELF']));

$jumlah_pesanan_baru = 0;
if (isset($_SESSION['role']) && $_SESSION['role'] == 'sales') {
    require_once __DIR__ . '/../../config/koneksi.php';
    $q_notif = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM pesanan WHERE status = 'Dibayar'");
    $jumlah_pesanan_baru = mysqli_fetch_assoc($q_notif)['total'] ?? 0;
}
$inisial = strtoupper(substr($_SESSION['nama_lengkap'], 0, 1));
?>

<!-- SIDEBAR (DESKTOP ONLY) -->
<div class="sidebar">
    <div class="sidebar-brand">
        <h4>🌸 GlowSkin</h4>
        <small>Skincare Sales System</small>
    </div>
    <div class="sidebar-user">
        <div class="user-avatar"><?= $inisial; ?></div>
        <div class="user-info">
            <div class="user-name"><?= $_SESSION['nama_lengkap']; ?></div>
            <div class="user-role"><?= ucfirst($_SESSION['role']); ?></div>
        </div>
    </div>
    <ul class="sidebar-menu">
        <li class="<?= $current_page == 'dashboard.php' ? 'active' : ''; ?>"><a href="/sistem_skincare/views/dashboard.php">🏠 Dashboard</a></li>
        <li class="<?= $current_dir == 'barang' ? 'active' : ''; ?>"><a href="/sistem_skincare/views/barang/index.php">📦 Data Barang</a></li>
        <?php if ($_SESSION['role'] == 'sales'): ?>
        <li class="<?= $current_dir == 'transaksi' ? 'active' : ''; ?>"><a href="/sistem_skincare/views/transaksi/kasir.php">🛒 Transaksi Baru</a></li>
        <?php endif; ?>
        <?php if ($_SESSION['role'] == 'admin'): ?>
        <li class="<?= $current_dir == 'laporan' ? 'active' : ''; ?>"><a href="/sistem_skincare/views/laporan/index.php">📊 Laporan</a></li>
        <?php endif; ?>
        <li class="<?= $current_dir == 'pesanan' ? 'active' : ''; ?>">
            <a href="/sistem_skincare/views/pesanan/index.php" style="display:flex;justify-content:space-between;align-items:center;">
                <span>📋 Pesanan</span>
                <?php if ($jumlah_pesanan_baru > 0): ?><span class="notif-badge"><?= $jumlah_pesanan_baru; ?></span><?php endif; ?>
            </a>
        </li>
        <li class="<?= $current_dir == 'komisi' ? 'active' : ''; ?>"><a href="/sistem_skincare/views/komisi/index.php">💰 Komisi</a></li>
        <?php if ($_SESSION['role'] == 'admin'): ?>
        <li class="<?= $current_dir == 'user' ? 'active' : ''; ?>"><a href="/sistem_skincare/views/user/index.php">👥 User</a></li>
        <?php endif; ?>
        <li><a href="/sistem_skincare/public/katalog.php" target="_blank">🌐 Katalog</a></li>
    </ul>
    <div class="sidebar-footer">
        <a href="/sistem_skincare/views/auth/logout.php" class="btn btn-light btn-sm w-100">🚪 Logout</a>
    </div>
</div>

<!-- MOBILE HEADER (HP ONLY) -->
<div class="mobile-header">
    <div style="display:flex;align-items:center;gap:10px;">
        <div class="mobile-avatar"><?= $inisial; ?></div>
        <div>
            <div class="mobile-user-name"><?= $_SESSION['nama_lengkap']; ?></div>
            <div class="mobile-user-role"><?= ucfirst($_SESSION['role']); ?></div>
        </div>
    </div>
    <a href="/sistem_skincare/views/auth/logout.php" class="mobile-logout">🚪</a>
</div>

<!-- BOTTOM NAV (HP ONLY) -->
<div class="bottom-nav">
    <a href="/sistem_skincare/views/dashboard.php" class="bnav-item <?= $current_page == 'dashboard.php' ? 'active' : ''; ?>">
        <div class="bnav-icon">🏠</div><div class="bnav-label">Home</div>
    </a>
    <a href="/sistem_skincare/views/barang/index.php" class="bnav-item <?= $current_dir == 'barang' ? 'active' : ''; ?>">
        <div class="bnav-icon">📦</div><div class="bnav-label">Barang</div>
    </a>
    <?php if ($_SESSION['role'] == 'sales'): ?>
    <a href="/sistem_skincare/views/transaksi/kasir.php" class="bnav-item <?= $current_dir == 'transaksi' ? 'active' : ''; ?>">
        <div class="bnav-icon">🛒</div><div class="bnav-label">Kasir</div>
    </a>
    <?php endif; ?>
    <?php if ($_SESSION['role'] == 'admin'): ?>
    <a href="/sistem_skincare/views/laporan/index.php" class="bnav-item <?= $current_dir == 'laporan' ? 'active' : ''; ?>">
        <div class="bnav-icon">📊</div><div class="bnav-label">Laporan</div>
    </a>
    <?php endif; ?>
    <a href="/sistem_skincare/views/pesanan/index.php" class="bnav-item <?= $current_dir == 'pesanan' ? 'active' : ''; ?>">
        <div class="bnav-icon">📋</div>
        <?php if ($jumlah_pesanan_baru > 0): ?><span class="bnav-badge"><?= $jumlah_pesanan_baru; ?></span><?php endif; ?>
        <div class="bnav-label">Pesanan</div>
    </a>
    <a href="/sistem_skincare/views/komisi/index.php" class="bnav-item <?= $current_dir == 'komisi' ? 'active' : ''; ?>">
        <div class="bnav-icon">💰</div><div class="bnav-label">Komisi</div>
    </a>
    <?php if ($_SESSION['role'] == 'admin'): ?>
    <a href="/sistem_skincare/views/user/index.php" class="bnav-item <?= $current_dir == 'user' ? 'active' : ''; ?>">
        <div class="bnav-icon">👥</div><div class="bnav-label">User</div>
    </a>
    <?php else: ?>
    <a href="/sistem_skincare/public/katalog.php" target="_blank" class="bnav-item">
        <div class="bnav-icon">🌐</div><div class="bnav-label">Katalog</div>
    </a>
    <?php endif; ?>
</div>

<style>
    * { box-sizing: border-box; }
    html, body { margin: 0; padding: 0; width: 100%; overflow-x: hidden; }
    body { background-color: #fce4ec; font-family: 'Segoe UI', sans-serif; }

    /* SIDEBAR DESKTOP */
    .sidebar {
        position: fixed; left: 0; top: 0;
        width: 260px; height: 100vh;
        background: linear-gradient(180deg, #d81b60 0%, #ad1457 100%);
        color: white; overflow-y: auto;
        box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        z-index: 100;
    }
    .sidebar-brand { padding: 25px 20px 15px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.15); }
    .sidebar-brand h4 { margin: 0; font-weight: bold; }
    .sidebar-brand small { opacity: 0.8; font-size: 0.75rem; }
    .sidebar-user { padding: 20px; display: flex; align-items: center; gap: 12px; border-bottom: 1px solid rgba(255,255,255,0.15); }
    .user-avatar { width: 45px; height: 45px; border-radius: 50%; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1.2rem; }
    .user-name { font-weight: 600; font-size: 0.9rem; }
    .user-role { font-size: 0.75rem; opacity: 0.8; }
    .sidebar-menu { list-style: none; padding: 15px 10px; margin: 0; }
    .sidebar-menu li { margin-bottom: 5px; }
    .sidebar-menu li a { display: block; padding: 12px 18px; color: rgba(255,255,255,0.85); text-decoration: none; border-radius: 10px; transition: all 0.3s; font-size: 0.9rem; }
    .sidebar-menu li a:hover { background: rgba(255,255,255,0.15); color: white; padding-left: 25px; }
    .sidebar-menu li.active a { background: rgba(255,255,255,0.25); color: white; font-weight: 600; }
    .notif-badge { background: #ff1744; color: white; font-size: 0.7rem; font-weight: 700; padding: 3px 8px; border-radius: 12px; min-width: 22px; text-align: center; }
    .sidebar-footer { padding: 20px; border-top: 1px solid rgba(255,255,255,0.15); }

    /* MOBILE HEADER & BOTTOM NAV - DEFAULT HIDDEN */
    .mobile-header, .bottom-nav { display: none; }

    /* MAIN CONTENT */
    .main-content { margin-left: 260px; padding: 25px; min-height: 100vh; max-width: calc(100% - 260px); overflow-x: hidden; }
    .page-card, .kasir-card { border: none; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); background: white; }

    /* ========================================== */
    /* HP & TABLET KECIL (max-width: 900px) */
    /* SIDEBAR HILANG, BOTTOM NAV MUNCUL */
    /* ========================================== */
    @media (max-width: 900px) {
        /* SIDEBAR HILANG TOTAL */
        .sidebar { display: none !important; }
        
        /* MOBILE HEADER MUNCUL */
        .mobile-header {
            display: flex; justify-content: space-between; align-items: center;
            background: linear-gradient(135deg, #ec407a, #d81b60);
            color: white; padding: 12px 15px;
            position: sticky; top: 0; z-index: 100;
            box-shadow: 0 2px 10px rgba(216,27,96,0.3);
        }
        .mobile-avatar { width: 38px; height: 38px; border-radius: 50%; background: rgba(255,255,255,0.25); display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1rem; }
        .mobile-user-name { font-size: 0.85rem; font-weight: 600; line-height: 1.2; }
        .mobile-user-role { font-size: 0.7rem; opacity: 0.85; }
        .mobile-logout { color: white; text-decoration: none; font-size: 1.1rem; padding: 6px; border-radius: 50%; background: rgba(255,255,255,0.2); width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; }
        
        /* MAIN CONTENT FULL WIDTH */
        .main-content { margin-left: 0; max-width: 100%; padding: 12px 12px 85px 12px; }
        
        /* BOTTOM NAV MUNCUL */
        .bottom-nav {
            display: flex; position: fixed; bottom: 0; left: 0; right: 0;
            background: white; box-shadow: 0 -3px 15px rgba(0,0,0,0.08);
            padding: 6px 4px 8px; z-index: 999;
            justify-content: space-around; align-items: center;
            border-top: 1px solid #f0f0f0;
        }
        .bnav-item { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; text-decoration: none; color: #999; padding: 5px 2px; position: relative; transition: all 0.2s; border-radius: 8px; max-width: 75px; }
        .bnav-item.active { color: #d81b60; background: #fce4ec; }
        .bnav-icon { font-size: 1.25rem; margin-bottom: 2px; }
        .bnav-label { font-size: 0.6rem; font-weight: 600; }
        .bnav-badge { position: absolute; top: 0; right: 12px; background: #ff1744; color: white; font-size: 0.55rem; font-weight: 700; padding: 1px 5px; border-radius: 8px; min-width: 14px; text-align: center; }
        
        /* CARD PADDING */
        .page-card, .kasir-card { padding: 15px !important; border-radius: 12px; margin-bottom: 12px; }
        
        /* GRID → 1 KOLOM */
        .col-md-4, .col-md-3, .col-md-6, .col-md-8, .col-md-12, .col-lg-4, .col-lg-3, .col-sm-6, .col-6, .col-4 { flex: 0 0 100% !important; max-width: 100% !important; }
        
        /* FONT */
        body { font-size: 0.85rem; }
        h3 { font-size: 1.1rem; }
        h4 { font-size: 1rem; }
        h5 { font-size: 0.9rem; }
        h6 { font-size: 0.8rem; }
        
        /* STAT CARD */
        .stat-card { padding: 12px !important; margin-bottom: 10px; }
        .stat-card h2, .stat-card h3 { font-size: 0.95rem !important; }
        .stat-card p, .stat-card h6 { font-size: 0.68rem !important; }
        .stat-card .icon { font-size: 1.4rem !important; }
        
        /* TABLE */
        .table-responsive { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .table { font-size: 0.72rem; min-width: 500px; }
        .table th, .table td { padding: 8px 6px; white-space: nowrap; }
        
        /* BUTTON & FORM */
        .btn { font-size: 0.75rem; padding: 6px 10px; }
        .form-control, .form-select { font-size: 0.85rem; padding: 8px 12px; }
        
        /* CHART */
        canvas { max-height: 200px; }
    }
    
    @media print {
        .sidebar, .bottom-nav, .mobile-header, .no-print { display: none !important; }
        .main-content { margin-left: 0 !important; max-width: 100% !important; }
    }
</style>