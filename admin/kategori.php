<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php"); exit();
}

$msg = ''; $msg_type = '';

// TAMBAH
if (isset($_POST['tambah'])) {
    $ket = mysqli_real_escape_string($koneksi, trim($_POST['ket_kategori']));
    $ikon = mysqli_real_escape_string($koneksi, trim($_POST['ikon'] ?? 'bi-tag'));
    if ($ket !== '') {
        $cek = mysqli_query($koneksi, "SELECT id_kategori FROM Kategori WHERE ket_kategori='$ket'");
        if (mysqli_num_rows($cek) > 0) {
            $msg = "Kategori '$ket' sudah ada!"; $msg_type = 'warning';
        } else {
            mysqli_query($koneksi, "INSERT INTO Kategori (ket_kategori, ikon) VALUES ('$ket','$ikon')");
            $msg = "Kategori berhasil ditambahkan!"; $msg_type = 'success';
        }
    } else { $msg = "Nama kategori tidak boleh kosong!"; $msg_type = 'danger'; }
}

// EDIT
if (isset($_POST['edit'])) {
    $id  = (int)$_POST['id_kategori'];
    $ket = mysqli_real_escape_string($koneksi, trim($_POST['ket_kategori']));
    $ikon = mysqli_real_escape_string($koneksi, trim($_POST['ikon'] ?? 'bi-tag'));
    if ($ket !== '') {
        mysqli_query($koneksi, "UPDATE Kategori SET ket_kategori='$ket', ikon='$ikon' WHERE id_kategori=$id");
        $msg = "Kategori berhasil diperbarui!"; $msg_type = 'success';
    } else { $msg = "Nama kategori tidak boleh kosong!"; $msg_type = 'danger'; }
}

// HAPUS
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $cek = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as t FROM Input_Aspirasi WHERE id_kategori=$id"));
    if ($cek['t'] > 0) {
        $msg = "Kategori ini masih digunakan oleh $cek[t] laporan, tidak bisa dihapus!"; $msg_type = 'danger';
    } else {
        mysqli_query($koneksi, "DELETE FROM Kategori WHERE id_kategori=$id");
        $msg = "Kategori berhasil dihapus!"; $msg_type = 'success';
    }
}

// Ambil semua kategori + jumlah laporan
$query = "SELECT k.*, COUNT(ia.id_pelaporan) as jml_laporan
          FROM Kategori k
          LEFT JOIN Input_Aspirasi ia ON k.id_kategori = ia.id_kategori
          GROUP BY k.id_kategori
          ORDER BY k.id_kategori ASC";
$result = mysqli_query($koneksi, $query);
$kategori_list = [];
while ($r = mysqli_fetch_assoc($result)) $kategori_list[] = $r;

// Daftar ikon
$ikon_options = [
    'bi-building'      => 'Gedung',
    'bi-door-open'     => 'Pintu/Ruangan',
    'bi-laptop'        => 'Elektronik',
    'bi-lightbulb'     => 'Listrik',
    'bi-droplet'       => 'Air/Sanitasi',
    'bi-tools'         => 'Peralatan',
    'bi-tree'          => 'Taman/Lingkungan',
    'bi-shield'        => 'Keamanan',
    'bi-book'          => 'Perpustakaan',
    'bi-people'        => 'Toilet/WC',
    'bi-bicycle'       => 'Olahraga',
    'bi-car-front'     => 'Parkir',
    'bi-wifi'          => 'Jaringan/WiFi',
    'bi-tag'           => 'Lainnya',
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategori — AspirasiKu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary:#4f46e5; --primary-lt:#eef2ff; --success:#059669; --success-lt:#d1fae5;
            --warning:#d97706; --warning-lt:#fef3c7; --danger:#dc2626; --danger-lt:#fee2e2;
            --info:#0284c7; --info-lt:#e0f2fe; 
            --sidebar-w: 260px; /* Samakan dengan laporan.php */
            --bg:#f1f5f9; --card:#fff; --border:#e2e8f0; --text-muted:#64748b; --text-dark:#0f172a;
        }
        body{font-family:'Plus Jakarta Sans',sans-serif;background:var(--bg);color:var(--text-dark);min-height:100vh;}

        /* MAIN WRAP */
        .main-wrap { 
            margin-left: var(--sidebar-w); 
            min-height: 100vh; 
            display: flex; 
            flex-direction: column;
            transition: all 0.3s ease;
        }
        
        .topbar{background:white;border-bottom:1px solid var(--border);padding:.875rem 1.75rem;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:100;}
        .topbar-left h1{font-size:1.1rem;font-weight:700;margin:0;}
        .topbar-left p{font-size:.78rem;color:var(--text-muted);margin:0;}
        .admin-badge{display:flex;align-items:center;gap:.5rem;background:var(--primary-lt);border-radius:999px;padding:.4rem .9rem;font-size:.8rem;font-weight:600;color:var(--primary);}
        .content{padding:1.75rem;flex:1;}

        /* KATEGORI CARDS */
        .card-custom{background:var(--card);border-radius:16px;border:1px solid var(--border);overflow:hidden;}
        .card-header-custom{padding:1.25rem 1.5rem;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--border);flex-wrap:wrap;gap:.75rem;}
        .card-header-custom h5{margin:0;font-weight:700;font-size:.95rem;}
        
        .kat-card{background:var(--card);border-radius:14px;border:1px solid var(--border);padding:1.25rem;transition:all .2s;position:relative;}
        .kat-card:hover{transform:translateY(-3px);box-shadow:0 8px 20px rgba(0,0,0,.06);}
        .kat-icon{width:48px;height:48px;border-radius:12px;background:var(--primary-lt);color:var(--primary);display:grid;place-items:center;font-size:1.4rem;margin-bottom:1rem;}
        .kat-name{font-weight:700;font-size:.95rem;margin-bottom:.2rem;}
        .badge-count{position:absolute;top:1.25rem;right:1.25rem;background:var(--primary-lt);color:var(--primary);font-size:.7rem;font-weight:700;padding:.2rem .6rem;border-radius:999px;}

        /* FORM ELEMENTS */
        .form-label-sm{font-size:.72rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:.4rem;}
        .form-control, .form-select{font-size:.85rem;border-radius:10px;padding:.6rem .8rem;}
        .ikon-preview{width:40px;height:40px;border-radius:10px;background:var(--primary-lt);color:var(--primary);display:grid;place-items:center;font-size:1.2rem;}

        /* RESPONSIVE */
        .sidebar-toggle{display:none;}
        @media(max-width:991.98px){
            .main-wrap{margin-left:0;}
            .sidebar-toggle{display:flex;}
        }
        .empty-state{padding:3rem;text-align:center;color:#94a3b8;}
    </style>
</head>
<body>
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<?php include 'sidebar.php'; ?>

<div class="main-wrap">
    <header class="topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm btn-outline-secondary sidebar-toggle border-0" onclick="toggleSidebar()"><i class="bi bi-list fs-4"></i></button>
            <div class="topbar-left">
                <h1>Manajemen Kategori</h1>
                <p>Kelola kategori laporan sarana prasarana</p>
            </div>
        </div>
        <div class="admin-badge d-none d-sm-flex"><i class="bi bi-shield-fill-check"></i> Administrator</div>
    </header>

    <main class="content">
        <?php if ($msg): ?>
        <div class="alert alert-<?= $msg_type ?> alert-dismissible fade show border-0 shadow-sm mb-4" style="border-radius:12px;" role="alert">
            <i class="bi bi-<?= $msg_type=='success'?'check-circle':'exclamation-triangle' ?> me-2"></i><?= $msg ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <div class="row g-4">
            <div class="col-12 col-lg-4">
                <div class="card-custom">
                    <div class="card-header-custom bg-light">
                        <h5><i class="bi bi-plus-circle me-2 text-primary"></i>Tambah Kategori</h5>
                    </div>
                    <div class="p-4">
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label-sm">Nama Kategori</label>
                                <input type="text" class="form-control" name="ket_kategori" placeholder="Contoh: Lab Komputer" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label-sm">Pilih Ikon</label>
                                <div class="d-flex gap-2">
                                    <div class="ikon-preview" id="preview-tambah"><i class="bi bi-tag"></i></div>
                                    <select class="form-select" name="ikon" onchange="previewIkon('tambah', this.value)">
                                        <?php foreach ($ikon_options as $val => $label): ?>
                                        <option value="<?= $val ?>"><?= $label ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <button type="submit" name="tambah" class="btn btn-primary w-100 py-2">Simpan Kategori</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-8">
                <div class="card-custom">
                    <div class="card-header-custom bg-light">
                        <h5><i class="bi bi-grid me-2 text-primary"></i>Daftar Kategori</h5>
                        <span class="badge rounded-pill" style="background:var(--primary-lt);color:var(--primary);"><?= count($kategori_list) ?> Total</span>
                    </div>
                    <div class="p-4">
                        <?php if (count($kategori_list) > 0): ?>
                        <div class="row g-3">
                            <?php foreach ($kategori_list as $kat): $ikon_val = $kat['ikon'] ?? 'bi-tag'; ?>
                            <div class="col-sm-6">
                                <div class="kat-card">
                                    <span class="badge-count"><?= $kat['jml_laporan'] ?> lap</span>
                                    <div class="kat-icon"><i class="bi <?= htmlspecialchars($ikon_val) ?>"></i></div>
                                    <div class="kat-name"><?= htmlspecialchars($kat['ket_kategori']) ?></div>
                                    <div class="d-flex gap-2 mt-3">
                                        <button class="btn btn-warning btn-sm flex-fill" onclick="openEdit(<?= $kat['id_kategori'] ?>, '<?= addslashes($kat['ket_kategori']) ?>', '<?= $ikon_val ?>')">
                                            <i class="bi bi-pencil me-1"></i> Edit
                                        </button>
                                        <?php if ($kat['jml_laporan'] == 0): ?>
                                        <a href="kategori.php?hapus=<?= $kat['id_kategori'] ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Hapus kategori ini?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                        <?php else: ?>
                                        <button class="btn btn-light btn-sm text-muted" disabled><i class="bi bi-lock"></i></button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                        <div class="empty-state">
                            <i class="bi bi-folder2-open"></i>
                            <p class="mt-2 fw-semibold">Belum ada kategori ditambahkan</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Edit Kategori</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body p-4">
                    <input type="hidden" name="id_kategori" id="edit-id">
                    <div class="mb-3">
                        <label class="form-label-sm">Nama Kategori</label>
                        <input type="text" class="form-control" name="ket_kategori" id="edit-nama" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label-sm">Ikon</label>
                        <div class="d-flex gap-2">
                            <div class="ikon-preview" id="preview-edit"><i class="bi bi-tag"></i></div>
                            <select class="form-select" name="ikon" id="ikon-edit" onchange="previewIkon('edit', this.value)">
                                <?php foreach ($ikon_options as $val => $label): ?>
                                <option value="<?= $val ?>"><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="edit" class="btn btn-primary px-4">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function previewIkon(which, val) {
    document.getElementById('preview-' + which).innerHTML = `<i class="bi ${val}"></i>`;
}
function openEdit(id, nama, ikon) {
    document.getElementById('edit-id').value = id;
    document.getElementById('edit-nama').value = nama;
    document.getElementById('ikon-edit').value = ikon;
    previewIkon('edit', ikon);
    new bootstrap.Modal(document.getElementById('modalEdit')).show();
}
function toggleSidebar(){ document.getElementById('sidebar').classList.toggle('open'); document.getElementById('sidebarOverlay').classList.toggle('show'); }
function closeSidebar(){ document.getElementById('sidebar').classList.remove('open'); document.getElementById('sidebarOverlay').classList.remove('show'); }
</script>
</body>
</html>