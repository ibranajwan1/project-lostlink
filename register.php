
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config/database.php';

$errors = [];
$success = '';
$nama = '';
$nim = '';
$email = '';

if (empty($_SESSION['register_csrf'])) {
    $_SESSION['register_csrf'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $nim = trim($_POST['nim'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $konfirmasi = $_POST['konfirmasi_password'] ?? '';
    $terms = isset($_POST['terms']);
    $csrf = $_POST['csrf_token'] ?? '';

    if (!hash_equals($_SESSION['register_csrf'], $csrf)) {
        $errors[] = 'Sesi form tidak valid. Silakan muat ulang halaman.';
    }

    if ($nama === '' || mb_strlen($nama) > 100) {
        $errors[] = 'Nama wajib diisi dan maksimal 100 karakter.';
    }

    if ($nim === '' || mb_strlen($nim) > 30) {
        $errors[] = 'Student ID wajib diisi dan maksimal 30 karakter.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 150) {
        $errors[] = 'Masukkan alamat email yang valid.';
    }

    if (strlen($password) < 8
        || !preg_match('/[0-9]/', $password)
        || !preg_match('/[^a-zA-Z0-9]/', $password)) {
        $errors[] = 'Password minimal 8 karakter dan harus mengandung angka serta simbol.';
    }

    if ($password !== $konfirmasi) {
        $errors[] = 'Konfirmasi password tidak cocok.';
    }

    if (!$terms) {
        $errors[] = 'Kamu harus menyetujui Terms of Service dan Privacy Policy.';
    }

    if (!$errors) {
        if (!isset($pdo) || !($pdo instanceof PDO)) {
            $errors[] = 'Koneksi database gagal. Periksa config/database.php.';
        } else {
            try {
                $check = $pdo->prepare(
                    'SELECT id FROM users WHERE email = :email OR nim = :nim LIMIT 1'
                );
                $check->execute([
                    ':email' => $email,
                    ':nim' => $nim
                ]);

                if ($check->fetch()) {
                    $errors[] = 'Email atau Student ID sudah terdaftar.';
                } else {
                    $hash = password_hash($password, PASSWORD_DEFAULT);

                    $insert = $pdo->prepare(
                        "INSERT INTO users (nama, email, password, nim, role)
                         VALUES (:nama, :email, :password, :nim, 'mahasiswa')"
                    );

                    $insert->execute([
                        ':nama' => $nama,
                        ':email' => $email,
                        ':password' => $hash,
                        ':nim' => $nim
                    ]);

                    $success = 'Akun berhasil dibuat! Kamu sekarang bisa melanjutkan ke halaman Log in.';
                    $nama = '';
                    $nim = '';
                    $email = '';
                    $_SESSION['register_csrf'] = bin2hex(random_bytes(32));
                }
            } catch (PDOException $e) {
                $errors[] = 'Terjadi masalah saat menyimpan akun. Periksa koneksi dan struktur database.';
            }
        }
    }
}

function e($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account | LostLink</title>
    <link rel="stylesheet" href="assets/css/register.css">
</head>
<body>

<header class="navbar">
    <a href="index.php" class="brand" aria-label="LostLink Home">
        <span class="brand-icon">L</span>
        <span class="brand-copy">
            <strong>LostLink</strong>
            <small>Northbridge University</small>
        </span>
    </a>

    <nav class="nav-links">
        <a href="index.php">Home</a>
        <a href="cari-barang.php">Browse items</a>
        <a href="index.php#cara-kerja">How it works</a>
    </nav>

    <div class="nav-actions">
        <a href="login.php" class="login-link">Log in</a>
        <a href="index.php" class="nav-button">Report an item</a>
    </div>
</header>

<main class="register-layout">
    <aside class="community-panel">
        <div class="eyebrow light-eyebrow">YOUR CAMPUS. YOUR COMMUNITY.</div>

        <h1>Good things find<br>their way back.</h1>

        <p class="community-description">
            Join a campus community that looks out for each other.
            Every report brings someone one step closer to their belongings.
        </p>

        <img
            src="assets/images/campus.png"
            alt="Students walking together on a university campus in autumn"
            class="campus-image"
        >

        <ul class="benefits-list">
            <li><span class="check-icon">✓</span> For the Northbridge community</li>
            <li><span class="check-icon">✓</span> University email, verified access</li>
            <li><span class="check-icon">✓</span> Your personal details stay private</li>
        </ul>
    </aside>

    <section class="form-panel">
        <div class="eyebrow">BECOME PART OF THE COMMUNITY</div>
        <h2>Let’s get you connected.</h2>
        <p class="form-intro">
            Create your Northbridge account. It’s free, secure, and only takes a moment.
        </p>

        <?php if ($success): ?>
            <div class="notice success-notice" role="status">
                <?= e($success) ?>
                <a href="login.php">Log in</a>
            </div>
        <?php endif; ?>

        <?php if ($errors): ?>
            <div class="notice error-notice" role="alert">
                <strong>Please check the following:</strong>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= e($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="register.php" class="register-form">
            <input type="hidden" name="csrf_token"
                   value="<?= e($_SESSION['register_csrf']) ?>">

            <div class="field">
                <label for="nama">Full name <span>*</span></label>
                <input
                    type="text"
                    id="nama"
                    name="nama"
                    placeholder="e.g. Alex Morgan"
                    value="<?= e($nama) ?>"
                    maxlength="100"
                    autocomplete="name"
                    required
                >
            </div>

            <div class="field">
                <label for="nim">Student ID <span>*</span></label>
                <input
                    type="text"
                    id="nim"
                    name="nim"
                    placeholder="Enter your student ID"
                    value="<?= e($nim) ?>"
                    maxlength="30"
                    autocomplete="off"
                    required
                >
            </div>

            <div class="field">
                <label for="email">University email <span>*</span></label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="you@university.edu"
                    value="<?= e($email) ?>"
                    maxlength="150"
                    autocomplete="email"
                    required
                >
                <small>We’ll send a verification link to this address.</small>
            </div>

            <div class="field">
                <label for="password">Password <span>*</span></label>
                <div class="password-wrap">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Create a strong password"
                        minlength="8"
                        autocomplete="new-password"
                        required
                    >
                    <button type="button" class="toggle-password"
                            data-target="password" aria-label="Show password">
                        Show
                    </button>
                </div>
                <small>At least 8 characters, including a number and a symbol.</small>
            </div>

            <div class="field">
                <label for="konfirmasi_password">Confirm password <span>*</span></label>
                <div class="password-wrap">
                    <input
                        type="password"
                        id="konfirmasi_password"
                        name="konfirmasi_password"
                        placeholder="Enter your password again"
                        minlength="8"
                        autocomplete="new-password"
                        required
                    >
                    <button type="button" class="toggle-password"
                            data-target="konfirmasi_password" aria-label="Show password">
                        Show
                    </button>
                </div>
            </div>

            <label class="terms-row">
                <input type="checkbox" name="terms" value="1"
                       <?= isset($_POST['terms']) ? 'checked' : '' ?> required>
                <span>
                    I agree to the <a href="#" class="text-link">Terms of Service</a>
                    and <a href="#" class="text-link">Privacy Policy</a>, and confirm
                    I am a member of Northbridge University.
                </span>
            </label>

            <button type="submit" class="create-button">Create account</button>
        </form>

        <p class="login-prompt">
            Already have an account? <a href="login.php">Log in</a>
        </p>

        <div class="security-note">
            <div class="security-icon">✓</div>
            <div>
                <strong>Your campus identity stays protected</strong>
                <p>
                    Your Student ID and contact details are never displayed
                    on public item listings.
                </p>
            </div>
        </div>
    </section>
</main>

<footer class="site-footer">
    <p>© 2026 LostLink · Northbridge University</p>
    <div>
        <a href="#">Privacy policy</a>
        <a href="#">Terms of service</a>
        <a href="mailto:">Contact us</a>
    </div>
</footer>

<script>
document.querySelectorAll('.toggle-password').forEach(function (button) {
    button.addEventListener('click', function () {
        const input = document.getElementById(this.dataset.target);
        const showing = input.type === 'text';

        input.type = showing ? 'password' : 'text';
        this.textContent = showing ? 'Show' : 'Hide';
        this.setAttribute('aria-label', showing ? 'Show password' : 'Hide password');
    });
});
</script>

</body>
</html>
