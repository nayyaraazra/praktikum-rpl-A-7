# KULAAN.id - Katalog Digital UMKM Kelurahan Jebres

[![Laravel Version](https://img.shields.io/badge/Laravel-11.x-red.svg?style=flat-square&logo=laravel)](https://laravel.com)
[![Vue Version](https://img.shields.io/badge/Vue.js-3.x-green.svg?style=flat-square&logo=vue.js)](https://vuejs.org)
[![Tailwind CSS Version](https://img.shields.io/badge/Tailwind_CSS-4.0-blue.svg?style=flat-square&logo=tailwindcss)](https://tailwindcss.com)
[![PHP Version](https://img.shields.io/badge/PHP-8.3-777BB4.svg?style=flat-square&logo=php)](https://php.net)
[![MySQL Version](https://img.shields.io/badge/MySQL-8.x-4479A1.svg?style=flat-square&logo=mysql)](https://mysql.com)
[![Build Status](https://github.com/nayyaraazra/praktikum-rpl-A-7/actions/workflows/laravel.yml/badge.svg)](https://github.com/nayyaraazra/praktikum-rpl-A-7/actions)

## 📌 Tentang Proyek

**KULAAN.id** adalah aplikasi katalog digital berbasis web yang dirancang khusus untuk menghubungkan pelaku UMKM di Kelurahan Jebres, Kecamatan Jebres, Kota Surakarta dengan calon pembeli secara efisien dan terstruktur.

### Latar Belakang & Permasalahan
Saat ini, pelaku UMKM lokal di Kelurahan Jebres masih mengandalkan platform tidak terstruktur seperti WhatsApp dan media sosial untuk memasarkan produk mereka. Hal ini menimbulkan beberapa kendala:
1. **Informasi Tidak Terstruktur:** Informasi produk (harga, stok, detail) tidak tersusun rapi dan sulit ditemukan kembali oleh calon pembeli.
2. **Visibilitas Rendah:** Produk lokal sulit bersaing dan kurang dikenal oleh pendatang maupun masyarakat setempat karena tidak adanya katalog terpusat.
3. **Pemesanan Tidak Efisien:** Alur pemesanan dan interaksi penjual-pembeli berjalan lambat dan terbatas.

### Solusi
**KULAAN.id** hadir sebagai platform katalog digital terpusat yang menawarkan:
- **Pencarian Semantik (Semantic Search):** Integrasi Google Gemini API dan Elasticsearch untuk membantu pembeli menemukan produk dengan pencarian berbasis makna (misalnya, mengetik "oleh-oleh murah khas Solo" akan otomatis merekomendasikan produk batik atau kuliner lokal).
- **Katalog Terstruktur:** Informasi produk, deskripsi toko, kategori, dan harga tersaji secara rapi dan mudah dinavigasi.
- **Pemesanan & Kontak Cepat:** Form pemesanan sederhana yang terintegrasi dengan WhatsApp untuk memudahkan komunikasi transaksi langsung.
- **Verifikasi Toko & Dashboard:** Dashboard bagi pemilik UMKM untuk mengelola produk dan pesanan, serta dashboard Admin Kelurahan untuk melakukan validasi keabsahan data toko.

---

## 👥 Anggota Kelompok (Kelompok 7)
| Nomor | NIM        | Nama                         | Peran / Deskripsi |
|-------|------------|------------------------------|-------------------|
| 1     | L0124006   | Benedhictus Kevin Doni B.E.  | Developer |
| 2     | L0124119   | Shaira Masyhita Putri Hatala | Developer |
| 3     | L0124120   | Silvi Amalia                 | Developer |
| 4     | L0124138   | Nayyara Aqila Azra           | Developer |

---

## 🛠️ Teknologi yang Digunakan
| Kategori | Teknologi | Deskripsi |
|----------|-----------|-----------|
| **Frontend** | Vue.js 3, Vite, Tailwind CSS 4, Pinia, Vue Router | Kerangka kerja frontend responsif, modern, dan interaktif. |
| **Backend** | Laravel 11 / 13 | Core backend API, autentikasi (Sanctum), routing, dan MVC. |
| **Database** | MySQL 8 | Basis data relasional utama. |
| **Integrasi AI** | Google Gemini API | Digunakan untuk NLP dan Semantic Search. |
| **Search Engine** | Elasticsearch | Mesin pencari performa tinggi untuk pencarian produk. |
| **Lainnya** | Composer, Node.js & npm, Git | Package manager dan version control. |

---

## 📂 Struktur Proyek
```
praktikum-rpl-A-7/
├── docs/                 # [DOKUMENTASI] Dokumen perencanaan proyek, srs, backlog, rancangan sistem
├── tests/                # [PENGUJIAN] Berkas automated testing untuk root level / unit testing
└── src/                  # [KODE UTAMA] Seluruh source code aplikasi (Backend & Frontend)
    ├── app/              # Logika bisnis utama Laravel (Controller, Model, Middleware, dll)
    ├── bootstrap/        # Cache sistem dan bootstrap aplikasi
    ├── config/           # File konfigurasi sistem aplikasi (Database, Mail, Auth, dll)
    ├── database/         # File Migration struktur database dan Seeders data awal/dummy
    ├── public/           # Entry point utama aplikasi (index.php) dan aset web publik (gambar, dll)
    ├── resources/        # Aset antarmuka aplikasi sebelum dikompilasi
    │   └── js/           # Sumber kode utama Vue.js
    │       ├── web/      # Komponen, halaman, dan logika khusus untuk aplikasi Web
    │       └── mobile/   # Komponen, halaman, dan logika khusus untuk aplikasi Mobile (SOON)
    ├── routes/           # Manajemen Routing (web.php untuk Web, api.php untuk API Mobile)
    ├── storage/          # Tempat menyimpan log sistem, file cache, dan file unggahan pengguna
    ├── .env              # File sensitif berisi kredensial database dan konfigurasi environment lokal
    ├── artisan           # Command-line interface (CLI) bawaan Laravel
    ├── composer.json     # Daftar dependensi PHP Laravel dan custom script setup
    ├── package.json      # Daftar dependensi JavaScript (Vue.js, Vite) dan npm script
    └── vite.config.js    # Konfigurasi utama bundling dan kompilasi aset frontend menggunakan Vite
```

---

## 🚀 Daftar Fitur & Status Implementasi

Berikut merupakan daftar lengkap fitur aplikasi berdasarkan dokumen perencanaan di folder `/docs`:

| Kode Fitur | Nama Fitur | Deskripsi | Aktor | Status | Screenshot |
|:---:|---|---|:---:|:---:|---|
| **US-01** | Register Account | Pendaftaran akun pembeli baru dengan validasi data email & nomor telepon. | Pembeli | Selesai | <img width="945" height="492" alt="image" src="https://github.com/user-attachments/assets/dfc8bed9-810b-40cc-8542-b860fd0d4225" /> |
| **US-02** | Login Account | Otentikasi pembeli menggunakan email dan password dengan perlindungan error generik. | Pembeli | Selesai | <img width="960" height="496" alt="image" src="https://github.com/user-attachments/assets/5f526077-0340-449d-b3a0-d6e7a9df4585" /> |
| **US-03** | Search Products | Pencarian produk berbasis kata kunci (semantic search engine). | Pembeli | Selesai | <img width="949" height="533" alt="image" src="https://github.com/user-attachments/assets/9260752e-5b8a-4c92-a74f-170545a235ff" /> |
| **US-04** | Filter Products | Menyaring katalog produk berdasarkan kategori dan rentang harga. | Pembeli | Selesai | *(Akan dilengkapi)* |
| **US-05** | View Product Details | Menampilkan rincian detail produk, deskripsi, harga, foto, dan informasi toko. | Pembeli | Selesai | *(Akan dilengkapi)* |
| **US-06** | Fill Order Form | Form pemesanan produk sederhana (nama, jumlah, catatan) tanpa payment gateway. | Pembeli | Selesai | *(Akan dilengkapi)* |
| **US-07** | Notifikasi Pemesanan | Notifikasi status pesanan (menunggu, dikonfirmasi, ditolak) di riwayat pemesanan. | Pembeli | Selesai | *(Akan dilengkapi)* |
| **US-08** | Register & Store Profile | Registrasi dua langkah bagi penjual: isi data akun lalu lengkapi profil toko. | Pemilik UMKM | Selesai | *(Akan dilengkapi)* |
| **US-09** | Login Account (Seller) | Otentikasi pemilik UMKM untuk masuk ke dashboard manajemen toko. | Pemilik UMKM | Selesai | *(Akan dilengkapi)* |
| **US-10** | Hubungi Penjual | Fitur kontak langsung dari halaman detail produk menggunakan tautan WhatsApp. | Pembeli | Selesai | *(Akan dilengkapi)* |
| **US-11** | Manage Store Profile | Mengelola dan memperbarui informasi profil toko (nama, kontak, alamat, jam operasional). | Pemilik UMKM | Selesai | *(Akan dilengkapi)* |
| **US-12** | Receive Orders | Melihat daftar pesanan masuk beserta rincian data pemesanan di dashboard penjual. | Pemilik UMKM | Selesai | *(Akan dilengkapi)* |
| **US-13** | Verify Store Information | Admin meninjau data pendaftaran toko dan melakukan persetujuan/penolakan verifikasi. | Admin | Selesai | *(Akan dilengkapi)* |
| **US-14** | Monitor System Activity | Dashboard ringkasan statistik aktivitas platform (jumlah toko, pesanan, dsb). | Admin | Selesai | *(Akan dilengkapi)* |

---

## ⚙️ Panduan Setup & Instalasi 
Ikuti langkah-langkah berikut untuk memasang proyek **KULAAN.id** di komputer lokal Anda dari awal.

### 📋 1. Prasyarat Sistem
Pastikan perangkat Anda sudah terpasang software-software berikut:
1. **Git**: Untuk mengunduh proyek. [Download Git](https://git-scm.com/)
   * *Cara cek di terminal:* `git --version`
2. **PHP 8.3** (atau minimal 8.2): Core runtime backend Laravel. [Download PHP](https://www.php.net/downloads)
   * *Cara cek di terminal:* `php -v`
3. **Composer 2.x**: Dependency manager untuk PHP. [Download Composer](https://getcomposer.org/)
   * *Cara cek di terminal:* `composer -V`
4. **Node.js LTS (Versi 20.x)** & **npm**: Runtime & package manager JavaScript. [Download Node.js](https://nodejs.org/)
   * *Cara cek di terminal:* `node -v` dan `npm -v`
5. **MySQL 8.x** (atau MariaDB): Untuk server database (biasanya dipaketkan dalam XAMPP/Laragon). [Download XAMPP](https://www.apachefriends.org/)
   * *Cara cek:* Jalankan service MySQL lewat Control Panel XAMPP/Laragon.

---

### 📥 2. Langkah-Langkah Instalasi

#### **Langkah 2.1: Clone Repositori**
Buka terminal (Git Bash, Command Prompt, atau PowerShell) di folder lokal Anda, kemudian jalankan:
```bash
git clone https://github.com/nayyaraazra/praktikum-rpl-A-7.git
cd praktikum-rpl-A-7
```

#### **Langkah 2.2: Pindah ke Direktori Kode Utama (`src`)**
Karena semua kode utama Laravel & Vue.js berada di dalam folder `src`, Anda wajib berpindah ke sana:
```bash
cd src
```

#### **Langkah 2.3: Konfigurasi Environment (`.env`)**
Salin file template `.env.example` menjadi `.env` dengan perintah:
* **Windows (Command Prompt / PowerShell):**
  ```powershell
  copy .env.example .env
  ```
* **Git Bash / Linux / macOS:**
  ```bash
  cp .env.example .env
  ```

#### **Langkah 2.4: Buat Database Baru di MySQL**
1. Buka browser Anda dan akses halaman database manager, misalnya **phpMyAdmin** (`http://localhost/phpmyadmin`).
2. Buat database baru dengan nama `kulaan_db`.
3. Buka file `.env` di text editor Anda (seperti VS Code) di dalam folder `src/`.
4. Sesuaikan konfigurasi database berikut dengan server lokal Anda:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=kulaan_db
   DB_USERNAME=root
   DB_PASSWORD=         # Kosongkan jika menggunakan XAMPP Windows, atau isi jika ada password
   ```

#### **Langkah 2.5: Instal Dependensi PHP Backend**
Jalankan composer untuk memasang semua library PHP Laravel:
```bash
composer install
```

#### **Langkah 2.6: Generate Kunci Keamanan Laravel**
Jalankan perintah ini untuk membuat key unik untuk keamanan sesi:
```bash
php artisan key:generate
```

#### **Langkah 2.7: Jalankan Migrasi Database & Seeder Data Uji Coba**
Jalankan migrasi untuk membuat tabel-tabel di basis data dan mengisinya dengan akun & data demo agar langsung siap digunakan:
```bash
php artisan migrate --seed
```
> **Catatan:** Apabila proses seeder meminta konfirmasi pembuatan database tambahan (`kulaan_db_test`), silakan pilih opsi persetujuan (opsional untuk testing).

#### **Langkah 2.8: Instal Dependensi JavaScript Frontend**
Jalankan perintah npm untuk mengunduh semua module JavaScript untuk Vue.js & Vite:
```bash
npm install
```

---

## 🖥️ Cara Menjalankan Aplikasi

Aplikasi KULAAN.id memerlukan server backend (Laravel API) dan frontend compiler (Vite) berjalan secara bersamaan.

### **Metode A: Menggunakan Satu Perintah (Sangat Direkomendasikan)**
Proyek ini sudah dilengkapi dengan script runner bawaan Composer. Cukup jalankan perintah berikut di folder `src/`:
```bash
composer run dev
```
Perintah ini akan secara otomatis memicu server backend Laravel (`php artisan serve`), server frontend Vite (`npm run dev`), logs, dan queue listener secara bersamaan dalam satu terminal yang terbagi.

### **Metode B: Menggunakan Dua Terminal Berbeda (Manual)**
Jika Anda ingin melihat log proses secara terpisah, Anda dapat membuka 2 jendela terminal baru di folder `src/`:

* **Terminal 1 (Backend Laravel):**
  ```bash
  php artisan serve
  ```
  *Backend akan berjalan di: `http://127.0.0.1:8000`*

* **Terminal 2 (Frontend Vue.js + Vite):**
  ```bash
  npm run dev
  ```
  *Frontend akan berjalan di: `http://localhost:5173` (atau port yang tertera pada output terminal)*

Buka tautan frontend (biasanya `http://localhost:5173` atau `http://localhost:3000`) di web browser Anda untuk mengakses aplikasi KULAAN.id!

---

## 🔑 Data Akun Demo untuk Pengujian (Uji Coba)

Setelah Anda menjalankan perintah `php artisan migrate --seed` pada **Langkah 2.7**, akun-akun berikut dapat Anda gunakan untuk langsung menguji sistem tanpa registrasi manual:

| Peran (Role) | Email Login | Password | Deskripsi / Kegunaan |
|--------------|-------------|----------|----------------------|
| **Admin Kelurahan** | `admin@kulaan.id` | `password` | Mengakses dashboard Admin untuk memantau sistem dan memverifikasi pendaftaran toko baru. |
| **Pemilik UMKM (Seller 1)** | `dapurbundasari@example.com` | `password` | Mengakses dashboard Toko "Dapur Bunda Sari", mengelola produk kuliner, dan memproses pesanan masuk. |
| **Pemilik UMKM (Seller 2)** | `danarhadi@example.com` | `password` | Mengakses dashboard Toko "Batik Danar Solo" untuk manajemen produk fesyen & batik. |
| **Pembeli (Buyer 1)** | `rizki@example.com` | `password` | Akun pembeli dummy "Rizki Darmawan" untuk melakukan simulasi pencarian produk dan pengisian form pesanan. |
| **Pembeli (Buyer 2)** | `siti@example.com` | `password` | Akun pembeli dummy "Siti Fatimah". |

---

## 🧪 Cara Menjalankan Unit & Feature Testing

Jika Anda ingin menjalankan automated testing untuk memvalidasi fungsionalitas backend:
```bash
composer run test
```
atau secara manual:
```bash
php artisan test
```

---

## 🤝 Cara Berkontribusi

Bagi anggota kelompok atau pengembang baru, ikuti alur kerja git berikut untuk menjaga kerapian repository:

1. **Pastikan branch `dev` Anda selalu up-to-date:**
   ```bash
   git checkout dev
   git pull origin dev
   ```
2. **Buat branch baru dari branch `dev`:**
   ```bash
   git checkout -b <tipe>/<deskripsi-singkat>
   # Contoh:
   git checkout -b feature/halaman-login
   git checkout -b fix/validasi-register
   git checkout -b docs/update-readme
   ```
3. **Konvensi Nama Branch:**
   - `feature/` : Fitur baru
   - `fix/`     : Perbaikan bug
   - `docs/`    : Perubahan dokumentasi
   - `refactor/`: Refaktor struktur kode
4. **Lakukan commit menggunakan Conventional Commits:**
   ```bash
   git add .
   git commit -m "<tipe>(<scope>): <deskripsi singkat>"
   # Contoh:
   git commit -m "docs(readme): update installation guide and features list"
   ```
5. **Kirim branch ke repositori remote:**
   ```bash
   git push origin <nama-branch-anda>
   ```
6. **Ajukan Pull Request (PR) di GitHub** ke target branch `dev` untuk direview oleh anggota tim lainnya sebelum disatukan ke `main`.
