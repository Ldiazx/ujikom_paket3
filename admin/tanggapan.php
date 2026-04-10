<?php
session_start();
include '../koneksi.php';

// Proteksi Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

$id_pelaporan = $_GET['id'];

// --- PERBAIKAN QUERY: Join dengan Aspirasi agar kolom 'status' tidak Undefined ---
$query = "SELECT ia.*, k.ket_kategori, a.status, a.feedback 
          FROM Input_Aspirasi ia 
          JOIN Kategori k ON ia.id_kategori = k.id_kategori 
          LEFT JOIN Aspirasi a ON ia.id_kategori = a.id_kategori 
          WHERE ia.id_pelaporan = '$id_pelaporan'";

$result = mysqli_query($koneksi, $query);
$d = mysqli_fetch_array($result);

// Jika status masih kosong di DB, kasih default "Menunggu" agar tidak error
$status_sekarang = $d['status'] ?? 'Menunggu';

// Proses Simpan Tanggapan
if (isset($_POST['simpan'])) {
    $id_kat   = $d['id_kategori'];
    $status   = $_POST['status'];
    $feedback = mysqli_real_escape_string($koneksi, $_POST['feedback']);

    $cek = mysqli_query($koneksi, "SELECT * FROM Aspirasi WHERE id_kategori = '$id_kat'");
    
    if (mysqli_num_rows($cek) > 0) {
        mysqli_query($koneksi, "UPDATE Aspirasi SET status='$status', feedback='$feedback' WHERE id_kategori='$id_kat'");
    } else {
        mysqli_query($koneksi, "INSERT INTO Aspirasi (status, id_kategori, feedback) VALUES ('$status', '$id_kat', '$feedback')");
    }
    
    echo "<script>alert('Tanggapan berhasil disimpan!'); window.location='halaman_admin.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beri Tanggapan - AspirasiKu</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f8fafc; margin: 0; padding: 20px; color: #1e293b; }
        .card { 
            background: white; padding: 30px; border-radius: 16px; 
            max-width: 600px; margin: auto; 
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1); border: 1px solid #e2e8f0;
        }
        h3 { margin-top: 0; color: #0f172a; font-size: 1.25rem; text-align: center; }
        
        /* Foto Box */
        .foto-container { width: 100%; margin-bottom: 20px; text-align: center; }
        .foto-aspirasi { 
            width: 100%; max-height: 300px; object-fit: contain; 
            border-radius: 12px; border: 4px solid #f1f5f9;
        }

        .info-section { background: #f1f5f9; padding: 15px; border-radius: 10px; margin-bottom: 20px; font-size: 0.9rem; }
        .info-section p { margin: 8px 0; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px; }
        
        label { display: block; margin-bottom: 8px; font-weight: 700; font-size: 0.75rem; color: #64748b; text-transform: uppercase; }
        textarea, select { 
            width: 100%; padding: 12px; margin-bottom: 20px; 
            border-radius: 10px; border: 2px solid #e2e8f0; font-family: inherit;
        }
        
        .btn-group { display: flex; gap: 10px; }
        button { background: #3b82f6; color: white; border: none; padding: 12px; width: 100%; border-radius: 10px; font-weight: 700; cursor: pointer; }
        .btn-back { background: #f1f5f9; color: #64748b; text-decoration: none; text-align: center; padding: 12px; width: 100%; border-radius: 10px; font-weight: 700; }
    </style>
</head>
<body>

<div class="card">
    <h3>Tanggapi Aspirasi <span style="color: #3b82f6;">#<?php echo $id_pelaporan; ?></span></h3>
    
    <div class="foto-container">
        <label>Bukti Foto Kerusakan:</label>
        <?php if(!empty($d['foto'])): ?>
            <a href="../img/<?php echo $d['foto']; ?>" target="_blank">
                <img src="../img/<?php echo $d['foto']; ?>" class="foto-aspirasi" alt="Foto Aspirasi">
            </a>
            <p style="font-size: 0.7rem; color: #94a3b8; margin-top: 5px;">*Klik gambar untuk memperbesar</p>
        <?php else: ?>
            <div style="padding: 20px; background: #f8fafc; border-radius: 10px; color: #94a3b8;">Tidak ada foto terlampir</div>
        <?php endif; ?>
    </div>

    <div class="info-section">
        <p><strong>👤 Pelapor:</strong> NIS <?php echo $d['nis']; ?></p>
        <p><strong>📂 Kategori:</strong> <?php echo $d['ket_kategori']; ?></p>
        <p><strong>📍 Lokasi:</strong> <?php echo $d['lokasi']; ?></p>
        <p><strong>📝 Laporan:</strong> "<?php echo $d['ket']; ?>"</p>
    </div>

    <form method="POST">
        <label>Update Status</label>
        <select name="status">
            <option value="Menunggu" <?php if($status_sekarang == "Menunggu") echo "selected"; ?>>Menunggu</option>
            <option value="Proses" <?php if($status_sekarang == "Proses") echo "selected"; ?>>Proses</option>
            <option value="Selesai" <?php if($status_sekarang == "Selesai") echo "selected"; ?>>Selesai</option>
        </select>

        <label>Feedback Admin</label>
        <textarea name="feedback" rows="4" placeholder="Berikan tanggapan untuk siswa..." required><?php echo $d['feedback'] ?? ''; ?></textarea>

        <div class="btn-group">
            <a href="halaman_admin.php" class="btn-back">Batal</a>
            <button type="submit" name="simpan">Simpan Perubahan</button>
        </div>
    </form>
</div>

</body>
</html>