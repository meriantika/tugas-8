<?php
// Mengaktifkan laporan error agar tidak muncul layar putih polos
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'koneksi_pdo.php';

$filter = isset($_GET['category']) ? $_GET['category'] : '';
$products = [];
$categories = [];

try {
    // Berdasarkan gambar Anda, nama tabelnya adalah 'produk'
    $nama_tabel = 'produk'; 

    // 1. Ambil daftar Kategori
    $stmt_cat = $pdo->query("SELECT DISTINCT category FROM $nama_tabel ORDER BY category ASC");
    $categories = $stmt_cat->fetchAll(PDO::FETCH_ASSOC);

    // 2. Query ambil data Produk
    if ($filter && $filter != 'all') {
        $stmt = $pdo->prepare("SELECT * FROM $nama_tabel WHERE category = :cat");
        $stmt->execute(['cat' => $filter]);
    } else {
        $stmt = $pdo->query("SELECT * FROM $nama_tabel");
    }
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Terjadi kesalahan pada Database: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Produk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card-img-top { height: 200px; object-fit: cover; }
    </style>
</head>
<body class="bg-light">

<div class="container py-5">
    <h2 class="text-center mb-4">Daftar Produk</h2>

    <div class="row mb-4 justify-content-center">
        <div class="col-md-5">
            <form method="GET" action="" class="d-flex gap-2">
                <select name="category" class="form-select">
                    <option value="all">Semua Kategori</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= htmlspecialchars($cat['category']) ?>" <?= ($filter == $cat['category']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['category']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-primary">Filter</button>
            </form>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php if (count($products) > 0): ?>
            <?php foreach ($products as $row): ?>
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <img src="<?= htmlspecialchars($row['image']) ?>" class="card-img-top" alt="Gambar Produk">
                        <div class="card-body">
                            <span class="badge bg-info text-dark mb-2"><?= htmlspecialchars($row['category']) ?></span>
                            <h5 class="card-title"><?= htmlspecialchars($row['name']) ?></h5>
                            <p class="card-text text-muted small"><?= htmlspecialchars(substr($row['description'], 0, 80)) ?>...</p>
                            <h5 class="text-primary">Rp <?= number_format($row['price'], 0, ',', '.') ?></h5>
                        </div>
                        <div class="card-footer bg-white border-top-0 text-center">
                            <button class="btn btn-primary w-100">Beli Sekarang</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center">
                <div class="alert alert-warning">Belum ada data produk di tabel 'produk'.</div>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>