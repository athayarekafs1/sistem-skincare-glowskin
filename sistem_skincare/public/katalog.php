<?php
require_once '../config/koneksi.php';
require_once '../config/helper.php';

$sudah_login = isset($_SESSION['id_user']);
$role_user = $sudah_login ? $_SESSION['role'] : '';
$nama_user = $sudah_login ? $_SESSION['nama_lengkap'] : '';

$query = "SELECT * FROM barang WHERE stok > 0 ORDER BY id_barang DESC";
$result = mysqli_query($koneksi, $query);

// Hitung jumlah item di keranjang
$jumlah_keranjang = 0;
if (isset($_SESSION['keranjang']) && is_array($_SESSION['keranjang'])) {
    $jumlah_keranjang = array_sum($_SESSION['keranjang']);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GlowSkin - Katalog Produk Skincare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Poppins', sans-serif; box-sizing: border-box; }
        body { background-color: #fff5f8; color: #333; margin: 0; }
        
        /* HERO */
        .hero { 
            background: linear-gradient(135deg, #f8bbd0 0%, #fce4ec 50%, #f8bbd0 100%); 
            padding: 45px 20px 35px; 
            text-align: center;
        }
        .hero h1 { 
            color: #d81b60; 
            font-weight: 700; 
            font-size: 2rem; 
            margin-bottom: 6px;
            letter-spacing: -0.5px;
        }
        .hero p { 
            color: #880e4f; 
            font-size: 0.9rem; 
            margin-bottom: 18px;
        }
        .hero-buttons {
            display: flex;
            gap: 8px;
            justify-content: center;
            flex-wrap: wrap;
            align-items: center;
        }
        .hero .btn-login {
            background: #d81b60;
            color: white;
            border: none;
            padding: 8px 18px;
            border-radius: 20px;
            font-size: 0.82rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s;
        }
        .hero .btn-login:hover { background: #ad1457; color: white; }
        
        .hero .btn-user {
            background: white;
            color: #d81b60;
            border: 1.5px solid #f8bbd0;
            padding: 8px 18px;
            border-radius: 20px;
            font-size: 0.82rem;
            font-weight: 500;
        }
        
        .hero .btn-keranjang {
            background: white;
            color: #d81b60;
            border: 1.5px solid #f8bbd0;
            padding: 8px 18px;
            border-radius: 20px;
            font-size: 0.82rem;
            font-weight: 500;
            text-decoration: none;
            position: relative;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .hero .btn-keranjang:hover { background: #fce4ec; color: #d81b60; }
        
        .badge-keranjang {
            position: absolute;
            top: -6px;
            right: -6px;
            background: #ff1744;
            color: white;
            font-size: 0.65rem;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 10px;
            min-width: 18px;
            text-align: center;
        }
        
        /* SEARCH */
        .search-section {
            max-width: 500px;
            margin: -22px auto 25px;
            padding: 0 15px;
            position: relative;
            z-index: 10;
        }
        .search-box { position: relative; }
        .search-input { 
            border: none; 
            border-radius: 30px; 
            padding: 12px 20px 12px 45px; 
            font-size: 0.88rem; 
            box-shadow: 0 5px 20px rgba(216,27,96,0.12); 
            width: 100%;
            background: white;
        }
        .search-input:focus { outline: none; box-shadow: 0 6px 25px rgba(216,27,96,0.25); }
        .search-icon { 
            position: absolute; left: 18px; top: 50%; transform: translateY(-50%); 
            font-size: 1rem; color: #d81b60;
        }
        
        /* FILTER KATEGORI */
        .kategori-wrapper { max-width: 900px; margin: 0 auto 30px; padding: 0 15px; }
        .kategori-scroll { display: flex; gap: 8px; justify-content: center; flex-wrap: wrap; }
        .kategori-btn { 
            padding: 6px 16px; border-radius: 20px; border: 1.5px solid #f8bbd0; 
            background: white; color: #d81b60; font-size: 0.78rem; font-weight: 500; 
            cursor: pointer; transition: all 0.3s; white-space: nowrap;
        }
        .kategori-btn:hover, .kategori-btn.active { 
            background: #d81b60; color: white; border-color: #d81b60; 
        }
        
        /* GRID PRODUK */
        .produk-container { max-width: 1100px; margin: 0 auto; padding: 0 15px; }
        .produk-card { 
            border: none; border-radius: 15px; overflow: hidden; transition: all 0.3s; 
            box-shadow: 0 3px 12px rgba(0,0,0,0.05); background: white; 
            height: 100%; display: flex; flex-direction: column;
        }
        .produk-card:hover { 
            transform: translateY(-5px); box-shadow: 0 10px 25px rgba(216,27,96,0.15);
        }
        .produk-img-wrapper { 
            width: 100%; height: 200px; background: #fff5f8; 
            display: flex; align-items: center; justify-content: center; 
            overflow: hidden; padding: 10px;
        }
        .produk-img { 
            max-width: 100%; max-height: 100%; width: auto; height: auto; 
            object-fit: contain; transition: transform 0.3s;
        }
        .produk-card:hover .produk-img { transform: scale(1.05); }
        
        .produk-body { padding: 15px; flex: 1; display: flex; flex-direction: column; }
        .kategori-badge { 
            background: #fce4ec; color: #880e4f; font-size: 0.65rem; 
            padding: 3px 10px; border-radius: 12px; font-weight: 600; 
            text-transform: uppercase; display: inline-block; margin-bottom: 8px;
            width: fit-content; letter-spacing: 0.3px;
        }
        .produk-nama { 
            font-weight: 600; font-size: 0.92rem; color: #333; 
            margin-bottom: 6px; line-height: 1.35; min-height: 40px;
        }
        .produk-desc { font-size: 0.75rem; color: #999; margin-bottom: 10px; line-height: 1.4; flex: 1; }
        .produk-harga { color: #d81b60; font-weight: 700; font-size: 1.1rem; margin-bottom: 3px; }
        .produk-stok { font-size: 0.7rem; color: #999; margin-bottom: 12px; }
        
        .btn-produk-group { display: flex; gap: 6px; }
        .btn-pesan { 
            background: #d81b60; color: white; border: none; border-radius: 20px; 
            font-weight: 600; font-size: 0.72rem; padding: 8px 10px; 
            flex: 1; text-align: center; text-decoration: none;
        }
        .btn-pesan:hover { background: #ad1457; color: white; }
        .btn-keranjang-kecil { 
            background: white; color: #d81b60; border: 1.5px solid #f8bbd0; 
            border-radius: 20px; font-weight: 600; font-size: 0.72rem; 
            padding: 8px 10px; flex: 1; text-align: center; text-decoration: none;
        }
        .btn-keranjang-kecil:hover { background: #fce4ec; color: #d81b60; }
        
        footer { 
            background: linear-gradient(135deg, #d81b60, #ad1457); 
            color: white; padding: 25px 15px; text-align: center; margin-top: 50px; 
        }
        footer p { margin: 0; opacity: 0.9; font-size: 0.82rem; }
        
        .empty-state { text-align: center; padding: 60px 20px; color: #999; }
        .empty-state .icon { font-size: 3.5rem; margin-bottom: 15px; }
        
        @media (max-width: 768px) {
            .hero h1 { font-size: 1.6rem; }
            .hero { padding: 35px 15px 30px; }
        }
    </style>
</head>
<body>

<!-- HERO -->
<div class="hero">
    <h1>🌸 GlowSkin</h1>
    <p>Produk perawatan kulit terbaik untuk kulit sehat bercahaya</p>
    
    <div class="hero-buttons">
        <?php if ($sudah_login): ?>
            <a href="/sistem_skincare/views/dashboard.php" class="btn-login">⬅️ Dashboard</a>
            <span class="btn-user">👤 <?= htmlspecialchars($nama_user); ?> (<?= ucfirst($role_user); ?>)</span>
        <?php else: ?>
            <a href="../views/auth/login.php" class="btn-login">🔐 Login Sales/Admin</a>
        <?php endif; ?>
        
        <a href="keranjang.php" class="btn-keranjang">
            🛒 Keranjang
            <?php if ($jumlah_keranjang > 0): ?>
                <span class="badge-keranjang"><?= $jumlah_keranjang; ?></span>
            <?php endif; ?>
        </a>
    </div>
</div>

<!-- SEARCH -->
<div class="search-section">
    <div class="search-box">
        <span class="search-icon">🔍</span>
        <input type="text" id="searchInput" class="search-input" placeholder="Cari produk skincare favoritmu...">
    </div>
</div>

<!-- FILTER KATEGORI -->
<div class="kategori-wrapper">
    <div class="kategori-scroll" id="kategoriFilter">
        <button class="kategori-btn active" data-kategori="semua">Semua</button>
        <?php
        $q_kat = mysqli_query($koneksi, "SELECT DISTINCT kategori FROM barang WHERE stok > 0 AND kategori IS NOT NULL AND kategori != '' ORDER BY kategori ASC");
        while ($kat = mysqli_fetch_assoc($q_kat)):
        ?>
            <button class="kategori-btn" data-kategori="<?= strtolower($kat['kategori']); ?>">
                <?= htmlspecialchars($kat['kategori']); ?>
            </button>
        <?php endwhile; ?>
    </div>
</div>

<!-- GRID PRODUK -->
<div class="produk-container">
    <div class="row g-3" id="produkContainer">
        <?php 
        mysqli_data_seek($result, 0);
        while ($row = mysqli_fetch_assoc($result)): 
            $gambar_ada = $row['gambar'] && file_exists('../public/uploads/' . $row['gambar']);
        ?>
        <div class="col-lg-3 col-md-4 col-sm-6 produk-item" 
             data-nama="<?= strtolower($row['nama_barang']); ?>"
             data-kategori="<?= strtolower($row['kategori']); ?>">
            <div class="produk-card">
                <div class="produk-img-wrapper">
                    <?php if ($gambar_ada): ?>
                        <img src="uploads/<?= $row['gambar']; ?>" class="produk-img" alt="<?= htmlspecialchars($row['nama_barang']); ?>">
                    <?php else: ?>
                        <div class="text-center text-muted">
                            <div style="font-size: 2.5rem;">🧴</div>
                            <small style="font-size: 0.7rem;">No Image</small>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="produk-body">
                    <span class="kategori-badge"><?= htmlspecialchars($row['kategori']); ?></span>
                    <h6 class="produk-nama"><?= htmlspecialchars($row['nama_barang']); ?></h6>
                    <p class="produk-desc"><?= htmlspecialchars(substr($row['deskripsi'] ?? '', 0, 60)); ?>...</p>
                    <div class="produk-harga"><?= rupiah($row['harga']); ?></div>
                    <div class="produk-stok">📦 Stok: <?= $row['stok']; ?></div>
                    <div class="btn-produk-group">
                        <a href="tambah_keranjang.php?id=<?= $row['id_barang']; ?>&redirect=checkout" class="btn-pesan">
                            ⚡ Pesan
                        </a>
                        <a href="tambah_keranjang.php?id=<?= $row['id_barang']; ?>" class="btn-keranjang-kecil">
                            🛒 +
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>

    <?php if (mysqli_num_rows($result) == 0): ?>
        <div class="empty-state">
            <div class="icon">🛍️</div>
            <h5>Belum ada produk tersedia</h5>
            <p style="font-size: 0.85rem;">Produk akan segera hadir. Cek lagi nanti ya!</p>
        </div>
    <?php endif; ?>
</div>

<footer>
    <p>&copy; <?= date('Y'); ?> GlowSkin Skincare. All rights reserved. 🌸</p>
</footer>

<script>
// Search
document.getElementById('searchInput').addEventListener('input', function() {
    let keyword = this.value.toLowerCase();
    document.querySelectorAll('.produk-item').forEach(function(item) {
        item.style.display = item.dataset.nama.includes(keyword) ? '' : 'none';
    });
});

// Filter kategori
document.querySelectorAll('.kategori-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.kategori-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        let kategori = this.dataset.kategori;
        document.querySelectorAll('.produk-item').forEach(function(item) {
            item.style.display = (kategori === 'semua' || item.dataset.kategori === kategori) ? '' : 'none';
        });
    });
});
</script>
</body>
</html>