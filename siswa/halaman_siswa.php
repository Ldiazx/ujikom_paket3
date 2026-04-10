<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'siswa') {
    header("Location: index.php");
    exit();
}

$nis_user = $_SESSION['nis'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Siswa Dashboard | AspirasiKu</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    
    <style>
        :root { --primary: #3b82f6; --bg: #f8fafc; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: var(--bg); color: #1e293b; }
        .navbar { background: rgba(255, 255, 255, 0.8) !important; backdrop-filter: blur(10px); border-bottom: 1px solid #e2e8f0; padding: 15px 0; }
        .card-form { background: white; border-radius: 24px; padding: 25px; border: 1px solid #e2e8f0; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05); position: sticky; top: 100px; }
        .report-card { background: white; border-radius: 20px; padding: 20px; margin-bottom: 20px; border: 1px solid #e2e8f0; transition: all 0.3s ease; display: flex; align-items: flex-start; gap: 15px; }
        .report-card:hover { transform: translateY(-3px); box-shadow: 0 12px 20px -5px rgba(0,0,0,0.08); }
        .img-report { width: 100%; height: 150px; object-fit: cover; border-radius: 15px; cursor: pointer; }
        .status-badge { padding: 5px 12px; border-radius: 8px; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; }
        .st-menunggu { background: #fef3c7; color: #92400e; }
        .st-proses { background: #e0f2fe; color: #075985; }
        .st-selesai { background: #dcfce7; color: #166534; }
        .admin-reply { background: #f1f5f9; border-left: 4px solid var(--primary); padding: 12px 15px; border-radius: 0 12px 12px 0; margin-top: 15px; font-size: 0.85rem; }
        .form-check-input { width: 22px; height: 22px; cursor: pointer; border: 2px solid #cbd5e1; }
    </style>
</head>
<body>

<nav class="navbar sticky-top">
    <div class="container d-flex justify-content-between">
        <a class="navbar-brand fw-bold text-primary" href="#">ASPIRASIKU</a>
        <div class="d-flex align-items-center gap-3">
            <span class="small fw-bold text-muted">Halo, <?= $nis_user ?></span>
            <a href="../logout.php" class="btn btn-outline-danger btn-sm fw-bold px-3 rounded-pill">Keluar</a>
        </div>
    </div>
</nav>

<div class="container my-5">
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card-form" data-aos="fade-right">
                <h5 class="fw-bold mb-3">Buat Laporan Baru</h5>
                <form action="proses_aspirasi.php" method="POST" enctype="multipart/form-data" id="formAspirasi">
                    <input type="hidden" name="nis" value="<?= $nis_user ?>">
                    <div class="mb-3">
                        <label class="fw-bold small text-muted text-uppercase">Kategori</label>
                        <select name="id_kategori" class="form-select border-0 bg-light" required>
                            <?php
                            $kat = mysqli_query($koneksi, "SELECT * FROM Kategori");
                            while($k = mysqli_fetch_array($kat)) echo "<option value='$k[id_kategori]'>$k[ket_kategori]</option>";
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold small text-muted text-uppercase">Lokasi</label>
                        <input type="text" name="lokasi" class="form-control border-0 bg-light" placeholder="Lokasi kejadian" required>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold small text-muted text-uppercase">Foto Bukti</label>
                        <input type="file" name="foto" class="form-control border-0 bg-light" accept="image/*" required>
                    </div>
                    <div class="mb-4">
                        <label class="fw-bold small text-muted text-uppercase">Deskripsi</label>
                        <textarea name="ket" rows="4" class="form-control border-0 bg-light" placeholder="Detail masalah..." required></textarea>
                    </div>
                    <button type="submit" name="kirim" class="btn btn-primary w-100 fw-bold shadow-sm">Kirim Aspirasi</button>
                </form>
            </div>
        </div>

        <div class="col-lg-8">
            <form id="formBulkDelete" action="hapus_laporan_siswa.php" method="POST">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold m-0">Riwayat Laporan Kamu</h5>
                    <button type="button" id="btnDeleteSelected" class="btn btn-danger btn-sm fw-bold px-3 rounded-pill" style="display: none;">
                        🗑️ Hapus Terpilih (<span id="countCheck">0</span>)
                    </button>
                </div>
                
                <?php
                $query = "SELECT ia.*, k.ket_kategori, a.status, a.feedback 
                          FROM Input_Aspirasi ia
                          JOIN Kategori k ON ia.id_kategori = k.id_kategori
                          LEFT JOIN Aspirasi a ON ia.id_pelaporan = a.id_aspirasi
                          WHERE ia.nis = '$nis_user'
                          ORDER BY ia.id_pelaporan DESC";
                
                $result = mysqli_query($koneksi, $query);
                if(mysqli_num_rows($result) > 0):
                    while($row = mysqli_fetch_array($result)):
                        $st = $row['status'] ?? 'Menunggu';
                        $st_class = "st-" . strtolower($st);
                        $foto = !empty($row['foto']) ? "../img/".$row['foto'] : "https://via.placeholder.com/300x180";
                ?>
                
                <div class="report-card" data-aos="fade-up">
                    <div class="pt-2">
                        <?php if($st == 'Menunggu'): ?>
                            <input type="checkbox" name="id_laporan[]" value="<?= $row['id_pelaporan'] ?>" class="form-check-input select-report">
                        <?php else: ?>
                            <input type="checkbox" disabled class="form-check-input bg-light">
                        <?php endif; ?>
                    </div>
                    <div class="row g-3 w-100">
                        <div class="col-md-4">
                            <img src="<?= $foto ?>" class="img-report" onclick="window.open(this.src)">
                        </div>
                        <div class="col-md-8">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <span class="text-primary fw-bold small">#<?= $row['id_pelaporan'] ?></span>
                                    <h6 class="fw-bold m-0"><?= $row['ket_kategori'] ?></h6>
                                </div>
                                <span class="status-badge <?= $st_class ?>"><?= $st ?></span>
                            </div>
                            <p class="small text-muted mb-2">📍 <?= $row['lokasi'] ?> &bull; <?= date('d M Y', strtotime($row['tanggal'])) ?></p>
                            <p class="mb-0 small"><?= $row['ket'] ?></p>

                            <?php if(!empty($row['feedback'])): ?>
                                <div class="admin-reply"><b>BALASAN:</b> <?= $row['feedback'] ?></div>
                            <?php endif; ?>

                            <div class="mt-3 pt-2 border-top">
                                <?php if($st == 'Menunggu'): ?>
                                    <a href="edit_aspirasi.php?id=<?= $row['id_pelaporan'] ?>" class="btn btn-link btn-sm p-0 text-decoration-none fw-bold me-3">✏️ Edit</a>
                                    <a href="javascript:void(0)" onclick="confirmSingleDelete('<?= $row['id_pelaporan'] ?>')" class="btn btn-link btn-sm p-0 text-decoration-none fw-bold text-danger">🗑️ Hapus</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; else: ?>
                    <div class="text-center py-5 bg-white rounded-4 border">Laporan tidak ditemukan.</div>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    AOS.init({ duration: 800, once: true });

    // Hitung Checkbox
    $('.select-report').on('change', function() {
        let count = $('.select-report:checked').length;
        $('#countCheck').text(count);
        count > 0 ? $('#btnDeleteSelected').fadeIn() : $('#btnDeleteSelected').fadeOut();
    });

    // Hapus Satuan
    function confirmSingleDelete(id) {
        Swal.fire({
            title: 'Hapus Laporan?',
            text: "Data ini akan dihapus permanen.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus'
        }).then((result) => {
            if (result.isConfirmed) window.location.href = "hapus_laporan_siswa.php?id=" + id;
        });
    }

    // Hapus Masal
    $('#btnDeleteSelected').click(function() {
        Swal.fire({
            title: 'Hapus yang dipilih?',
            text: "Laporan yang ditandai akan dihapus.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus Semua'
        }).then((result) => {
            if (result.isConfirmed) $('#formBulkDelete').submit();
        });
    });
</script>
</body>
</html>