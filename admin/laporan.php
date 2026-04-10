<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

// Ambil filter
$filter_bulan    = $_GET['filter_bulan'] ?? '';
$filter_kategori = $_GET['filter_kat']   ?? '';
$filter_status   = $_GET['filter_status'] ?? '';

// Build Query Kondisi
$where = "WHERE 1=1";
if ($filter_bulan)    $where .= " AND ia.tanggal LIKE '".mysqli_real_escape_string($koneksi,$filter_bulan)."%'";
if ($filter_kategori) $where .= " AND ia.id_kategori = '".mysqli_real_escape_string($koneksi,$filter_kategori)."'";
if ($filter_status) {
    if ($filter_status == 'Menunggu') $where .= " AND (a.status='Menunggu' OR a.status IS NULL)";
    else $where .= " AND a.status = '".mysqli_real_escape_string($koneksi,$filter_status)."'";
}

// Statistik ringkasan (mengikuti filter)
$count_all = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as t FROM Input_Aspirasi ia LEFT JOIN Aspirasi a ON ia.id_pelaporan=a.id_aspirasi $where"))['t'];
$count_selesai = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as t FROM Input_Aspirasi ia LEFT JOIN Aspirasi a ON ia.id_pelaporan=a.id_aspirasi $where AND a.status='Selesai'"))['t'];
$count_proses = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as t FROM Input_Aspirasi ia LEFT JOIN Aspirasi a ON ia.id_pelaporan=a.id_aspirasi $where AND a.status='Proses'"))['t'];
$count_menunggu = $count_all - $count_selesai - $count_proses;

// Data Utama
$sql = "SELECT ia.*, k.ket_kategori, a.status, a.feedback
        FROM Input_Aspirasi ia
        JOIN Kategori k ON ia.id_kategori = k.id_kategori
        LEFT JOIN Aspirasi a ON ia.id_pelaporan = a.id_aspirasi
        $where ORDER BY ia.tanggal DESC";
$result = mysqli_query($koneksi, $sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan — AspirasiKu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary:#4f46e5; --primary-lt:#eef2ff; --success:#059669; --success-lt:#d1fae5;
            --warning:#d97706; --warning-lt:#fef3c7; --danger:#dc2626; --danger-lt:#fee2e2;
            --info:#0284c7; --info-lt:#e0f2fe; 
            --sidebar-w: 260px;
            --bg:#f1f5f9; --card:#fff; --border:#e2e8f0; --text-muted:#64748b; --text-dark:#0f172a;
        }
        body{font-family:'Plus Jakarta Sans',sans-serif;background:var(--bg);color:var(--text-dark);min-height:100vh;}

        .main-wrap { margin-left: var(--sidebar-w); min-height: 100vh; transition: all 0.3s ease; }
        .topbar{background:white;border-bottom:1px solid var(--border);padding:.875rem 1.75rem;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:100;}
        .topbar-left h1{font-size:1.1rem;font-weight:700;margin:0;}
        .content{padding:1.75rem;}

        /* STATS */
        .stat-card{background:var(--card);border-radius:16px;border:1px solid var(--border);padding:1.4rem 1.5rem;}
        .stat-card .stat-value{font-size:1.8rem;font-weight:800;line-height:1;}
        .stat-card .stat-label{font-size:.75rem;color:var(--text-muted);font-weight:600;text-transform:uppercase;}

        /* TABLE */
        .filter-card, .table-card{background:var(--card);border-radius:16px;border:1px solid var(--border);padding:1.25rem 1.5rem;margin-bottom:1.5rem;}
        .table-card{padding:0; overflow:hidden;}
        .table thead th{background:#f8fafc;font-size:.7rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;padding:1rem;border-bottom:2px solid var(--border);}
        .table tbody td{padding:1rem;font-size:.85rem;vertical-align:middle;}
        
        /* BADGES */
        .badge-status{padding:.35rem .75rem;border-radius:999px;font-size:.7rem;font-weight:700;}
        .badge-menunggu{background:var(--warning-lt);color:var(--warning);}
        .badge-proses{background:var(--info-lt);color:var(--info);}
        .badge-selesai{background:var(--success-lt);color:var(--success);}

        /* PRINT STYLES */
        @media print {
            @page { size: landscape; margin: 1cm; }
            .sidebar, .topbar, .filter-card, .no-print, .btn, .sidebar-overlay { display: none !important; }
            .main-wrap { margin-left: 0 !important; }
            body { background: white !important; }
            .content { padding: 0 !important; }
            .table-card { border: none !important; }
            .table th { background-color: #eee !important; color: black !important; -webkit-print-color-adjust: exact; }
            .print-header { display: block !important; border-bottom: 3px solid #000; margin-bottom: 20px; padding-bottom: 10px; }
            .print-footer { display: block !important; margin-top: 30px; }
            .badge-status { border: 1px solid #ccc !important; color: black !important; background: transparent !important; }
        }
        .print-header, .print-footer { display: none; }
    </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main-wrap">
    <header class="topbar no-print">
        <div class="topbar-left">
            <h1>Laporan Aspirasi</h1>
            <p class="text-muted mb-0" style="font-size: .8rem;">Manajemen rekapitulasi laporan</p>
        </div>
        <button type="button" onclick="window.print()" class="btn btn-success btn-sm">
            <i class="bi bi-printer me-2"></i>Cetak Laporan
        </button>
    </header>

    <main class="content">
        <div class="print-header text-center">
            <h2 class="mb-1">REKAPITULASI ASPIRASI SISWA</h2>
            <h5 class="mb-2">Sarana dan Prasarana Sekolah</h5>
            <p class="mb-0 small text-muted">
                Periode: <?= $filter_bulan ?: 'Semua Waktu' ?> | 
                Kategori: <?= $filter_kategori ?: 'Semua' ?> | 
                Status: <?= $filter_status ?: 'Semua' ?>
            </p>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-label">Total</div>
                    <div class="stat-value text-primary"><?= $count_all ?></div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-label">Menunggu</div>
                    <div class="stat-value text-warning"><?= $count_menunggu ?></div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-label">Proses</div>
                    <div class="stat-value text-info"><?= $count_proses ?></div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-label">Selesai</div>
                    <div class="stat-value text-success"><?= $count_selesai ?></div>
                </div>
            </div>
        </div>

        <div class="filter-card no-print">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Bulan</label>
                    <input type="month" class="form-control" name="filter_bulan" value="<?= $filter_bulan ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Kategori</label>
                    <select class="form-select" name="filter_kat">
                        <option value="">Semua Kategori</option>
                        <?php 
                        $kats = mysqli_query($koneksi,"SELECT * FROM Kategori"); 
                        while($k=mysqli_fetch_array($kats)): 
                        ?>
                        <option value="<?= $k['id_kategori'] ?>" <?= ($filter_kategori==$k['id_kategori'])?'selected':'' ?>>
                            <?= htmlspecialchars($k['ket_kategori']) ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Status</label>
                    <select class="form-select" name="filter_status">
                        <option value="">Semua Status</option>
                        <option value="Menunggu" <?= ($filter_status=='Menunggu')?'selected':'' ?>>Menunggu</option>
                        <option value="Proses" <?= ($filter_status=='Proses')?'selected':'' ?>>Proses</option>
                        <option value="Selesai" <?= ($filter_status=='Selesai')?'selected':'' ?>>Selesai</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">Terapkan Filter</button>
                </div>
            </form>
        </div>

        <div class="table-card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th width="50">No</th>
                            <th>Tanggal</th>
                            <th>NIS</th>
                            <th>Kategori</th>
                            <th>Lokasi</th>
                            <th>Status</th>
                            <th class="no-print">Tanggapan</th>
                            <th class="no-print text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        if (mysqli_num_rows($result) > 0): 
                            while ($row = mysqli_fetch_assoc($result)): 
                                $st = $row['status'] ?? 'Menunggu';
                                $bc = match(strtolower($st)){ 'proses'=>'badge-proses','selesai'=>'badge-selesai',default=>'badge-menunggu' };
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                            <td><span class="fw-bold"><?= $row['nis'] ?></span></td>
                            <td><?= htmlspecialchars($row['ket_kategori']) ?></td>
                            <td><?= htmlspecialchars($row['lokasi']) ?></td>
                            <td><span class="badge-status <?= $bc ?>"><?= $st ?></span></td>
                            <td class="no-print">
                                <small class="text-muted"><?= $row['feedback'] ? substr($row['feedback'], 0, 30).'...' : '-' ?></small>
                            </td>
                            <td class="no-print text-center">
                                <button class="btn btn-light btn-sm border" onclick='showDetail(<?= json_encode($row) ?>)'>
                                    <i class="bi bi-eye"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr><td colspan="8" class="text-center p-5 text-muted">Data tidak ditemukan</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="print-footer">
            <div class="row">
                <div class="col-8"></div>
                <div class="col-4 text-center">
                    <p>Dicetak pada: <?= date('d F Y') ?></p>
                    <p>Mengetahui, <br><strong>Administrator Sistem</strong></p>
                    <br><br><br>
                    <p>__________________________</p>
                </div>
            </div>
        </div>
    </main>
</div>

<div class="modal fade" id="modalDetail" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Detail Laporan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="small text-muted d-block text-uppercase fw-bold">Laporan / Deskripsi</label>
                    <div class="p-3 bg-light rounded mt-1" id="d-desc"></div>
                </div>
                <div class="mb-3">
                    <label class="small text-muted d-block text-uppercase fw-bold">Tanggapan Admin</label>
                    <div class="p-3 rounded mt-1 border-start border-4 border-primary bg-light" id="d-tanggapan"></div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <a id="btn-tanggapi" href="#" class="btn btn-primary w-100">Berikan Tanggapan</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function showDetail(row) {
    document.getElementById('d-desc').textContent = row.deskripsi || 'Tidak ada deskripsi.';
    document.getElementById('d-tanggapan').textContent = row.feedback || 'Belum ada tanggapan.';
    document.getElementById('btn-tanggapi').href = 'tanggapan.php?id=' + row.id_pelaporan;
    new bootstrap.Modal(document.getElementById('modalDetail')).show();
}
</script>
</body>
</html>