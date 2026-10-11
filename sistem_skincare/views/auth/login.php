<?php
require_once '../../config/koneksi.php';
require_once '../../config/helper.php';

if (isset($_SESSION['id_user'])) {
    header("Location: ../dashboard.php");
    exit;
}

$error = '';
if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($koneksi, $query);

    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
        
        if ($password === $user['password']) {
            $_SESSION['id_user']       = $user['id_user'];
            $_SESSION['nama_lengkap']  = $user['nama_lengkap'];
            $_SESSION['role']          = $user['role'];
            $_SESSION['komisi_persen'] = $user['komisi_persen'];
            
            header("Location: ../dashboard.php");
            exit;
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Username tidak ditemukan!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Login - GlowSkin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        
        /* KUNCI MATI: Body nggak bisa digeser */
        html {
            overflow: hidden;
            position: fixed;
            width: 100%;
            height: 100%;
        }
        body {
            width: 100%;
            height: 100%;
            overflow: hidden;
            position: fixed;
            background: linear-gradient(135deg, #fce4ec, #f8bbd0);
            font-family: 'Segoe UI', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .card-login {
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 450px;
            padding: 30px;
            background: white;
        }
        
        @media (max-width: 768px) {
            body { padding: 15px; }
            .card-login {
                padding: 25px 20px;
                border-radius: 15px;
                max-width: 100%;
            }
            .card-login h3 { font-size: 1.2rem; }
            .form-control { font-size: 0.9rem; padding: 10px 14px; }
            .btn-login { padding: 12px; font-size: 0.9rem; }
        }
    </style>
</head>
<body>

<div class="card card-login">
    <h3 class="text-center mb-2" style="color: #d81b60; font-weight: bold;">🌸 GlowSkin</h3>
    <p class="text-center text-muted small mb-4">Sistem Penjualan & Komisi Sales</p>
    
    <?php if ($error): ?>
        <div class="alert alert-danger py-2 small"><?= $error ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" placeholder="Username" required autofocus>
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" placeholder="Password" required>
        </div>
        <button type="submit" name="login" class="btn btn-login w-100 text-white" style="background-color: #d81b60;">
            🔐 Login
        </button>
    </form>
    
    <hr class="my-3">
    <small class="text-muted text-center d-block" style="font-size: 0.75rem; line-height: 1.5;">
        <b>Admin:</b> admin / admin123<br>
        <b>Sales:</b> budi / sales123
    </small>
</div>

</body>
</html>