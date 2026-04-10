Tentu, ini adalah draf **README.md** yang profesional dan lengkap untuk proyek **AspirasiKu (Ujikom Paket 3)** kamu. File ini sangat penting agar penguji atau orang lain tahu cara menjalankan aplikasi yang sudah kamu buat.

---

# 📝 AspirasiKu - Sistem Pengaduan Siswa (Ujikom Paket 3)

**AspirasiKu** adalah aplikasi berbasis web yang dirancang untuk memudahkan siswa dalam menyampaikan aspirasi, laporan kerusakan, atau keluhan terkait fasilitas sekolah secara digital. Proyek ini dibangun untuk memenuhi tugas **Ujikom Paket 3**.

---

## 🚀 Fitur Utama

### 👨‍🎓 Fitur Siswa
* **Registrasi & Login**: Keamanan akses menggunakan akun NIS.
* **Kirim Aspirasi**: Melaporkan keluhan lengkap dengan deskripsi, lokasi, kategori, dan bukti foto.
* **Riwayat Laporan**: Melihat daftar aspirasi yang pernah dikirim beserta statusnya.
* **Update & Hapus**: Siswa dapat mengedit atau menghapus laporan selama statusnya masih **"Menunggu"**.

### 👨‍💼 Fitur Admin
* **Dashboard Statistik**: Melihat ringkasan jumlah laporan.
* **Manajemen Kategori**: Menambah, mengubah, atau menghapus kategori fasilitas.
* **Tanggapan Admin**: Memberikan feedback (balasan) dan mengubah status laporan (Menunggu -> Proses -> Selesai).
* **Manajemen Data**: Mengelola data siswa dan laporan yang masuk.

---

## 🛠️ Teknologi yang Digunakan
* **Bahasa Pemrograman**: PHP 8.x
* **Database**: MySQL / MariaDB
* **Styling**: CSS3 (Inter Fonts, Custom UI Components)
* **Server**: Laragon / XAMPP (Local) & InfinityFree (Hosting)
* **Version Control**: Git & GitHub

---

## 📂 Struktur Folder
```text
ujikom-paket3/
├── admin/            # Halaman manajemen admin
├── siswa/            # Halaman operasional siswa
├── img/              # Penyimpanan foto bukti aspirasi
├── koneksi.php       # Konfigurasi database
├── login.php         # Proses autentikasi
└── index.php         # Halaman utama / Welcome page
```

---

## ⚙️ Cara Instalasi (Lokal)

1. **Clone Repository**
   ```bash
   git clone https://github.com/Ldiazx/ujikom_paket3.git
   ```
2. **Siapkan Database**
   * Buka `phpMyAdmin`.
   * Buat database baru dengan nama `ujikom_paket3`.
   * Import file `.sql` (jika ada) ke dalam database tersebut.
3. **Konfigurasi Koneksi**
   * Buka file `koneksi.php`.
   * Sesuaikan `host`, `user`, `pass`, dan `dbname` dengan settingan Laragon/XAMPP kamu.
4. **Jalankan Aplikasi**
   * Pindahkan folder ke `C:/laragon/www/` atau `C:/xampp/htdocs/`.
   * Akses melalui browser di `http://localhost/ujikom-paket3`.

---

## 🌐 Deployment (InfinityFree)
Jika kamu mengakses versi live, pastikan:
1. File `.htaccess` sudah dikonfigurasi (opsional).
2. Folder `img/` memiliki izin akses (CHMOD 777) agar upload foto lancar.
3. **Penting**: Nama tabel di database harus **huruf kecil semua** karena server Linux bersifat *case-sensitive*.

---

## 🧑‍💻 Penulis
* **Nama**: Diaz (Ldiazx)
* **Project**: Ujikom Paket 3 - Sistem Aspirasi Siswa

---

### Cara Pasang README ini di VS Code:
1. Buat file baru bernama `README.md` di folder utama proyek kamu.
2. Copy-Paste teks di atas ke dalam file tersebut.
3. **Save**, lalu lakukan **Push** lagi ke GitHub:
   ```bash
   git add README.md
   git commit -m "Add README file"
   git push origin main
   ```
