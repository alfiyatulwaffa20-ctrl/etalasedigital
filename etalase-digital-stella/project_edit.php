<?php
require_once 'config/kuis_db.php';
require_once 'middleware/auth_check.php';

check_login();

$id = $_GET['id'] ?? null;
if (!$id) { header('Location: index.php'); exit(); }

$stmt = $pdo->prepare("SELECT * FROM projects WHERE id = :id");
$stmt->execute([':id' => $id]);
$project = $stmt->fetch();

if (!$project) { die("Data karya tidak ditemukan."); }

// Pengecekan Hak Akses Edit
if ($_SESSION['role'] === 'siswa' && $project['user_id'] != $_SESSION['user_id']) {
    die("Akses Ditolak: Anda tidak dapat mengedit karya milik siswa lain.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_project = trim($_POST['nama_project']);
    $deskripsi    = trim($_POST['deskripsi']);
    $link_project = trim($_POST['link_project']);
    $gambar_nama  = $project['gambar'];

    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
        $new_name = uniqid('project_', true) . '.' . $ext;
        
        if (move_uploaded_file($_FILES['gambar']['tmp_name'], 'uploads/' . $new_name)) {
            if (file_exists('uploads/' . $project['gambar'])) {
                unlink('uploads/' . $project['gambar']);
            }
            $gambar_nama = $new_name;
        }
    }

    $stmt = $pdo->prepare("UPDATE projects SET nama_project = :nama, deskripsi = :desk, gambar = :img, link_project = :link WHERE id = :id");
    $stmt->execute([':nama' => $nama_project, ':desk' => $deskripsi, ':img' => $gambar_nama, ':link' => $link_project, ':id' => $id]);
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Karya</title>
    <link rel="stylesheet" href="assets/css/style.php.css">
    <link rel="stylesheet" href="assets/css/form.css">
    <link rel="stylesheet" href="assets/css/edit.css">
</head>
<body>
<main class="form-container edit-container">
    <a href="index.php" class="edit-back"><span aria-hidden="true">←</span> Kembali ke Etalase</a>

    <div class="edit-heading">
        <span class="edit-icon" aria-hidden="true">✎</span>
        <div>
            <h1>Edit Karya Digital</h1>
            <p>Perbarui detail karya yang ingin kamu tampilkan.</p>
        </div>
    </div>

    <form class="edit-form" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="nama_project">Nama Karya</label>
            <input id="nama_project" type="text" name="nama_project" value="<?= htmlspecialchars($project['nama_project']) ?>" required>
        </div>
        <div class="form-group">
            <label for="deskripsi">Deskripsi</label>
            <textarea id="deskripsi" name="deskripsi" required><?= htmlspecialchars($project['deskripsi']) ?></textarea>
        </div>
        <div class="form-group">
            <label for="link_project">Link Project</label>
            <input id="link_project" type="url" name="link_project" value="<?= htmlspecialchars($project['link_project']) ?>" required>
        </div>
        <div class="form-group">
            <label for="gambar">Ganti Gambar <span>(Opsional)</span></label>
            <input id="gambar" type="file" name="gambar" accept="image/*">
        </div>
        <div class="edit-actions">
            <a class="cancel-button" href="index.php">Batal</a>
            <button class="save-button" type="submit">Simpan Perubahan</button>
        </div>
    </form>
</main>
</body>
</html>
