# Changelog

Semua perubahan penting pada proyek **KULAAN.id** akan didokumentasikan pada file ini.
Format mengikuti [Keep a Changelog](https://keepachangelog.com/en/1.1.0/) dan proyek ini menggunakan [Semantic Versioning](https://semver.org/).

## [1.0.0] - 2026-07-06

### Added
- **US-01 Register Account** — Pendaftaran akun pembeli baru dengan validasi email & nomor telepon.
- **US-02 Login Account** — Otentikasi pembeli menggunakan email dan password dengan perlindungan error generik.
- **US-03 Search Products** — Pencarian produk berbasis kata kunci.
- **US-04 Filter Products** — Penyaringan katalog produk berdasarkan kategori dan rentang harga.
- **US-05 View Product Details** — Halaman rincian produk (deskripsi, harga, foto, info toko).
- **US-06 Fill Order Form** — Form pemesanan produk sederhana (nama, jumlah, catatan) tanpa payment gateway.
- **US-07 Notifikasi Pemesanan** — Notifikasi status pesanan (menunggu, dikonfirmasi, ditolak) di riwayat pemesanan.
- **US-08 Register & Store Profile** — Registrasi dua langkah bagi penjual: akun lalu profil toko.
- **US-09 Login Account (Seller)** — Otentikasi pemilik UMKM ke dashboard manajemen toko.
- **US-10 Hubungi Penjual** — Kontak langsung dari halaman detail produk via tautan WhatsApp.
- **US-11 Manage Store Profile** — Pengelolaan profil toko (nama, kontak, alamat, jam operasional).
- **US-12 Receive Orders** — Daftar pesanan masuk beserta rincian data di dashboard penjual.
- **US-13 Verify Store Information** — Peninjauan dan persetujuan/penolakan verifikasi toko oleh Admin Kelurahan.
- **US-14 Monitor System Activity** — Dashboard ringkasan statistik aktivitas platform untuk Admin.
- Unit test (`tests/Unit`) untuk fungsi kritis: `isOpen()`, `isVerified()` (Model `Store`), `isSeller()`, `isBuyer()` (Model `User`) — 9 test case dengan pola AAA.
- Dokumentasi proyek lengkap (README, panduan instalasi, daftar akun demo, badge teknologi).

### Changed
- Struktur README dirapikan agar dapat diikuti pengembang baru dari nol (prasyarat → instalasi → menjalankan → pengujian → kontribusi).

### Fixed
- Penanganan kasus kosong (edge case) pada pengecekan role user (`isSeller`/`isBuyer`) agar tidak error saat data `roles` kosong.
- Penyesuaian logika `isOpen()` agar konsisten membedakan hari kerja dan akhir pekan.

---
_Rilis ini merupakan rilis stabil pertama (v1.0.0) dari KULAAN.id, hasil pengembangan Kelompok 7 Praktikum RPL Kelas A._
