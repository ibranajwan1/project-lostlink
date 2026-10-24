<?php
session_start();

if (isset($_SESSION['user_id'], $_SESSION['role'])) {
    if ($_SESSION['role'] === 'mahasiswa') {
        header('Location: dashboard-mahasiswa.php');
        exit;
    }

    if ($_SESSION['role'] === 'admin') {
        header('Location: dashboard-admin.php');
        exit;
    }
}

require_once __DIR__ . '/config/database.php';

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $passwordInput = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $passwordInput === '') {
        $error = 'Masukkan email dan password yang valid.';
    } elseif (!($pdo instanceof PDO)) {
        $error = 'Koneksi database gagal. Silakan coba lagi.';
    } else {
        try {
            $stmt = $pdo->prepare(
                'SELECT id, nama, email, password, role
                 FROM users
                 WHERE email = :email
                 LIMIT 1'
            );
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch();

            if (!$user || !password_verify($passwordInput, $user['password'])) {
                $error = 'Email atau password salah.';
            } elseif (!in_array($user['role'], ['mahasiswa', 'admin'], true)) {
                $error = 'Role akun tidak valid.';
            } else {
                session_regenerate_id(true);

                $_SESSION['user_id'] = (int)$user['id'];
                $_SESSION['nama'] = $user['nama'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];

                if ($user['role'] === 'mahasiswa') {
                    header('Location: dashboard-mahasiswa.php');
                } else {
                    header('Location: dashboard-admin.php');
                }
                exit;
            }
        } catch (PDOException $e) {
            error_log('LostLink login error: ' . $e->getMessage());
            $error = 'Terjadi kesalahan. Silakan coba lagi.';
        }
    }
}

function e($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Log in | LostLink</title>
<link rel="stylesheet" href="assets/css/login.css">
</head>

<body>

<div class="login-page">

    <div class="login-left">

        <a href="index.php" class="brand">
            <span class="brand-icon">L</span>
            <span>
                <strong>LostLink</strong>
                <small>Northbridge University</small>
            </span>
        </a>

        <p class="eyebrow">WELCOME BACK</p>

        <h1>Good to see you again.</h1>

        <p class="intro">
            Log in to continue to your LostLink account.
        </p>

        <?php if ($error !== ''): ?>
            <div class="error"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">

            <label>University email</label>

            <input
                type="email"
                name="email"
                value="<?= e($email) ?>"
                placeholder="you@university.edu"
                required>

            <label>Password</label>

            <input
                type="password"
                name="password"
                placeholder="Enter your password"
                required>

            <button type="submit">Log in</button>

        </form>

        <p class="signup">
            Don’t have an account?
            <a href="register.php">Create account</a>
        </p>

        <a href="index.php" class="back-home">← Back to home</a>

    </div>

    <div class="login-right">

        <div class="photo-card">

            <img src="assets/images/campus.png" alt="Campus LostLink">

        </div>

        <div class="info-card">

            <div class="info-icon">✓</div>

            <div>
                <h3>Campus community</h3>
                <p>Helping students reconnect with what matters.</p>
            </div>

        </div>

    </div>

</div>

</body>
</html>