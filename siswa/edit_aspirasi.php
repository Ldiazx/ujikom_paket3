<?php
session_start();
include '../koneksi.php';

// Proteksi Login
if (!isset($_SESSION['nis'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];
$nis_user = $_SESSION['nis'];

// Ambil data dan pastikan ini milik siswa yang login
$query = mysqli_query($koneksi, "SELECT * FROM Input_Aspirasi WHERE id_pelaporan = '$id' AND nis = '$nis_user'");
$d = mysqli_fetch_array($query);

// Jika ID tidak ditemukan atau milik orang lain, tendang balik
if (!$d) {
    header("Location: halaman_siswa.php");
    exit();
}

if(isset($_POST['update'])){
    $lokasi = mysqli_real_escape_string($koneksi, $_POST['lokasi']);
    $ket = mysqli_real_escape_string($koneksi, $_POST['ket']);
    
    mysqli_query($koneksi, "UPDATE Input_Aspirasi SET lokasi='$lokasi', ket='$ket' WHERE id_pelaporan='$id'");
    echo "<script>alert('Laporan berhasil diperbarui!'); window.location='halaman_siswa.php';</script>";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Laporan - AspirasiKu</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body { 
            font-family: 'Inter', sans-serif; 
            background: #f8fafc; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            min-height: 100vh; 
            margin: 0; 
            color: #1e293b;
        }
        .card { 
            background: white; 
            width: 100%;
            max-width: 450px; 
            padding: 32px; 
            border-radius: 20px; 
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            border: 1px solid #e2e8f0;
        }
        .header { margin-bottom: 24px; text-align: center; }
        .header h3 { margin: 0; font-size: 1.5rem; font-weight: 700; color: #0f172a; }
        .header p { color: #64748b; font-size: 0.875rem; margin-top: 8px; }

        /* Preview Foto yang sudah ada */
        .photo-preview {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 12px;
            margin-bottom: 20px;
            border: 1px solid #e2e8f0;
        }

        label { 
            display: block; 
            margin-bottom: 6px; 
            font-weight: 600; 
            font-size: 0.75rem; 
            color: #475569; 
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }

        input, textarea { 
            width: 100%; 
            padding: 12px 16px; 
            margin-bottom: 20px; 
            border: 1px solid #cbd5e1; 
            border-radius: 12px; 
            font-family: inherit;
            font-size: 0.95rem;
            transition: all 0.2s;
            outline: none;
        }

        input:focus, textarea:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        .btn-group { display: flex; flex-direction: column; gap: 12px; }

        button { 
            width: 100%; 
            padding: 14px; 
            background: #3b82f6; 
            color: white; 
            border: none; 
            border-radius: 12px; 
            font-weight: 700; 
            font-size: 1rem;
            cursor: pointer; 
            transition: background 0.2s;
        }
        button:hover { background: #2563eb; }

        .btn-cancel { 
            text-align: center; 
            text-decoration: none; 
            color: #64748b; 
            font-size: 0.875rem; 
            font-weight: 500;
            padding: 8px;
        }
        .btn-cancel:hover { color: #1e293b; }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <h3>Edit Laporan</h3>
            <p>ID Laporan: <span style="color: #3b82f6; font-weight: 600;">#<?php echo $id; ?></span></p>
        </div>

        <img src="../img/<?php echo $d['foto']; ?>" alt="Foto Laporan" class="photo-preview">

        <form method="POST">
            <label>Lokasi Kejadian</label>
            <input type="text" name="lokasi" value="<?php echo htmlspecialchars($d['lokasi']); ?>" placeholder="Misal: Kantin, Kelas 10..." required>
            
            <label>Deskripsi Kerusakan / Keluhan</label>
            <textarea name="ket" rows="4" placeholder="Jelaskan secara detail..." required><?php echo htmlspecialchars($d['ket']); ?></textarea>
            
            <div class="btn-group">
                <button type="submit" name="update">Simpan Perubahan</button>
                <a href="halaman_siswa.php" class="btn-cancel">Batal & Kembali</a>
            </div>
        </form>
    </div>
</body>
</html>