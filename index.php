<?php
declare(strict_types=1);

require_once __DIR__ . '/src/Perpustakaan.php';
require_once __DIR__ . '/src/BukuPelajaran.php';
session_start();

header('Content-Type: text/html; charset=UTF-8');

function esc(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

// CSRF token
if (!isset($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(16));
}
$csrf = $_SESSION['csrf'];

$flash = [];
$errors = [];

// Reset data via link
if (isset($_GET['reset'])) {
    if (!hash_equals($csrf, $_GET['csrf'] ?? '')) {
        $errors[] = 'CSRF token tidak valid untuk reset data.';
    } else {
        unset($_SESSION['lib_serialized']);
        $flash[] = 'Data perpustakaan direset.';
    }
}

// Ambil instance perpustakaan dari session atau seed awal
if (isset($_SESSION['lib_serialized'])) {
    $lib = @unserialize($_SESSION['lib_serialized'], ['allowed_classes' => true]);
    if (!$lib instanceof Perpustakaan) {
        $lib = new Perpustakaan();
    }
} else {
    $lib = new Perpustakaan();
    // Seed data awal hanya sekali
    $lib->tambahBuku(new Buku('Clean Code', 'Robert C. Martin', '9780132350884'));
    $lib->tambahBuku(new Buku('Refactoring', 'Martin Fowler', '9780201485677'));
    $lib->tambahBuku(new Buku('Design Patterns', 'Erich Gamma et al.', '9780201633610'));
    $lib->tambahBuku(new BukuPelajaran('Matematika Dasar', 'Tim Penulis', '9786231234567', 'Matematika', 'Kelas 10'));

    $lib->tambahAnggota(new Anggota('Budi', 'A001'));
    $lib->tambahAnggota(new Anggota('Siti', 'A002'));
}

// Proses aksi dari form
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $action = $_POST['action'] ?? '';
    $token = $_POST['csrf'] ?? '';
    if (!hash_equals($csrf, $token)) {
        $errors[] = 'CSRF token tidak valid.';
    } else {
        try {
            switch ($action) {
                case 'tambah_buku':
                    $judul = trim((string)($_POST['judul'] ?? ''));
                    $penulis = trim((string)($_POST['penulis'] ?? ''));
                    $isbn = trim((string)($_POST['isbn'] ?? ''));
                    if ($judul === '' || $penulis === '' || $isbn === '') {
                        throw new RuntimeException('Semua field buku wajib diisi.');
                    }
                    $lib->tambahBuku(new Buku($judul, $penulis, $isbn));
                    $flash[] = 'Buku berhasil ditambahkan.';
                    break;

                case 'tambah_anggota':
                    $nama = trim((string)($_POST['nama'] ?? ''));
                    $nomor = trim((string)($_POST['nomor_anggota'] ?? ''));
                    if ($nama === '' || $nomor === '') {
                        throw new RuntimeException('Nama dan nomor anggota wajib diisi.');
                    }
                    $lib->tambahAnggota(new Anggota($nama, $nomor));
                    $flash[] = 'Anggota berhasil didaftarkan.';
                    break;

                case 'pinjam':
                    $nomor = trim((string)($_POST['nomor_anggota'] ?? ''));
                    $isbn = trim((string)($_POST['isbn'] ?? ''));
                    if ($nomor === '' || $isbn === '') {
                        throw new RuntimeException('Nomor anggota dan ISBN wajib diisi.');
                    }
                    $lib->pinjamBuku($nomor, $isbn);
                    $flash[] = 'Buku berhasil dipinjam.';
                    break;

                case 'kembalikan':
                    $nomor = trim((string)($_POST['nomor_anggota'] ?? ''));
                    $isbn = trim((string)($_POST['isbn'] ?? ''));
                    if ($nomor === '' || $isbn === '') {
                        throw new RuntimeException('Nomor anggota dan ISBN wajib diisi.');
                    }
                    $lib->kembalikanBuku($nomor, $isbn);
                    $flash[] = 'Buku berhasil dikembalikan.';
                    break;

                default:
                    $errors[] = 'Aksi tidak dikenali.';
                    break;
            }
        } catch (Throwable $e) {
            $errors[] = $e->getMessage();
        }
    }
}

// Simpan kembali state ke session
$_SESSION['lib_serialized'] = serialize($lib);

// Render HTML
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Manajemen Perpustakaan</title>
    <style>
        :root {
            --bg: #f7f9fc;
            --text: #1f2937;
            --muted: #6b7280;
            --primary: #2563eb;
            --primary-600: #1d4ed8;
            --secondary: #334155;
            --card-bg: #ffffff;
            --border: #e5e7eb;
            --ok-bg: #ecfdf5; --ok-border: #10b981; --ok-text: #065f46;
            --err-bg: #fef2f2; --err-border: #ef4444; --err-text: #7f1d1d;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
            color: var(--text);
            background: var(--bg);
        }
        .container { max-width: 980px; margin: 0 auto; padding: 20px; }
        .hero { background: linear-gradient(135deg, #eef2ff, #f0f9ff); border-bottom: 1px solid var(--border); }
        .hero h1 { margin: 0 0 6px 0; font-size: 1.75rem; }
        .subtitle { margin: 0 0 12px 0; color: var(--muted); }

        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px; }
        .card { background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; padding: 16px; box-shadow: 0 1px 2px rgba(0,0,0,.04); }
        fieldset { border: none; padding: 0; margin: 0; }
        label { display: block; margin: 10px 0 6px; font-weight: 600; }
        input[type=text] { width: 100%; padding: 10px 12px; border: 1px solid var(--border); border-radius: 8px; outline: none; background: #fff; }
        input[type=text]:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(37, 99, 235, .15); }

        .btn { display: inline-block; padding: 10px 14px; border-radius: 8px; text-decoration: none; font-weight: 600; border: 1px solid transparent; }
        .btn-primary { background: var(--primary); color: #fff; }
        .btn-primary:hover { background: var(--primary-600); }
        .btn-secondary { background: #fff; color: var(--secondary); border-color: var(--border); }
        .btn-secondary:hover { border-color: #cbd5e1; }
        button { padding: 10px 14px; border-radius: 8px; background: var(--primary); color: #fff; border: none; cursor: pointer; font-weight: 600; }
        button:hover { background: var(--primary-600); }

        .messages { margin: 16px 0; display: grid; gap: 8px; }
        .ok { background: var(--ok-bg); border: 1px solid var(--ok-border); color: var(--ok-text); padding: 10px 12px; border-radius: 8px; }
        .err { background: var(--err-bg); border: 1px solid var(--err-border); color: var(--err-text); padding: 10px 12px; border-radius: 8px; }

        h2 { margin-top: 24px; }
        .muted { color: var(--muted); }

        ul { list-style: none; padding: 0; margin: 0; }
        ul li { background: #fff; border: 1px solid var(--border); border-radius: 10px; padding: 10px 12px; margin-bottom: 8px; box-shadow: 0 1px 1px rgba(0,0,0,.03); }

        .footer { border-top: 1px solid var(--border); background: #ffffff; color: var(--muted); }
        .footer .container { padding-top: 16px; padding-bottom: 16px; }
    </style>
</head>
<body>
<header class="hero">
  <div class="container">
    <h1>Demo Sistem Manajemen Perpustakaan (OOP PHP)</h1>
    <p class="subtitle">OOP PHP • data disimpan di session (tanpa database)</p>
    <p><a class="btn btn-secondary" href="?reset=1&amp;csrf=<?=esc($csrf)?>">Reset Data</a></p>
  </div>
</header>
<main class="container">

<div class="messages">
    <?php foreach ($flash as $m): ?>
        <div class="ok"><?=esc($m)?></div>
    <?php endforeach; ?>
    <?php foreach ($errors as $m): ?>
        <div class="err"><?=esc($m)?></div>
    <?php endforeach; ?>
</div>

<div class="grid">
    <div class="card">
        <h2>Tambah Buku</h2>
        <form method="post">
            <input type="hidden" name="csrf" value="<?=esc($csrf)?>" />
            <input type="hidden" name="action" value="tambah_buku" />
            <fieldset>
                <label for="judul">Judul</label>
                <input type="text" id="judul" name="judul" required />

                <label for="penulis">Penulis</label>
                <input type="text" id="penulis" name="penulis" required />

                <label for="isbn">ISBN</label>
                <input type="text" id="isbn" name="isbn" required />
            </fieldset>
            <button type="submit">Tambah Buku</button>
        </form>
    </div>

    <div class="card">
        <h2>Daftarkan Anggota</h2>
        <form method="post">
            <input type="hidden" name="csrf" value="<?=esc($csrf)?>" />
            <input type="hidden" name="action" value="tambah_anggota" />
            <fieldset>
                <label for="nama">Nama</label>
                <input type="text" id="nama" name="nama" required />

                <label for="nomor_anggota">Nomor Anggota</label>
                <input type="text" id="nomor_anggota" name="nomor_anggota" required />
            </fieldset>
            <button type="submit">Daftarkan</button>
        </form>
    </div>

    <div class="card">
        <h2>Pinjam Buku</h2>
        <form method="post">
            <input type="hidden" name="csrf" value="<?=esc($csrf)?>" />
            <input type="hidden" name="action" value="pinjam" />
            <fieldset>
                <label for="nomor_anggota_pinjam">Nomor Anggota</label>
                <input type="text" id="nomor_anggota_pinjam" name="nomor_anggota" required />

                <label for="isbn_pinjam">ISBN</label>
                <input type="text" id="isbn_pinjam" name="isbn" required />
            </fieldset>
            <button type="submit">Pinjam</button>
        </form>
    </div>

    <div class="card">
        <h2>Kembalikan Buku</h2>
        <form method="post">
            <input type="hidden" name="csrf" value="<?=esc($csrf)?>" />
            <input type="hidden" name="action" value="kembalikan" />
            <fieldset>
                <label for="nomor_anggota_kembali">Nomor Anggota</label>
                <input type="text" id="nomor_anggota_kembali" name="nomor_anggota" required />

                <label for="isbn_kembali">ISBN</label>
                <input type="text" id="isbn_kembali" name="isbn" required />
            </fieldset>
            <button type="submit">Kembalikan</button>
        </form>
    </div>
</div>

<hr />
<h2>Daftar Buku</h2>
<ul>
<?php foreach ($lib->daftarBuku() as $b): ?>
    <?php $status = $b->isTersedia() ? 'Tersedia' : ('Dipinjam oleh ' . $b->getDipinjamOleh()); ?>
    <?php $extra = ''; if ($b instanceof BukuPelajaran) { $extra = ' [Pelajaran: ' . esc($b->getMataPelajaran()) . '; Tingkat: ' . esc($b->getTingkatKelas()) . ']'; } ?>
    <li><strong><?=esc($b->getJudul())?></strong> — <?=esc($b->getPenulis())?> (ISBN: <?=esc($b->getIsbn())?>) <?=$extra?> — <em><?=esc($status)?></em></li>
<?php endforeach; ?>
</ul>

<h2>Daftar Anggota</h2>
<?php foreach ($lib->getDaftarAnggota() as $a): ?>
    <h3><?=esc($a->getNama())?> (<?=esc($a->getNomorAnggota())?>)</h3>
    <?php $pinjam = $a->getBukuDipinjam(); ?>
    <?php if (!$pinjam): ?>
        <p class="muted">Tidak ada buku yang dipinjam.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($pinjam as $b): ?>
                <li><?=esc($b->getJudul())?> — ISBN <?=esc($b->getIsbn())?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
<?php endforeach; ?>

<p class="muted">Catatan: Untuk menjaga enkapsulasi, semua perubahan data dilakukan melalui method kelas (bukan manipulasi properti secara langsung).</p>
</main>
<footer class="footer">
</footer>
</body>
</html>
