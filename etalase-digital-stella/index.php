<?php
require_once 'config/kuis_db.php';
require_once 'middleware/auth_check.php';

check_login();

// Ambil semua karya beserta nama pembuat
$stmt = $pdo->query("SELECT projects.*, users.nama AS pembuat 
                     FROM projects 
                     JOIN users ON projects.user_id = users.id 
                     ORDER BY projects.created_at DESC");
$projects = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Etalase Digital Stella</title>
    <link rel="stylesheet" href="assets/css/style.php.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/home.css">
<style>
    body { font-family: Arial, sans-serif; background: #f8f9fa; margin: 0; padding: 20px; }
    .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; margin-top: 20px; }
    .card { background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    .card img { width: 100%; height: 180px; object-fit: cover; }
    .card-body { padding: 15px; }
    .btn { text-decoration: none; padding: 8px 12px; border-radius: 4px; color: white; display: inline-block; font-size: 12px; margin-right: 5px; }
    .btn-blue { background: #007bff; } .btn-warning { background: #ffc107; color: black; } .btn-danger { background: #dc3545; }
</style>
</head>
<body>
<header class="site-header">
    <a class="brand" href="index.php" aria-label="Etalase Digital Stella">
        <img src="assets/img/logo.png" alt="">
        <span>Etalase Digital <strong>Stella</strong></span>
    </a>

    <nav class="header-actions" aria-label="Navigasi utama">
        <span class="user-greeting">Halo, <strong><?= htmlspecialchars($_SESSION['nama']) ?></strong></span>
        <span class="role-badge"><?= htmlspecialchars($_SESSION['role']) ?></span>
        <a href="project_tambah.php" class="nav-button nav-button-primary">＋ Tambah Karya</a>
        <?php if ($_SESSION['role'] === 'super_admin'): ?>
            <a href="admin/users.php" class="nav-button nav-button-light">Kelola Users</a>
        <?php endif; ?>
        <a href="auth/logout.php" class="nav-button nav-button-logout">Logout</a>
    </nav>
</header>

<div class="grid">
    <?php foreach($projects as $p): ?>
        <div class="card">
            <img src="uploads/<?= htmlspecialchars($p['gambar']) ?>" alt="Karya">
            <div class="card-body">
                <h3><?= htmlspecialchars($p['nama_project']) ?></h3>
                <p><small>Oleh: <b><?= htmlspecialchars($p['pembuat']) ?></b></small></p>
                <p><?= htmlspecialchars($p['deskripsi']) ?></p>
                <p><a href="<?= htmlspecialchars($p['link_project']) ?>" target="_blank">Lihat Project Tautan &rarr;</a></p>
                
                <!-- Hak Akses Edit/Hapus: Owner / Admin / Super Admin -->
                <?php if($_SESSION['role'] === 'super_admin' || $_SESSION['role'] === 'admin' || $_SESSION['user_id'] == $p['user_id']): ?>
                    <div class="project-actions">
                        <a href="project_edit.php?id=<?= $p['id'] ?>" class="btn btn-warning project-action project-action-edit">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="m14 5 5 5"></path>
                                <path d="M4 20l4.5-1 10.8-10.8a2.1 2.1 0 0 0-3-3L5.5 16 4 20z"></path>
                            </svg>
                            <span>Edit Karya</span>
                        </a>
                        <a href="project_hapus.php?id=<?= $p['id'] ?>" class="btn btn-danger project-action project-action-delete" onclick="return confirm('Hapus karya ini?')">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M4 7h16"></path>
                                <path d="M10 11v6M14 11v6"></path>
                                <path d="m5 7 1 13h12l1-13M9 7V4h6v3"></path>
                            </svg>
                            <span>Hapus</span>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>
</body>
</html>
