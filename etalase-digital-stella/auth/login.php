<?php
session_start();
require_once '../config/kuis_db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['nama']    = $user['nama'];
        $_SESSION['role']    = $user['role'];
        header('Location: ../index.php');
        exit();
    } else {
        $error = "Email atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><title>Login - Etalase Digital Stella</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/auth.css">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            margin: 0;
            padding: 24px;
            display: grid;
            place-items: center;
            font-family: Arial, sans-serif;
            color: #24252b;
            background:
                radial-gradient(circle at 15% 15%, rgba(239, 45, 54, 0.1), transparent 28%),
                radial-gradient(circle at 85% 85%, rgba(239, 45, 54, 0.08), transparent 30%),
                #f5f6f8;
        }

        .login-card {
            width: 100%;
            max-width: 430px;
            padding: 42px 40px 32px;
            border: 1px solid rgba(30, 32, 38, 0.06);
            border-radius: 22px;
            background: #fff;
            box-shadow: 0 24px 70px rgba(30, 32, 38, 0.12);
        }

        .brand {
            margin-bottom: 32px;
            text-align: center;
        }

        .brand-logo {
            width: 82px;
            height: 82px;
            margin: 0 auto 18px;
            display: grid;
            place-items: center;
            border: 1px solid #f0f0f1;
            border-radius: 22px;
            background: #fff;
            box-shadow: 0 8px 22px rgba(30, 32, 38, 0.08);
        }

        .brand-logo img {
            display: block;
            width: 62px;
            height: 62px;
            object-fit: contain;
        }

        .brand h1 {
            margin: 0 0 8px;
            font-size: 24px;
            letter-spacing: -0.5px;
        }

        .brand p {
            margin: 0;
            color: #777982;
            font-size: 14px;
            line-height: 1.5;
        }

        .login-form label {
            display: block;
            margin: 0 0 8px;
            color: #41434b;
            font-size: 13px;
            font-weight: 700;
        }

        .input-with-icon {
            position: relative;
        }

        .input-icon {
            position: absolute;
            top: 15px;
            left: 14px;
            width: 18px;
            height: 18px;
            color: #92949d;
            pointer-events: none;
            transition: color 0.2s;
        }

        .login-form input {
            width: 100%;
            height: 48px;
            margin: 0 0 18px;
            padding: 0 14px 0 43px;
            border: 1px solid #dedfe3;
            border-radius: 10px;
            outline: none;
            background: #fff;
            color: #24252b;
            font: inherit;
            font-size: 14px;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .login-form input::placeholder {
            color: #a0a1a8;
        }

        .login-form input:focus {
            border-color: #e52d36;
            box-shadow: 0 0 0 4px rgba(229, 45, 54, 0.11);
        }

        .input-with-icon:focus-within .input-icon {
            color: #df2933;
        }

        .login-form button {
            width: 100%;
            min-height: 49px;
            margin-top: 4px;
            border: 0;
            border-radius: 10px;
            background: #df2933;
            color: #fff;
            cursor: pointer;
            font: inherit;
            font-size: 15px;
            font-weight: 700;
            box-shadow: 0 8px 18px rgba(223, 41, 51, 0.22);
            transition: background 0.2s, transform 0.2s, box-shadow 0.2s;
        }

        .login-form button:hover {
            transform: translateY(-1px);
            background: #c91f29;
            box-shadow: 0 11px 22px rgba(223, 41, 51, 0.28);
        }

        .login-form button:focus-visible,
        .register-link:focus-visible {
            outline: 3px solid rgba(223, 41, 51, 0.3);
            outline-offset: 3px;
        }

        .login-error {
            margin: 0 0 18px;
            padding: 11px 13px;
            border: 1px solid #f3c4c6;
            border-radius: 9px;
            background: #fff2f2;
            color: #a5232b;
            font-size: 13px;
            line-height: 1.45;
        }

        .register-prompt {
            margin: 24px 0 0;
            color: #777982;
            text-align: center;
            font-size: 13px;
        }

        .register-link {
            color: #d92631;
            font-weight: 700;
            text-decoration: none;
        }

        .register-link:hover {
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            body {
                padding: 16px;
            }

            .login-card {
                padding: 34px 24px 27px;
                border-radius: 18px;
            }

            .brand h1 {
                font-size: 22px;
            }
        }
    </style>
</head>
<body>
<main class="login-card">
    <header class="brand">
        <div class="brand-logo">
            <img src="../assets/img/logo.png" alt="Logo Stella">
        </div>
        <h1>Etalase Digital Stella</h1>
        <p>Masuk untuk berbagi dan melihat karya.</p>
    </header>

    <?php if ($error): ?>
        <p class="login-error" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <form class="login-form" method="POST">
        <label for="email">Email</label>
        <div class="input-with-icon">
            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <rect x="3" y="5" width="18" height="14" rx="2"></rect>
                <path d="m3 7 9 6 9-6"></path>
            </svg>
            <input id="email" type="email" name="email" placeholder="nama@email.com" autocomplete="email" required>
        </div>

        <label for="password">Password</label>
        <div class="input-with-icon">
            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <rect x="4" y="10" width="16" height="11" rx="2"></rect>
                <path d="M8 10V7a4 4 0 0 1 8 0v3"></path>
                <path d="M12 14v3"></path>
            </svg>
            <input id="password" type="password" name="password" placeholder="Masukkan password" autocomplete="current-password" required>
        </div>

        <button type="submit">Masuk</button>
    </form>

    <p class="register-prompt">Siswa baru? <a class="register-link" href="register.php">Daftar Akun</a></p>
</main>
</body>
</html>
