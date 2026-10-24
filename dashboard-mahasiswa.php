
<?php
session_start();

if (!isset($_SESSION['user_id'], $_SESSION['role'])) {
    header('Location: login.php');
    exit;
}

if ($_SESSION['role'] !== 'mahasiswa') {
    if ($_SESSION['role'] === 'admin') {
        header('Location: dashboard-admin.php');
        exit;
    }

    http_response_code(403);
    exit('Akses ditolak.');
}

$nama = $_SESSION['nama'] ?? 'Mahasiswa';
$email = $_SESSION['email'] ?? '';
$inisial = strtoupper(substr(trim($nama), 0, 1));

function e($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$tanggal = strtoupper(
    strftime('%A, %B %d, %Y', time())
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard | LostLink</title>
    <link rel="stylesheet" href="/assets/css/dashboard-mahasiswa.css">
</head>
<body>

<div class="app-layout">

    <aside class="sidebar">
        <a href="dashboard-mahasiswa.php" class="logo">
            <span class="logo-mark">L</span>
            <span>LostLink</span>
        </a>

        <p class="university-label">NORTHBRIDGE UNIVERSITY</p>

        <nav class="side-nav">
            <a href="dashboard-mahasiswa.php" class="nav-link active">
                <span class="nav-icon">▦</span>
                <span>Dashboard</span>
            </a>

            <a href="cari-barang.php" class="nav-link">
                <span class="nav-icon">⌕</span>
                <span>Search items</span>
            </a>

            <a href="lapor-hilang.php" class="nav-link">
                <span class="nav-icon">▤</span>
                <span>My reports</span>
            </a>

            <a href="klaim.php" class="nav-link">
                <span class="nav-icon">♢</span>
                <span>My claims</span>
            </a>

            <a href="klaim.php" class="nav-link">
                <span class="nav-icon">♧</span>
                <span>Notifications</span>
            </a>
        </nav>

        <div class="sidebar-help">
            <div class="help-symbol">♡</div>
            <h3>Need a helping hand?</h3>
            <p>
                Our Student Services is here to help you reconnect
                with your belongings.
            </p>
            <a href="mailto:lostlink@northbridge.edu">
                Contact campus support →
            </a>
        </div>

        <div class="sidebar-bottom">
            <a href="mailto:lostlink@northbridge.edu" class="nav-link">
                <span class="nav-icon">⚙</span>
                <span>Settings</span>
            </a>

            <a href="index.php" class="nav-link">
                <span class="nav-icon">↗</span>
                <span>Back to website</span>
            </a>
        </div>
    </aside>

    <div class="main-area">

        <header class="topbar">
            <div class="breadcrumb">
                Student workspace <span>/</span> Dashboard
            </div>

            <div class="topbar-right">
                <button class="notification-button" type="button"
                        aria-label="Notifications">
                    ♧
                </button>

                <div class="profile-avatar">
                    <?= e($inisial) ?>
                </div>

                <div class="profile-details">
                    <strong><?= e($nama) ?></strong>
                    <span>Student<?= $email ? ' · ' . e($email) : '' ?></span>
                </div>

                <a href="logout.php" class="profile-menu" title="Log out">⌄</a>
            </div>
        </header>

        <main class="dashboard-content">

            <section class="page-heading">
                <div>
                    <p class="date-label"><?= e($tanggal) ?></p>
                    <h1>Welcome back, <?= e($nama) ?>.</h1>
                    <p class="heading-description">
                        Here's what's happening with your items around campus.
                    </p>
                </div>

                <a href="lapor-temuan.php" class="report-button">
                    <span>＋</span> Report an item
                </a>
            </section>

            <section class="stats-grid">
                <article class="stat-card">
                    <div class="stat-top">
                        <span>My reports</span>
                        <span class="stat-icon">▦</span>
                    </div>
                    <div class="stat-number">—</div>
                    <p>Reports you have submitted</p>
                </article>

                <article class="stat-card">
                    <div class="stat-top">
                        <span>Potential matches</span>
                        <span class="stat-icon">⌕</span>
                    </div>
                    <div class="stat-number">—</div>
                    <p>Possible matching items</p>
                </article>

                <article class="stat-card">
                    <div class="stat-top">
                        <span>Pending claims</span>
                        <span class="stat-icon">♢</span>
                    </div>
                    <div class="stat-number">—</div>
                    <p>Claims awaiting review</p>
                </article>

                <article class="stat-card">
                    <div class="stat-top">
                        <span>Items returned</span>
                        <span class="stat-icon">♡</span>
                    </div>
                    <div class="stat-number">—</div>
                    <p>Items reunited with owners</p>
                </article>
            </section>

            <section class="quick-actions">
                <a href="lapor-hilang.php" class="quick-card">
                    <span class="quick-icon">⌕</span>
                    <div>
                        <h2>Lost something?</h2>
                        <p>Tell us what you're looking for.</p>
                        <span class="quick-link">Report lost item →</span>
                    </div>
                </a>

                <a href="lapor-temuan.php" class="quick-card">
                    <span class="quick-icon">⬡</span>
                    <div>
                        <h2>Found something?</h2>
                        <p>Help it get back to its owner.</p>
                        <span class="quick-link">Report found item →</span>
                    </div>
                </a>

                <a href="cari-barang.php" class="quick-card">
                    <span class="quick-icon">⌕</span>
                    <div>
                        <h2>Take a look around</h2>
                        <p>Browse recent finds on campus.</p>
                        <span class="quick-link">Search items →</span>
                    </div>
                </a>
            </section>

            <section class="dashboard-columns">

                <div class="left-column">

                    <article class="panel recent-panel">
                        <div class="panel-heading">
                            <div>
                                <h2>Recent reports</h2>
                                <p>Keep track of the items you've shared.</p>
                            </div>
                        </div>

                        <div class="empty-reports">
                            <span class="empty-icon">▤</span>
                            <div>
                                <strong>Your reports will appear here</strong>
                                <p>Submit a lost or found item to get started.</p>
                            </div>
                        </div>

                        <a href="lapor-hilang.php" class="panel-link">
                            Create a report →
                        </a>
                    </article>

                    <article class="panel match-panel">
                        <div class="panel-heading">
                            <div>
                                <h2>A possible match for your items</h2>
                                <p>
                                    Find potential matches among reports from campus.
                                </p>
                            </div>
                        </div>

                        <div class="match-empty">
                            <div class="match-illustration">⌕</div>
                            <div>
                                <strong>Looking for a match?</strong>
                                <p>
                                    Browse reported items to see if something
                                    matches what you're looking for.
                                </p>
                                <a href="cari-barang.php" class="panel-link">
                                    Search items →
                                </a>
                            </div>
                        </div>
                    </article>

                </div>

                <div class="right-column">

                    <article class="panel claim-panel">
                        <h2>Your current claim</h2>

                        <div class="status-placeholder">
                            <span class="status-dot"></span>
                            No claim information yet
                        </div>

                        <p class="panel-description">
                            Items you claim will appear here so you can
                            follow their status and next steps.
                        </p>

                        <a href="klaim.php" class="panel-link">
                            View my claims →
                        </a>
                    </article>

                    <article class="panel collection-panel">
                        <h2>Your campus collection point</h2>

                        <div class="location-icon">♧</div>

                        <strong>Student Services Desk</strong>
                        <p class="location-address">
                            University Center, Ground Floor
                        </p>

                        <p class="opening-hours">
                            Monday–Friday · 9:00 AM–5:00 PM
                            <br>
                            Bring your student ID for pickup.
                        </p>

                        <div class="collection-email">
                            <a href="mailto:lostlink@northbridge.edu">
                                lostlink@northbridge.edu
                            </a>
                        </div>
                    </article>

                </div>
            </section>

            <section class="bottom-banner">
                <span class="banner-icon">♡</span>
                <div>
                    <strong>Found an item on campus? A small action makes a big difference.</strong>
                    <p>
                        Bring valuable finds to Student Services, then add a report
                        so the owner can discover them safely.
                    </p>
                </div>
            </section>

            <footer class="page-footer">
                <span>LostLink · Northbridge University</span>
                <div>
                    <a href="mailto:lostlink@northbridge.edu">Campus support</a>
                    <a href="mailto:lostlink@northbridge.edu">Privacy &amp; safety</a>
                </div>
            </footer>

        </main>
    </div>
</div>

</body>
</html>
