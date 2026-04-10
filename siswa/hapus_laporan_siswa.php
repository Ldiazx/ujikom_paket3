<?php
session_start();
include '../koneksi.php';

// 1. Keamanan: Pastikan user login sebagai siswa
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'siswa') {
    header("Location: index.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['id']);
    $nis_siswa = $_SESSION['nis'];

    // 2. Validasi: Ambil data untuk cek kepemilikan dan status
    // Kita join ke tabel Aspirasi untuk melihat apakah statusnya sudah berubah atau belum
    $query_cek = "SELECT ia.foto, a.status 
                  FROM Input_Aspirasi ia 
                  LEFT JOIN Aspirasi a ON ia.id_pelaporan = a.id_aspirasi 
                  WHERE ia.id_pelaporan = '$id' AND ia.nis = '$nis_siswa'";
    
    $cek = mysqli_query($koneksi, $query_cek);
    $data = mysqli_fetch_assoc($cek);

    if ($data) {
        $status = $data['status'] ?? 'Menunggu';

        // 3. Aturan: Hanya boleh hapus jika status masih 'Menunggu'
        if ($status == 'Menunggu') {
            
            if (!empty($data['foto']) && file_exists("../img/" . $data['foto'])) {
                unlink("../img/" . $data['foto']);
            }
            
            // Hapus data di tabel terkait
            // Jika ada relasi ON DELETE CASCADE di database, cukup hapus Input_Aspirasi
            mysqli_query($koneksi, "DELETE FROM Aspirasi WHERE id_aspirasi = '$id'");
            $delete = mysqli_query($koneksi, "DELETE FROM Input_Aspirasi WHERE id_pelaporan = '$id'");

            if ($delete) {
                $_SESSION['pesan'] = "Laporan berhasil dihapus!";
            } else {
                $_SESSION['pesan'] = "Gagal menghapus data dari database.";
            }
        } else {
            $_SESSION['pesan'] = "Laporan tidak bisa dihapus karena sedang dalam proses/selesai.";
        }
    } else {
        $_SESSION['pesan'] = "Data tidak ditemukan atau Anda tidak memiliki akses.";
    }
}

header("Location: halaman_siswa.php");
exit();