# KELOMPOK 7 PRAKTIKUM RPL

## Tentang Proyek
 
**KULAAN.id** adalah aplikasi katalog digital berbasis web yang dirancang untuk menghubungkan pelaku UMKM di Kelurahan Jebres, Kecamatan Jebres, Kota Surakarta dengan calon pembeli secara efisien.
 
Saat ini, pelaku UMKM lokal mengandalkan platform tidak terstruktur seperti WhatsApp dan media sosial untuk memasarkan produk. Hal ini menyebabkan informasi produk tidak tersusun rapi, sulit ditemukan, dan proses pemesanan tidak efisien. KULAAN.id hadir sebagai solusi terpusat yang menjembatani keduanya.

## Anggota Kelompok
| Nomor | NIM        | Nama                         |
|-------|------------|------------------------------|
| 1     | L0124006   | Benedhictus Kevin Doni B.E.  |
| 2     | L0124119   | Shaira Masyhita Putri Hatala |
| 3     | L0124120   | Silvi Amalia                 |
| 4     | L0124138   | Nayyara Aqila Azra           |

## Teknologi yang Digunakan
 
| Kategori | Teknologi |
|----------|-----------|
| **Frontend** | Vue.js & Vite |
| **Backend** | Laravel 11 |
| **Database** | MySQL |
| **Others Tools** | Composer, Node.js & npm |
| **Version Control** | Git & GitHub |
| **Editor** | VS Code |

## Struktur Proyek
```
kulaan-project/
├── docs/                 # [DOKUMENTASI] Dokumen perencanaan proyek, panduan, dan rancangan sistem
├── tests/                # [PENGUJIAN] Berkas automated testing untuk validasi fitur dan komponen aplikasi
└── src/                  # [KODE UTAMA] Seluruh source code aplikasi (Backend & Frontend)
    ├── app/              # Logika bisnis utama Laravel (Controller, Model, Middleware)
    ├── bootstrap/        # Cache sistem dan pengaturan awal saat aplikasi pertama kali dimuat
    ├── config/           # Semua file konfigurasi sistem aplikasi (Database, Mail, Auth, dll)
    ├── database/         # File Migration struktur database dan Seeders data awal
    ├── public/           # Entry point utama aplikasi (index.php) dan aset web publik (gambar, dll)
    ├── resources/        # Aset antarmuka aplikasi sebelum dikompilasi
    │   └── js/           # Sumber kode utama Vue.js
    │       ├── web/      # Komponen, halaman, dan logika khusus untuk aplikasi Web
    │       └── mobile/   # Komponen, halaman, dan logika khusus untuk aplikasi Mobile (SOON)
    ├── routes/           # Manajemen Routing (web.php untuk Web, api.php untuk API Mobile)
    ├── storage/          # Tempat menyimpan log sistem, file cache, dan file unggahan pengguna
    ├── .env              # File sensitif berisi kredensial database dan konfigurasi environment lokal (tdk boldeh di push di github)
    ├── artisan           # Command-line interface (CLI) bawaan Laravel untuk menjalankan perintah terminal
    ├── composer.json     # Daftar dependensi dan paket library PHP yang digunakan oleh Laravel
    ├── package.json      # Daftar dependensi library JavaScript (Vue.js, Vite) dan perintah script NPM
    └── vite.config.js    # Konfigurasi utama bundling dan kompilasi aset frontend menggunakan Vite
```
## Fitur yang Sudah Ada

| Nama Fitur | Status        | Screenshot                       |
|------------|---------------|----------------------------------|
|**US-01: Register Account**   | Selesai | <img width="945" height="492" alt="image" src="https://github.com/user-attachments/assets/dfc8bed9-810b-40cc-8542-b860fd0d4225" />|
|**US-02: Login Account**       | Selesai |  <img width="960" height="496" alt="image" src="https://github.com/user-attachments/assets/5f526077-0340-449d-b3a0-d6e7a9df4585" />|
|**US-03: Search Product**      | Selesai |  <img width="949" height="533" alt="image" src="https://github.com/user-attachments/assets/9260752e-5b8a-4c92-a74f-170545a235ff" />|


## Setup & Run Project
Berikut merupakan langkah untuk menjalankan project:

### Requirements
* PHP 8.3
* Composer 2.x
* Node.js 20 LTS
* MySQL 8
* Git

### Installation

Clone repository:

```bash id="r9n3ht"
git clone https://github.com/<username>/<repository>.git
cd kulaan-project/src
```

Install dependencies:

```bash id="fj3m2a"
composer install
npm install
```

Setup environment:

```bash id="g8x7pw"
cp .env.example .env
php artisan key:generate
```

### Database Configuration

Edit `.env` (contoh):

```env id="k2v6qu"
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kulaan_db
DB_USERNAME=root
DB_PASSWORD=
```

Buat database lalu jalankan migration:

```bash id="r1z5yn"
php artisan migrate
```

### Run Project

Jalankan menggunakan 2 terminal berbeda.

**Backend Laravel**

```bash id="v8m4tx"
php artisan serve
```

**Frontend Vue.js**

```bash id="s4p9eg"
npm run dev
```


## Cara Kontribusi
 
Semua anggota tim harus mengikuti alur kerja berikut untuk membuat perubahan pada repositori. Jangan langsung push ke branch `dev` atau `main`.
 
### Alur Kerja Git (Feature Branch Workflow)
 
**Langkah 1 — Pastikan branch `dev` lokal sudah terbaru**
 
```bash
git checkout dev
git pull origin dev
```
 
**Langkah 2 — Buat branch baru untuk fitur atau tugas**
 
Gunakan konvensi penamaan branch berikut:
 
```bash
# Format:
git checkout -b <type>/<deskripsi-singkat>
 
# Contoh:
git checkout -b feature/halaman-login
git checkout -b fix/validasi-form-register
git checkout -b docs/update-readme
```
 
| Prefix | Digunakan untuk |
|--------|-----------------|
| `feature/` | Fitur baru |
| `fix/` | Perbaikan bug |
| `docs/` | Perubahan dokumentasi |
| `refactor/` | Refactoring tanpa mengubah fungsi |
 
**Langkah 3 — Kerjakan perubahan, lalu commit**
 
```bash
git add .
git commit -m "<type>(<scope>): <deskripsi singkat>"
```
 
**Langkah 4 — Push branch ke GitHub**
 
```bash
git push origin <nama-branch-anda>
```
 
**Langkah 5 — Buat Pull Request di GitHub**
 
1. Buka repositori di GitHub
2. Klik **"Compare & pull request"**
3. Pastikan base branch adalah `dev` (bukan `main`)
4. Isi judul dan deskripsi PR dengan jelas


6. Merge PR setelah disetujui

