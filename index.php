<?php
require_once __DIR__ . '/config/database.php';

$items = [];
$dbReady = false;
if ($pdo instanceof PDO) {
    try {
        $stmt = $pdo->query("SELECT id, judul, deskripsi, kategori, tipe, lokasi, tanggal_kejadian, foto, status FROM items WHERE status <> 'dikembalikan' ORDER BY created_at DESC LIMIT 6");
        $items = $stmt->fetchAll();
        $dbReady = true;
    } catch (PDOException $e) {
        error_log('LostLink item query failed: ' . $e->getMessage());
    }
}
function e($value): string { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
function itemDate($date): string {
    if (!$date) return 'Tanggal tidak dicantumkan';
    $time = strtotime($date);
    return $time ? date('d M Y', $time) : 'Tanggal tidak dicantumkan';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="LostLink adalah platform kampus untuk melaporkan dan menemukan barang hilang.">
  <title>LostLink — Temukan kembali barangmu</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/landing.css">
</head>
<body class="landing-page">
<header class="navbar landing-nav">
  <a href="index.php" class="logo">Lost<span>Link</span></a>
  <button class="nav-toggle" type="button" aria-label="Buka menu" aria-expanded="false">☰</button>
  <nav class="nav-links">
    <a class="active" href="index.php">Beranda</a>
    <a href="#barang-terbaru">Cari Barang</a>
    <a href="#cara-kerja">Cara Kerja</a>
    <a href="#keamanan">Keamanan</a>
  </nav>
  <div class="nav-actions">
    <a class="nav-login" href="login.php">Masuk</a>
    <a class="button button-small" href="lapor-hilang.php">Laporkan Barang</a>
  </div>
</header>

<main>
  <section class="hero section-wrap">
    <div class="hero-copy">
      <span class="eyebrow hero-eyebrow">LOSTLINK · CAMPUS COMMUNITY</span>
      <h1>Barang hilang?<br><span>Kita cari bersama.</span></h1>
      <p>Temukan barang yang hilang atau bantu teman kampus menemukan barangnya kembali. Satu komunitas, saling membantu.</p>
      <form class="hero-search" action="cari-barang.php" method="get">
        <span class="search-symbol" aria-hidden="true">⌕</span>
        <input type="search" name="q" placeholder="Cari barang, lokasi, atau kategori..." aria-label="Cari barang">
        <button type="submit" class="button">Cari Barang</button>
      </form>
      <div class="hero-links">
        <a href="lapor-hilang.php">＋ Laporkan barang hilang</a>
        <a href="lapor-temuan.php">Saya menemukan barang →</a>
      </div>
      <div class="trust-line"><span class="trust-dot"></span> Dibuat untuk komunitas kampus yang saling peduli</div>
    </div>
    <div class="hero-visual">
      <div class="hero-image-frame"><img src="assets/images/campus.png" alt="Area kampus LostLink" class="hero-image"></div>
      <div class="floating-note note-top"><span class="note-icon">✓</span><span><strong>Temukan lebih cepat</strong><small>Barang dan pemilik terhubung</small></span></div>
      <div class="floating-note note-bottom"><span class="note-avatar">LL</span><span><strong>Komunitas kampus</strong><small>Saling bantu, saling percaya</small></span></div>
      <div class="hero-orb"></div>
    </div>
  </section>

  <section class="stats-strip" aria-label="Tentang LostLink">
    <div><strong>01</strong><span>Laporkan barang</span></div>
    <div><strong>02</strong><span>Temukan kecocokan</span></div>
    <div><strong>03</strong><span>Hubungkan kembali</span></div>
  </section>

  <section class="items-section section-wrap" id="barang-terbaru">
    <div class="section-heading">
      <div><span class="eyebrow">DARI KOMUNITAS KAMPUS</span><h2>Barang terbaru</h2><p>Lihat laporan terbaru dan bantu barang kembali ke pemiliknya.</p></div>
      <a class="text-link" href="cari-barang.php">Lihat semua barang <span>→</span></a>
    </div>
    <?php if (!$dbReady): ?>
      <div class="database-notice"><strong>Database belum tersambung.</strong> Import file <code>database.sql</code> melalui phpMyAdmin, lalu muat ulang halaman ini.</div>
    <?php elseif (!$items): ?>
      <div class="empty-state"><div class="empty-icon">⌕</div><h3>Belum ada laporan barang</h3><p>Jadilah yang pertama membantu komunitas kampus.</p><a class="button" href="lapor-temuan.php">Laporkan barang temuan</a></div>
    <?php else: ?>
      <div class="item-grid">
        <?php foreach ($items as $item): ?>
          <article class="item-card">
            <a class="item-image" href="detail-barang.php?id=<?= (int)$item['id'] ?>" aria-label="Lihat <?= e($item['judul']) ?>">
              <?php if (!empty($item['foto']) && is_file(__DIR__ . '/' . $item['foto'])): ?>
                <img src="<?= e($item['foto']) ?>" alt="<?= e($item['judul']) ?>">
              <?php else: ?>
                <span class="item-placeholder" aria-hidden="true"><?= $item['kategori'] === 'Elektronik' ? '⌁' : ($item['kategori'] === 'Kartu & Identitas' ? '▤' : '◇') ?></span>
              <?php endif; ?>
              <span class="item-type <?= $item['tipe'] === 'hilang' ? 'type-lost' : 'type-found' ?>"><?= $item['tipe'] === 'hilang' ? 'Barang hilang' : 'Barang ditemukan' ?></span>
            </a>
            <div class="item-content"><span class="item-category"><?= e($item['kategori']) ?></span><h3><a href="detail-barang.php?id=<?= (int)$item['id'] ?>"><?= e($item['judul']) ?></a></h3><p class="item-location"><span>⌖</span> <?= e($item['lokasi']) ?></p><div class="item-card-footer"><span><?= e(itemDate($item['tanggal_kejadian'])) ?></span><a href="detail-barang.php?id=<?= (int)$item['id'] ?>" aria-label="Detail barang">→</a></div></div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>

  <section class="how-section" id="cara-kerja">
    <div class="section-wrap">
      <div class="section-heading centered-heading"><div><span class="eyebrow">MUDAH DAN PRAKTIS</span><h2>Tiga langkah sederhana</h2><p>Barang yang hilang tidak harus hilang selamanya.</p></div></div>
      <div class="steps-grid">
        <article class="step-card"><span class="step-number">01</span><div class="step-icon">＋</div><h3>Laporkan barang</h3><p>Isi informasi barang yang hilang atau yang kamu temukan beserta lokasi dan tanggalnya.</p></article>
        <article class="step-card"><span class="step-number">02</span><div class="step-icon">⌕</div><h3>Cari dan cocokkan</h3><p>Gunakan pencarian untuk menemukan laporan yang sesuai dengan barang yang kamu cari.</p></article>
        <article class="step-card"><span class="step-number">03</span><div class="step-icon">♡</div><h3>Kembali ke pemilik</h3><p>Ikuti proses klaim dan verifikasi agar barang diserahkan kepada pemilik yang benar.</p></article>
      </div>
    </div>
  </section>

  <section class="safety-section section-wrap" id="keamanan">
    <div class="safety-mark">✓</div><div class="safety-copy"><span class="eyebrow">AMAN DAN BERTANGGUNG JAWAB</span><h2>Barang kembali, rasa aman terjaga.</h2><p>Jangan bagikan informasi sensitif secara terbuka. Saat mengklaim barang, jelaskan ciri khusus yang dapat membantu proses verifikasi.</p></div><a class="button button-outline" href="cari-barang.php">Jelajahi laporan <span>→</span></a>
  </section>

  <section class="cta-section"><div class="section-wrap cta-inner"><span class="eyebrow">JADILAH BAGIAN DARI SOLUSI</span><h2>Menemukan barang itu lebih mudah<br>kalau kita saling membantu.</h2><p>Satu laporan kecil bisa sangat berarti bagi orang lain.</p><div class="cta-actions"><a class="button button-light" href="lapor-temuan.php">Laporkan barang temuan</a><a class="cta-secondary" href="lapor-hilang.php">Laporkan barang hilang →</a></div></div></section>
</main>

<footer class="site-footer"><div class="section-wrap footer-main"><div class="footer-brand"><a href="index.php" class="logo">Lost<span>Link</span></a><p>Platform lost and found untuk komunitas kampus yang peduli.</p></div><div class="footer-column"><strong>Jelajahi</strong><a href="cari-barang.php">Cari barang</a><a href="#cara-kerja">Cara kerja</a></div><div class="footer-column"><strong>Berpartisipasi</strong><a href="lapor-hilang.php">Laporkan kehilangan</a><a href="lapor-temuan.php">Laporkan temuan</a></div><div class="footer-column"><strong>Akun</strong><a href="login.php">Masuk</a></div></div><div class="section-wrap footer-bottom"><span>© <?= date('Y') ?> LostLink · Campus Lost &amp; Found</span><span>Dibuat untuk saling membantu.</span></div></footer>
<script>
const toggle = document.querySelector('.nav-toggle');
const nav = document.querySelector('.nav-links');
if (toggle && nav) toggle.addEventListener('click', () => {
  const open = nav.classList.toggle('nav-open');
  toggle.setAttribute('aria-expanded', String(open));
});
</script>
</body>
</html>
