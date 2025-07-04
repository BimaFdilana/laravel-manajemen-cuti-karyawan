# 📅 Aplikasi Manajemen Cuti Karyawan

Repositori ini merupakan implementasi **Aplikasi Manajemen Cuti Karyawan** berbasis web yang dikembangkan dengan **Laravel**. Sistem ini dirancang untuk mempermudah proses pencatatan, pemantauan, dan pelaporan data cuti di lingkungan perusahaan atau organisasi.

Aplikasi ini memiliki dua antarmuka utama: **Panel Admin** untuk pengelolaan data secara penuh dan **Halaman Jadwal Publik** yang bisa diakses oleh siapa saja untuk melihat jadwal cuti tanpa perlu login.

---

## ✨ Fitur Utama

1.  **Manajemen Cuti (CRUD)**
    Admin dapat dengan mudah menambah, melihat, mengubah, dan menghapus data cuti karyawan.

2.  **Dashboard Interaktif**
    Menyajikan ringkasan data penting secara visual, seperti jumlah karyawan yang sedang cuti hari ini, total cuti terdaftar, dan daftar pengajuan terbaru.

3.  **Notifikasi Otomatis**
    Admin menerima notifikasi di dalam aplikasi untuk setiap cuti baru yang didaftarkan. Terdapat juga sistem terjadwal untuk memberitahu saat cuti seorang karyawan dimulai atau berakhir.

4.  **Filter & Pencarian Lanjutan**
    Mempermudah pencarian data cuti spesifik berdasarkan **nama karyawan**, **bulan**, dan **tahun**.

5.  **Ekspor ke Excel**
    Fitur untuk mengekspor data cuti (baik semua data maupun yang sudah difilter) ke dalam format file `.xlsx` dengan *style* dan format yang rapi.

6.  **Jadwal Publik**
    Sebuah halaman *read-only* yang dapat diakses siapa saja untuk melihat jadwal cuti karyawan. Halaman ini juga dilengkapi dengan fitur filter yang interaktif.

7.  **Manajemen Data Master**
    Admin dapat mengelola data master seperti **Ruangan/Bagian** yang akan digunakan pada form pengajuan cuti.

---

## 🚀 Cara Menjalankan Project Secara Lokal

Berikut adalah langkah-langkah untuk menjalankan aplikasi di lingkungan pengembangan Anda:

### 1. Clone Repository

```bash
git clone https://github.com/BimaFdilana/sistem-cuti-karyawan.git
cd sistem-cuti-karyawan
```
### 2. Install Dependency
Pastikan Anda memiliki Composer dan Node.js terinstal.

```bash
composer install
npm install
```

### 3. Konfigurasi .env
Salin file .env.example menjadi .env.

```bash
cp .env.example .env
```

```bash
APP_NAME="Sistem Cuti"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_sistem_cuti
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Generate Kunci Aplikasi

```bash
php artisan key:generate
```

### 5. Jalankan Migrasi & Seeder

```bash
php artisan migrate:fresh --seed
```

### 6. Jalankan Server Pengembangan

```bash
php artisan serve
```

### 7. Akses Aplikasi
Panel Admin: Buka http://localhost:8000 (akan mengarah ke halaman login).
Jadwal Publik: Buka http://localhost:8000/jadwal-cuti.

## 👥 Akun Default (Untuk Pengujian)

Admin
Email: admin@example.com (sesuaikan dengan data seeder Anda)
Password: password (sesuaikan dengan data seeder Anda)
