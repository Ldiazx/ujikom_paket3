<?php
$host     = "localhost";
$username = "root";
$password = "";
$database = "ukk-paket3";

    $koneksi = mysqli_connect($host, $username, $password, $database);

    function cekKoneksi($conn) {
        if (!$conn) {
            die("Koneksi Database Gagal: " . mysqli_connect_error());
        }
    }

    cekKoneksi($koneksi);

    date_default_timezone_set('Asia/Jakarta');
    ?>