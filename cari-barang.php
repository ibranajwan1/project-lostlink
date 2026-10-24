
<?php
require_once __DIR__ . '/config/database.php';

$q = trim($_GET['q'] ?? '');
$kategori = trim($_GET['kategori'] ?? '');
$tipe = trim($_GET['tipe'] ?? '');

$items = [];
$dbReady = false;
$errorMessage = '';

if (isset($pdo) && $pdo instanceof PDO) {
    try {
        $sql = "SELECT id, judul, deskripsi, kategori, tipe, lokasi,
                       tanggal_kejadian, foto, status
                FROM items
                WHERE status <> 'dikembalikan'";

        $params = [];

        if ($q !== '') {
            $sql .= " AND (
                judul LIKE :q1
                OR deskripsi LIKE :q2
                OR lokasi LIKE :q3
                OR kategori LIKE :q4
            )";

            $keyword = '%' . $q . '%';

            $params['q1'] = $keyword;
            $params['q2'] = $keyword;
            $params['q3'] = $keyword;
            $params['q4'] = $keyword;
        }

        if ($kategori !== '') {
            $sql .= " AND kategori = :kategori";
            $params['kategori'] = $kategori;
        }

        if ($tipe !== '') {
            $sql .= " AND tipe = :tipe";
            $params['tipe'] = $tipe;
        }

        $sql .= " ORDER BY created_at DESC LIMIT 100";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $items = $stmt->fetchAll();

        $dbReady = true;
    } catch (PDOException $e) {
        error_log('LostLink search error: ' . $e->getMessage());
        $errorMessage = 'Terjadi kesalahan saat mengambil data barang.';
    }
} else {
    $errorMessage = 'Koneksi database belum tersedia.';
}

function escapeSearch($value)
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function formatTanggalSearch($tanggal)
{
    if (empty($tanggal)) {
        return '-';
    }

    $timestamp = strtotime($tanggal);

    return $timestamp ? date('d M Y', $timestamp) : '-';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cari Barang - LostLink</title>

    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/landing.css">
</head>
<body>

<?php
$headerFile = __DIR__ . '/includes/header.php';

if (file_exists($headerFile)) {
    require $headerFile;
}
?>

<main>
    <section class="container">
        <h1>Cari Barang</h1>
        <p>Temukan barang hilang atau barang yang telah ditemukan.</p>

        <form action="cari-barang.php" method="GET">
            <div>
                <label for="q">Kata kunci</label>
                <input
                    type="search"
                    id="q"
                    name="q"
                    placeholder="Cari nama atau deskripsi barang..."
                    value="<?= escapeSearch($q) ?>"
                >
            </div>

            <div>
                <label for="kategori">Kategori</label>
                <select id="kategori" name="kategori">
                    <option value="">Semua kategori</option>
                    <?php
                    $kategoriOptions = [
                        'Elektronik',
                        'Dokumen',
                        'Aksesoris',
                        'Pakaian',
                        'Kartu Mahasiswa',
                        'Lainnya'
                    ];

                    foreach ($kategoriOptions as $option):
                    ?>
                        <option
                            value="<?= escapeSearch($option) ?>"
                            <?= $kategori === $option ? 'selected' : '' ?>
                        >
                            <?= escapeSearch($option) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="tipe">Jenis barang</label>
                <select id="tipe" name="tipe">
                    <option value="">Semua jenis</option>
                    <option value="hilang" <?= $tipe === 'hilang' ? 'selected' : '' ?>>
                        Barang hilang
                    </option>
                    <option value="temuan" <?= $tipe === 'temuan' ? 'selected' : '' ?>>
                        Barang ditemukan
                    </option>
                </select>
            </div>

            <button type="submit">Cari Barang</button>
            <a href="cari-barang.php">Reset</a>
        </form>
    </section>

    <section class="container">
        <h2>Hasil Pencarian</h2>

        <?php if ($errorMessage !== ''): ?>
            <p><?= escapeSearch($errorMessage) ?></p>

        <?php elseif (!$items): ?>
            <p>Belum ada barang yang sesuai dengan pencarian.</p>

        <?php else: ?>
            <p>Ditemukan <?= count($items) ?> barang.</p>

            <div class="items-grid">
                <?php foreach ($items as $item): ?>
                    <article class="item-card">
                        <?php if (!empty($item['foto'])): ?>
                            <img
                                src="uploads/<?= rawurlencode(basename($item['foto'])) ?>"
                                alt="<?= escapeSearch($item['judul']) ?>"
                                loading="lazy"
                            >
                        <?php endif; ?>

                        <div class="item-card-content">
                            <h3><?= escapeSearch($item['judul']) ?></h3>

                            <p>
                                <?= escapeSearch($item['deskripsi'] ?: 'Tidak ada deskripsi.') ?>
                            </p>

                            <p>
                                <strong>Jenis:</strong>
                                <?= escapeSearch(ucfirst($item['tipe'])) ?>
                            </p>

                            <p>
                                <strong>Kategori:</strong>
                                <?= escapeSearch($item['kategori']) ?>
                            </p>

                            <p>
                                <strong>Lokasi:</strong>
                                <?= escapeSearch($item['lokasi'] ?: '-') ?>
                            </p>

                            <p>
                                <strong>Tanggal:</strong>
                                <?= escapeSearch(formatTanggalSearch($item['tanggal_kejadian'])) ?>
                            </p>

                            <a href="detail-barang.php?id=<?= (int) $item['id'] ?>">
                                Lihat Detail
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</main>

<?php
$footerFile = __DIR__ . '/includes/footer.php';

if (file_exists($footerFile)) {
    require $footerFile;
}
?>

</body>
</html>
```