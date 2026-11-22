<?php
session_start();
require_once 'config/kuis_db.php';
require_once 'middleware/auth_check.php';

check_login();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_project = trim($_POST['nama_project'] ?? '');
    $deskripsi    = trim($_POST['deskripsi'] ?? '');
    $link_project = trim($_POST['link_project'] ?? '');
    $user_id      = $_SESSION['user_id'];

    if (empty($nama_project) || empty($deskripsi) || empty($link_project)) {
        $error = "Semua kolom input wajib diisi!";
    } elseif (!filter_var($link_project, FILTER_VALIDATE_URL)) {
        $error = "Format Link Project tidak valid (Gunakan format https://...)!";
    } elseif (!isset($_FILES['gambar']) || $_FILES['gambar']['error'] !== UPLOAD_ERR_OK) {
        $error = "Pilih gambar karya digital terlebih dahulu!";
    } else {
        $file_tmp   = $_FILES['gambar']['tmp_name'];
        $file_name  = $_FILES['gambar']['name'];
        $file_size  = $_FILES['gambar']['size'];
        $file_ext   = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];
        $max_size    = 2 * 1024 * 1024; // Maksimal 2MB

        if (!in_array($file_ext, $allowed_ext)) {
            $error = "Format gambar harus berupa JPG, JPEG, PNG, atau WEBP!";
        } elseif ($file_size > $max_size) {
            $error = "Ukuran berkas gambar tidak boleh lebih dari 2MB!";
        } else {
            $new_file_name = uniqid('project_', true) . '.' . $file_ext;
            $upload_dir    = 'uploads/';

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $destination = $upload_dir . $new_file_name;

            if (move_uploaded_file($file_tmp, $destination)) {
                try {
                    $stmt = $pdo->prepare("INSERT INTO projects (user_id, nama_project, deskripsi, gambar, link_project) VALUES (:user_id, :nama_project, :deskripsi, :gambar, :link_project)");
                    $stmt->execute([
                        ':user_id'      => $user_id,
                        ':nama_project' => $nama_project,
                        ':deskripsi'    => $deskripsi,
                        ':gambar'       => $new_file_name,
                        ':link_project' => $link_project
                    ]);

                    header('Location: index.php');
                    exit();
                } catch (PDOException $e) {
                    if (file_exists($destination)) {
                        unlink($destination);
                    }
                    $error = "Terjadi kesalahan sistem: " . $e->getMessage();
                }
            } else {
                $error = "Gagal mengunggah berkas gambar ke server.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah Karya - Etalase Digital Stella</title>
    <link rel="stylesheet" href="assets/css/style.php.css">
    <link rel="stylesheet" href="assets/css/form.css">
    <link rel="stylesheet" href="assets/css/edit.css">
</head>
<body>
<main class="form-container edit-container">
    <a href="index.php" class="edit-back"><span aria-hidden="true">←</span> Kembali ke Etalase</a>

    <div class="edit-heading">
        <span class="edit-icon" aria-hidden="true">＋</span>
        <div>
            <h1>Tambah Karya Digital</h1>
            <p>Tambahkan karya baru untuk ditampilkan di etalase.</p>
        </div>
    </div>

    <?php if (!empty($error)): ?>
        <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
            <?= htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form class="edit-form" action="" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="nama_project">Nama Karya</label>
            <input type="text" id="nama_project" name="nama_project" placeholder="Masukkan nama karya" value="<?= htmlspecialchars($_POST['nama_project'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label for="deskripsi">Deskripsi</label>
            <textarea id="deskripsi" name="deskripsi" placeholder="Ceritakan singkat tentang karya ini" required><?= htmlspecialchars($_POST['deskripsi'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label for="link_project">Link Project</label>
            <input type="url" id="link_project" name="link_project" placeholder="https://..." value="<?= htmlspecialchars($_POST['link_project'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label for="gambar">Gambar Karya <span>(Maks. 2MB)</span></label>
            <input type="file" id="gambar" name="gambar" accept="image/png, image/jpeg, image/jpg, image/webp" required>
            
            <div class="preview-box" id="previewBox">
                <p style="font-size: 12px; color: #858791; margin-bottom: 8px;">Pratinjau Gambar</p>
                <img id="imagePreview" src="" alt="Preview Gambar">
            </div>
        </div>

        <div class="edit-actions">
            <a class="cancel-button" href="index.php">Batal</a>
            <button class="save-button" type="submit">Simpan Karya</button>
        </div>
    </form>
</main>

<!-- Script JS Preview Gambar Native -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputGambar = document.getElementById('gambar');
        const previewBox = document.getElementById('previewBox');
        const imagePreview = document.getElementById('imagePreview');

        inputGambar.addEventListener('change', function() {
            const file = this.files[0];

            if (file) {
                const reader = new FileReader();

                reader.addEventListener('load', function() {
                    imagePreview.setAttribute('src', this.result);
                    previewBox.style.display = 'block';
                });

                reader.readAsDataURL(file);
            } else {
                imagePreview.setAttribute('src', '');
                previewBox.style.display = 'none';
            }
        });
    });
</script>

</body>
</html>
