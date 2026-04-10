<?php
session_start();
include '../koneksi.php';

// Proteksi Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

// --- LOGIKA HAPUS MASAL ---
if (isset($_POST['hapus_masal'])) {
    if (!empty($_POST['cek_id'])) {
        foreach ($_POST['cek_id'] as $id_laporan) {
            $id_laporan = mysqli_real_escape_string($koneksi, $id_laporan);
            mysqli_query($koneksi, "DELETE FROM Aspirasi WHERE id_aspirasi = '$id_laporan'");
            mysqli_query($koneksi, "DELETE FROM Input_Aspirasi WHERE id_pelaporan = '$id_laporan'");
        }
        $msg = "Data terpilih berhasil dihapus!";
        $type = "success";
    } else {
        $msg = "Pilih data terlebih dahulu.";
        $type = "warning";
    }
}

// Ambil data filter
$filter_kategori = $_GET['filter_kat'] ?? '';
$filter_bulan    = $_GET['filter_bulan'] ?? '';
$filter_nis      = $_GET['filter_nis'] ?? '';
$filter_status   = $_GET['filter_status'] ?? '';

// Query Statistik
$count_all      = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM Input_Aspirasi"))['total'];
$count_selesai  = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM Aspirasi WHERE status='Selesai'"))['total'];
$count_proses   = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM Aspirasi WHERE status='Proses'"))['total'];
$count_menunggu = $count_all - $count_proses - $count_selesai;
if ($count_menunggu < 0) $count_menunggu = 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard — AspirasiKu</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #4f46e5; --primary-lt: #eef2ff;
            --success: #059669; --success-lt: #d1fae5;
            --warning: #d97706; --warning-lt: #fef3c7;
            --danger: #dc2626;  --danger-lt: #fee2e2;
            --sidebar-w: 260px;
            --bg: #f8fafc;
        }

        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); color: #1e293b; overflow-x: hidden; }

        /* --- LAYOUT --- */
        .sidebar-overlay { 
            position: fixed; inset: 0; background: rgba(0,0,0,0.5); 
            z-index: 1040; display: none; backdrop-filter: blur(4px);
        }
        
        .main-wrap { 
            margin-left: var(--sidebar-w); 
            transition: all 0.3s ease; 
            min-height: 100vh;
        }

        /* --- CARDS & UI --- */
        .stat-card {
            background: white; border-radius: 16px; border: 1px solid #e2e8f0;
            padding: 1.25rem; transition: transform 0.2s; height: 100%;
        }
        .stat-value { font-size: 1.5rem; font-weight: 800; }
        
        /* --- RESPONSIVE TABLE (STAKCABLE) --- */
        @media (max-width: 991.98px) {
            .main-wrap { margin-left: 0; }
            .sidebar-overlay.show { display: block; }
            
            /* Table Mobile Transformation */
            .table-responsive-stack thead { display: none; }
            .table-responsive-stack tr { 
                display: block; border: 1px solid #e2e8f0; 
                margin-bottom: 1rem; border-radius: 12px; background: white;
                padding: 0.5rem;
            }
            .table-responsive-stack td { 
                display: flex; justify-content: space-between; 
                text-align: right; border: none; padding: 0.5rem 1rem;
            }
            .table-responsive-stack td::before {
                content: attr(data-label); font-weight: 700;
                text-align: left; color: #64748b; font-size: 0.75rem; text-transform: uppercase;
            }
        }

        .badge-status {
            padding: 0.4rem 0.8rem; border-radius: 8px; font-size: 0.75rem; font-weight: 700;
        }
        
        /* Utility */
        .fw-800 { font-weight: 800; }
        .bg-menunggu { background: var(--warning-lt); color: var(--warning); }
        .bg-proses { background: #e0f2fe; color: #0284c7; }
        .bg-selesai { background: var(--success-lt); color: var(--success); }
    </style>
</head>
<body>

<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<?php include 'sidebar.php'; ?>

<div class="main-wrap">
    <header class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top px-3">
        <div class="container-fluid px-0">
            <button class="btn btn-light d-lg-none me-2" onclick="toggleSidebar()">
                <i class="bi bi-list fs-4"></i>
            </button>
            <div class="me-auto">
                <h1 class="h5 mb-0 fw-800 d-none d-sm-block">Dashboard Admin</h1>
                <p class="small text-muted mb-0 d-none d-md-block">Manajemen Laporan AspirasiKu</p>
            </div>
            <div class="dropdown">
                <div class="bg-primary-subtle text-primary px-3 py-2 rounded-pill small fw-bold">
                    <i class="bi bi-person-badge me-1"></i> Admin
                </div>
            </div>
        </div>
    </header>

    <main class="p-3 p-lg-4">
        <div class="row g-3 mb-4">
            <div class="col-6 col-xl-3">
                <div class="stat-card border-start border-4 border-primary">
                    <div class="text-muted small fw-bold mb-1">TOTAL</div>
                    <div class="stat-value text-primary"><?= $count_all ?></div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="stat-card border-start border-4 border-warning">
                    <div class="text-muted small fw-bold mb-1">PENDING</div>
                    <div class="stat-value text-warning"><?= $count_menunggu ?></div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="stat-card border-start border-4 border-info">
                    <div class="text-muted small fw-bold mb-1">PROSES</div>
                    <div class="stat-value text-info"><?= $count_proses ?></div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="stat-card border-start border-4 border-success">
                    <div class="text-muted small fw-bold mb-1">SELESAI</div>
                    <div class="stat-value text-success"><?= $count_selesai ?></div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-3">
                <form method="GET" class="row g-2">
                    <div class="col-12 col-md-3">
                        <input type="month" class="form-control" name="filter_bulan" value="<?= htmlspecialchars($filter_bulan) ?>">
                    </div>
                    <div class="col-6 col-md-3">
                        <select class="form-select" name="filter_kat">
                            <option value="">Kategori</option>
                            <?php
                            $kats = mysqli_query($koneksi, "SELECT * FROM Kategori");
                            while ($k = mysqli_fetch_array($kats)) {
                                $sel = ($filter_kategori == $k['id_kategori']) ? 'selected' : '';
                                echo "<option value='$k[id_kategori]' $sel>$k[ket_kategori]</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <select class="form-select" name="filter_status">
                            <option value="">Status</option>
                            <option value="Menunggu" <?= ($filter_status=='Menunggu')?'selected':'' ?>>Menunggu</option>
                            <option value="Proses" <?= ($filter_status=='Proses')?'selected':'' ?>>Proses</option>
                            <option value="Selesai" <?= ($filter_status=='Selesai')?'selected':'' ?>>Selesai</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-4 d-flex gap-2">
                        <input type="number" class="form-control" name="filter_nis" placeholder="Cari NIS..." value="<?= htmlspecialchars($filter_nis) ?>">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
                        <a href="halaman_admin.php" class="btn btn-light"><i class="bi bi-arrow-clockwise"></i></a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold">Daftar Laporan</h6>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-outline-danger d-none" id="btnHapusMasal" onclick="submitHapusMasal()">
                        Hapus (<span id="countCheck">0</span>)
                    </button>
                    <button onclick="window.print()" class="btn btn-sm btn-light border"><i class="bi bi-printer"></i></button>
                </div>
            </div>
            
            <form id="formHapus" method="POST">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 table-responsive-stack">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-3" width="40"><input type="checkbox" id="checkAll" class="form-check-input"></th>
                                <th>Info Laporan</th>
                                <th>Kategori</th>
                                <th>Status</th>
                                <th class="text-end pe-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // ... Query tetap sama seperti sebelumnya ...
                            $sql = "SELECT ia.*, k.ket_kategori, a.status 
                                    FROM Input_Aspirasi ia
                                    JOIN Kategori k ON ia.id_kategori = k.id_kategori
                                    LEFT JOIN Aspirasi a ON ia.id_pelaporan = a.id_aspirasi
                                    WHERE 1=1";
                            // (Tambahkan logika filter Anda di sini)
                            if ($filter_kategori) $sql .= " AND ia.id_kategori = '".mysqli_real_escape_string($koneksi, $filter_kategori)."'";
                            if ($filter_bulan)    $sql .= " AND ia.tanggal LIKE '".mysqli_real_escape_string($koneksi, $filter_bulan)."%'";
                            if ($filter_nis)      $sql .= " AND ia.nis = '".mysqli_real_escape_string($koneksi, $filter_nis)."'";
                            if ($filter_status)   $sql .= ($filter_status == 'Menunggu') ? " AND (a.status = 'Menunggu' OR a.status IS NULL)" : " AND a.status = '".mysqli_real_escape_string($koneksi, $filter_status)."'";
                            
                            $sql .= " ORDER BY ia.id_pelaporan DESC";
                            $res = mysqli_query($koneksi, $sql);

                            if(mysqli_num_rows($res) > 0):
                                while($row = mysqli_fetch_array($res)):
                                    $st = $row['status'] ?? 'Menunggu';
                                    $cls = match($st) { 'Proses'=>'bg-proses', 'Selesai'=>'bg-selesai', default=>'bg-menunggu' };
                            ?>
                            <tr>
                                <td class="ps-3" data-label="Pilih">
                                    <input type="checkbox" name="cek_id[]" value="<?= $row['id_pelaporan'] ?>" class="form-check-input item-checkbox">
                                </td>
                                <td data-label="Info Laporan">
                                    <div class="fw-bold text-primary">#<?= $row['id_pelaporan'] ?></div>
                                    <div class="small text-muted"><?= date('d/m/Y', strtotime($row['tanggal'])) ?></div>
                                    <div class="small fw-bold">NIS: <?= $row['nis'] ?></div>
                                </td>
                                <td data-label="Kategori">
                                    <span class="small"><?= $row['ket_kategori'] ?></span><br>
                                    <span class="small text-muted"><i class="bi bi-geo-alt"></i> <?= $row['lokasi'] ?></span>
                                </td>
                                <td data-label="Status">
                                    <span class="badge-status <?= $cls ?>"><?= $st ?></span>
                                </td>
                                <td class="text-end pe-3" data-label="Opsi">
                                    <a href="tanggapan.php?id=<?= $row['id_pelaporan'] ?>" class="btn btn-sm btn-primary px-3 rounded-pill">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; else: ?>
                                <tr><td colspan="5" class="text-center py-5 text-muted">Data tidak ditemukan.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <input type="hidden" name="hapus_masal" value="1">
            </form>
        </div>
    </main>
</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="liveToast" class="toast align-items-center border-0 text-white" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body fw-bold" id="toastMsg"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('open');
        document.getElementById('sidebarOverlay').classList.toggle('show');
    }

    // Checkbox Logic
    const checkAll = document.getElementById('checkAll');
    const items = document.querySelectorAll('.item-checkbox');
    const btnHapus = document.getElementById('btnHapusMasal');
    const countText = document.getElementById('countCheck');

    function handleCheckbox() {
        const checkedCount = document.querySelectorAll('.item-checkbox:checked').length;
        btnHapus.classList.toggle('d-none', checkedCount === 0);
        countText.textContent = checkedCount;
    }

    checkAll.addEventListener('change', (e) => {
        items.forEach(i => i.checked = e.target.checked);
        handleCheckbox();
    });

    items.forEach(i => i.addEventListener('change', handleCheckbox));

    function submitHapusMasal() {
        if(confirm('Hapus data yang dipilih secara permanen?')) {
            document.getElementById('formHapus').submit();
        }
    }

    function showToast(msg, type) {
        const toastEl = document.getElementById('liveToast');
        const colors = { success: '#059669', warning: '#d97706', danger: '#dc2626' };
        toastEl.style.backgroundColor = colors[type];
        document.getElementById('toastMsg').textContent = msg;
        new bootstrap.Toast(toastEl).show();
    }

    <?php if(isset($msg)): ?>
        showToast("<?= $msg ?>", "<?= $type ?>");
    <?php endif; ?>
</script>

</body>
</html>