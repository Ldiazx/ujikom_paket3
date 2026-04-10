<?php
include 'koneksi.php';

if (isset($_POST['kirim'])) {
    $nis = $_POST['nis'];
    $id_kat = $_POST['id_kategori'];
    $lokasi = $_POST['lokasi'];
    $ket = $_POST['ket'];
    $tanggal = date("Y-m-d");

    // Logika Upload Foto
    $foto = $_FILES['foto']['name'];
    $tmp = $_FILES['foto']['tmp_name'];
    
    $fotobaru = date('dmYHis').$foto;
    $path = "../img/".$fotobaru; // Folder img ada di root, jadi langsung panggil

    if (move_uploaded_file($tmp, $path)) {
        $query = "INSERT INTO Input_Aspirasi (nis, id_kategori, lokasi, ket, foto, tanggal) 
                  VALUES ('$nis', '$id_kat', '$lokasi', '$ket', '$fotobaru', '$tanggal')";
        $sql = mysqli_query($koneksi, $query);

        if ($sql) {
            echo "<script>alert('Laporan Berhasil Terkirim!'); window.location='siswa/halaman_siswa.php';</script>";
        } else {
            echo "<script>alert('Gagal simpan ke database'); window.location='siswa/halaman_siswa.php';</script>";
        }
    } else {
        echo "<script>alert('Gagal Upload Gambar!'); window.location='siswa/halaman_siswa.php';</script>";
    }
}
?>