# PRODUCT BACKLOG DAN MOSCOW

## MUST HAVE

| Prioritas | User Story | User Role | Deskripsi |
|-----------|-----------|-----------|-----------|
| MUST HAVE | **US-01**: Register Account | Pembeli | Pengguna mengisi nama lengkap, email, nomor telepon, dan password → sistem membuat akun baru dan mengirim email verifikasi; input tidak valid → muncul pesan error |
| MUST HAVE | **US-02**: Login Account | Pembeli | Input email dan password valid → sistem mengautentikasi dan mengarahkan ke beranda; kredensial salah → muncul pesan error tanpa mengungkapkan field yang salah |
| MUST HAVE | **US-03**: Search Products | Pembeli | Input keyword → sistem menampilkan produk relevan; halaman menampilkan info lengkap saat dimuat; tidak ada produk cocok → muncul pesan informatif |
| MUST HAVE | **US-05**: View Product Details | Pembeli | Klik produk → detail ditampilkan; data produk lengkap saat halaman dimuat |
| MUST HAVE | **US-06**: Fill Order Form | Pembeli | Form valid → pesanan terkirim ke UMKM; form tidak lengkap → muncul pesan error |
| MUST HAVE | **US-08**: Register Account & Store Profile | Pemilik UMKM | Pemilik UMKM mendaftar dua langkah: isi data akun lalu data profil toko; setelah selesai, status toko menjadi *"Menunggu Verifikasi Admin"* |
| MUST HAVE | **US-09**: Login Account | Pemilik UMKM | Input email dan password valid → diarahkan ke dashboard toko; toko belum terverifikasi → muncul notifikasi informasi; kredensial salah → muncul pesan error |
| MUST HAVE | **US-12**: Receive Orders | Pemilik UMKM | Pemilik UMKM dapat melihat daftar pesanan yang masuk beserta detailnya melalui dashboard |
| MUST HAVE | **US-13**: Verify Store Information | Admin | Admin login → dapat melihat daftar toko → menampilkan data yang menunggu verifikasi |

---

## SHOULD HAVE

| Prioritas | User Story | User Role | Deskripsi |
|-----------|-----------|-----------|-----------|
| SHOULD HAVE | **US-04**: Filter Products | Pembeli | Memilih kategori/harga → sistem menampilkan produk sesuai filter; tidak ada produk cocok → muncul pesan dan opsi reset filter |
| SHOULD HAVE | **US-11**: Manage Store Profile | Pemilik UMKM | Pemilik UMKM dapat memperbarui/mengelola informasi toko seperti deskripsi, kontak, atau produk yang ditampilkan |
| SHOULD HAVE | **US-14**: Monitor System Activity | Admin | Dashboard menampilkan data aktivitas; data terbaru muncul saat diperbarui; gagal memuat → muncul pesan error dan tidak menampilkan data kedaluarsa |

---

## COULD HAVE

| Prioritas | User Story | User Role | Deskripsi |
|-----------|-----------|-----------|-----------|
| COULD HAVE | **US-10**: Contact Pembeli | Pemilik UMKM | Pembeli yang ingin menghubungi akan diarahkan ke media komunikasi (WA, dll) |
| COULD HAVE | **US-07**: Notifikasi Pemesanan Produk | Pembeli | Pembeli menerima notifikasi saat pesanan dikirim, dikonfirmasi, atau ditolak penjual; jika pengiriman gagal, sistem retry maksimal 3 kali dan notifikasi tersimpan di riwayat pesanan |

---

## WON'T HAVE

| Prioritas | User Story | User Role | Deskripsi |
|-----------|-----------|-----------|-----------|
| WON'T HAVE | — | — | Tidak ada (semua fitur masih relevan untuk versi ini) |
