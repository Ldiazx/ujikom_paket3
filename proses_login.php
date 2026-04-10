<?php
session_start();
include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = $_POST['role'];
    $user_input = mysqli_real_escape_string($koneksi, $_POST['username_login']);
    $pass_input = $_POST['password_login'];

    if ($role == "siswa") {
        $query = "SELECT * FROM siswa WHERE nis = '$user_input' OR username = '$user_input'";
        $result = mysqli_query($koneksi, $query);

        if (mysqli_num_rows($result) > 0) {
            $data = mysqli_fetch_assoc($result);
            if ($pass_input == $data['password']) {
                $_SESSION['login'] = true;
                $_SESSION['role']  = 'siswa';
                $_SESSION['nis']   = $data['nis'];
                // MASUK KE FOLDER SISWA
                header("Location: siswa/halaman_siswa.php");
                exit();
            } else {
                echo "<script>alert('Password Siswa Salah!'); window.location='index.php';</script>";
            }
        } else {
            echo "<script>alert('NIS/Username tidak ditemukan!'); window.location='index.php';</script>";
        }

    } else if ($role == "admin") {
        $query = "SELECT * FROM Admin WHERE username = '$user_input'";
        $result = mysqli_query($koneksi, $query);

        if (mysqli_num_rows($result) > 0) {
            $data = mysqli_fetch_assoc($result);
            if ($pass_input == $data['password']) {
                $_SESSION['login']    = true;
                $_SESSION['role']     = 'admin';
                $_SESSION['username'] = $data['username'];
                // MASUK KE FOLDER ADMIN
                header("Location: admin/halaman_admin.php");
                exit();
            } else {
                echo "<script>alert('Password salah!'); window.location='index.php';</script>";
            }
        } else {
            echo "<script>alert('Admin tidak ditemukan!'); window.location='index.php';</script>";
        }
    }
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

?>