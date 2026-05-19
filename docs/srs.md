# Software Requirements Specification (SRS) - Kulaan.id

**Judul Proyek:** Kulaan.id <br>
**Versi SRS:** 1.0  
**Tanggal:** 2026  
**Tim Pengembang:** <br>
Benedhictus Kevin Doni Brillian Everest (L0124006) <br>
Shaira Masyhita Putri Hatala (L0124119) <br>
Silvi Amalia (L0124120) <br>
Nayyara Aqila Azra (L0124138)  
**Dosen:** Haryono Setiadi, S.T., M.Eng.  
**Asisten:** <br>
Rifqi Makarim <br>
Ravelin Luthfan Syach Putra

---

## 1. Pendahuluan

### 1.1 Tujuan Dokumen

Dokumen Software Requirements Specification (SRS) ini disusun untuk mendefinisikan secara formal dan terstruktur mengenai seluruh kebutuhan perangkat lunak dari aplikasi Kulaan.id, yakni sebuah katalog digital UMKM yang melayani pelaku usaha dan pembeli di Kelurahan Jebres, Surakarta. Dokumen ini berfungsi sebagai:

- Kontrak antara tim pengembang (Kelompok 7) dan seluruh pemangku kepentingan proyek.
- Acuan teknis selama proses pengembangan, pengujian dan penerimaan produk.
- Referensi traceability yang menghubungkan kebutuhan fungsional dengan user story dari P2.

### 1.2 Ruang Lingkup

Kulaan.id adalah aplikasi katalog digital berbasis web yang dirancang untuk:

- Membantu pelaku UMKM di kelurahan Jebres mendaftarkan, mengelola, dan mempublikasikan profil toko serta produk mereka secara digital.
- Membantu pembeli menemukan produk UMKM lokal dengan mudah melalui fitur pencarian, filter, dan tampilan detail produk yang terstruktur.
- Memfasilitasi proses pemesanan secara sederhana tanpa memerlukan sistem pembayaran digital yang kompleks.
- Memungkinkan admin memverifikasi keabsahan toko dan memantau aktivitas platform.

**Di dalam lingkup sistem:**

- Semantic Search Engine untuk pencarian produk berbasis makna
- Manajemen profil UMKM (pendaftaran, pembaruan, verifikasi)
- Filter dan sorting produk berdasarkan kategori dan harga
- Form pemesanan sederhana
- Dashboard untuk admin dan pemilik UMKM
- Integrasi WhatsApp untuk komunikasi langsung

**Di luar lingkup sistem (Won't have):**

- Sistem pembayaran digital (payment gateway)
- Fitur pelacakan pengiriman real-time
- Komunikasi dalam aplikasi (in-app chat)
- Integrasi dengan marketplace eksternal

### 1.3 Definisi dan Akronim

| Istilah / Akronim | Definisi |
|---|---|
| UMKM | Usaha Mikro, Kecil, dan Menengah |
| SRS | Software Requirements Specification (Dokumen Spesifikasi Kebutuhan Perangkat Lunak) |
| FR | Functional Requirement (Kebutuhan Fungsional) |
| NFR | Non-Functional Requirement (Kebutuhan Non-Fungsional) |
| US | User Story (Narasi Kebutuhan dari Perspektif Pengguna) |
| AC | Acceptance Criteria (Kriteria Penerimaan format Given-When-Then) |
| MoSCoW | Must Have, Should Have, Could Have, Won't Have (Metode Prioritisasi) |
| Admin | Pengelola platform "Kulaan.id" yang memiliki akses penuh |
| Pembeli | Pengguna akhir yang mencari dan memesan produk UMKM |
| Pemilik UMKM | Pelaku usaha yang mendaftarkan dan mengelola toko serta produk |
| Dashboard | Halaman ringkasan utama yang menampilkan informasi penting |
| PR | Pull Request (Mekanisme review perubahan kode) |
| Gemini API | Layanan AI dari Google untuk Natural Language Processing |
| COD | Cash on Delivery (Metode Pembayaran Tunai) |
| NLP | Natural Language Processing (Pemrosesan Bahasa Alami) |
| Elasticsearch | Search engine dan analytics engine |

---

## 2. Deskripsi Umum

### 2.1 Perspektif Produk

Kulaan.id merupakan aplikasi web mandiri (standalone) yang dibangun dari awal (greenfield) sebagai solusi atas ketiadaan platform terpusat bagi UMKM lokal di kelurahan Jebres. Saat ini, pelaku UMKM mengandalkan platform tidak terstruktur seperti WhatsApp dan media sosial untuk memasarkan produk, sehingga informasi produk tidak tersusun rapi, sulit ditemukan oleh calon pembeli, dan proses transaksi menjadi tidak efisien.

Aplikasi ini dirancang untuk menjadi:

- **Solusi Terpusat:** Platform tunggal untuk mendaftarkan, mengelola, dan menemukan produk UMKM lokal
- **User-Friendly:** Antarmuka intuitif yang dapat diakses oleh pengguna dengan berbagai latar belakang teknis
- **Efisien:** Mengurangi hambatan komunikasi antara pelaku UMKM dan pembeli potensial

### 2.2 Fungsi Produk

Kulaan.id menyediakan fungsi-fungsi utama berikut yang dikelompokkan berdasarkan peran pengguna:

#### **Untuk Pembeli:**

- Mencari produk UMKM menggunakan kata kunci dengan semantic search
- Memfilter produk berdasarkan kategori dan rentang harga
- Melihat detail produk secara lengkap (nama, deskripsi, harga, foto, kontak toko)
- Mengisi formulir pemesanan sederhana
- Menghubungi penjual melalui WhatsApp

#### **Untuk Pemilik UMKM:**

- Mendaftarkan profil toko dan mengirimkan data untuk diverifikasi admin
- Mengelola dan memperbarui informasi toko serta produk
- Menerima dan melihat detail pesanan yang masuk melalui dashboard
- Menyediakan kontak langsung (WhatsApp) bagi pembeli

#### **Untuk Admin:**

- Memverifikasi keabsahan data toko yang mendaftar
- Menerima atau menolak pendaftaran toko
- Memantau aktivitas platform secara keseluruhan melalui dashboard monitoring
- Melihat statistik toko terdaftar, pesanan, dan status verifikasi

### 2.3 Karakteristik Pengguna

| Peran | Deskripsi | Tingkat Kemampuan Teknis |
|---|---|---|
| Pembeli | Masyarakat umum di kelurahan Jebres yang mencari produk UMKM lokal. Mayoritas pengguna mobile, rentang usia beragam. | Rendah hingga menengah sehingga antarmuka harus intuitif dan mudah dipahami tanpa pelatihan khusus |
| Pemilik UMKM | Pelaku usaha skala mikro hingga kecil yang belum tentu familiar dengan platform digital. Kemampuan literasi digital bervariasi. | Rendah hingga menengah sehingga form dan alur pendaftaran harus sederhana dan berpanduan |
| Admin | Pengelola platform dari pihak kelurahan atau tim Kulaan.id yang bertanggung jawab atas keabsahan data dan operasional sistem. | Menengah sehingga terbiasa menggunakan dashboard dan melakukan validasi data. |

### 2.4 Batasan Sistem

Sistem Kulaan.id dibangun dengan memperhatikan batasan-batasan berikut:

- **Batasan Pembayaran:** Sistem tidak mengintegrasikan payment gateway apapun.
- **Batasan Geografis:** Target pengguna awal terbatas pada pelaku UMKM dan pembeli di kelurahan Jebres, Kecamatan Jebres, Kota Surakarta.
- **Batasan Autentikasi:** Sistem menggunakan autentikasi berbasis email dan password. Belum mendukung login platform sosial (Google, Facebook)
- **Batasan Konten:** Pengunggahan foto produk dibatasi pada ukuran maksimum tertentu untuk menjaga performa server tetap stabil.
- **Batasan Verifikasi:** Proses verifikasi toko dilakukan secara manual oleh admin dan tidak otomatis. Waktu verifikasi bergantung pada ketersediaan admin.

---

## 3. Kebutuhan Fungsional

Kebutuhan fungsional berikut ditransformasi secara langsung dari user story (US) yang telah disusun pada P2, khususnya dari item-item dengan prioritas Must Have dan Should Have pada product backlog.

### 3.1. FR-01: Registrasi Akun Pembeli

Sistem menyediakan halaman registrasi khusus untuk pembeli. Pembeli dapat
mengisi nama lengkap, email, nomor telepon, dan password. Setelah form
dikirimkan, sistem membuat akun baru, mengirimkan email verifikasi, dan
mengarahkan pengguna ke halaman konfirmasi. Sistem memvalidasi input
sebelum pembuatan akun; jika email sudah terdaftar atau format email tidak
valid, sistem menampilkan pesan error yang informatif (misalnya: "Email sudah
digunakan." atau "Format email tidak valid.").

Prioritas: High | Ref: US-01 (Register Account)

---

### 3.2. FR-02: Login Akun Pembeli

Sistem menyediakan halaman login untuk pembeli. Pembeli memasukkan email
dan password yang valid; jika autentikasi berhasil, sistem mengarahkan
pengguna ke halaman beranda. Jika email atau password yang dimasukkan
salah, sistem menampilkan pesan error generik ("Email atau password salah.
Silakan coba lagi.") tanpa mengungkapkan field mana yang tidak sesuai, untuk
mencegah enumerasi akun.

Prioritas: High | Ref: US-02 (Login Account)

---

### 3.3. FR-03: Search Produk

Sistem menyediakan fitur pencarian produk yang dapat diakses melalui halaman pencarian. 
Ketika pengguna mengetikkan kata kunci lalu menekan tombol cari atau Enter, sistem menampilkan 
daftar produk yang relevan dengan kata kunci tersebut. Halaman hasil pencarian memuat informasi 
produk secara lengkap ketika halaman selesai dimuat. Apabila tidak ada produk yang cocok dengan 
kata kunci yang dimasukkan, sistem menampilkan pesan informatif seperti *"Produk tidak ditemukan. 
Coba kata kunci lain."* dan tidak menampilkan daftar kosong tanpa keterangan.

**Prioritas:** High | **Ref:** US-03 (Search Products)

---

### 3.4. FR-04: Filter dan Sorting Produk

Sistem menyediakan mekanisme filter produk berdasarkan kategori produk dan
rentang harga. Ketika filter diterapkan, sistem hanya menampilkan produk yang
sesuai dengan parameter filter yang dipilih pengguna. Apabila tidak ada produk
yang cocok dengan kombinasi filter yang diterapkan, sistem menampilkan pesan
"Tidak ada produk yang sesuai filter ini." dan menyediakan opsi untuk mereset
filter.

Prioritas: High | Ref: US-04 (Filter Products)

Detail Teknis:
- Implementasi logika filter pada sisi Backend
- Optimasi query database untuk performa
- UI untuk pemilihan kategori dan input range harga

---

### 3.5. FR-05: Detail Produk

Sistem menampilkan halaman detail produk yang memuat informasi lengkap
meliputi nama produk, deskripsi, harga, foto produk, serta informasi kontak
toko UMKM, ketika pengguna mengklik produk dari daftar pencarian atau
katalog.

Prioritas: High | Ref: US-05 (View Product Details)

---

### 3.6. FR-06: Form Pemesanan

Sistem menyediakan formulir pemesanan yang dapat diisi oleh pembeli
(minimal: nama pemesan, jumlah pesanan, dan catatan tambahan). Sistem
memvalidasi kelengkapan form sebelum pengiriman; jika form tidak lengkap,
sistem menampilkan pesan error yang informatif. Jika form valid dan
dikirimkan, sistem meneruskan pesanan kepada pemilik UMKM yang
bersangkutan.

Prioritas: High | Ref: US-06 (Fill Order Form)

---

### 3.7. FR-07: Notifikasi Pemesanan Produk

Sistem mengirimkan notifikasi kepada pembeli di setiap perubahan status
pesanan. Notifikasi dikirim pada tiga kondisi: (1) saat pesanan berhasil
dikirimkan dan sedang menunggu konfirmasi penjual, (2) saat pemilik UMKM
mengonfirmasi pesanan, dan (3) saat pemilik UMKM menolak pesanan beserta
alasan penolakan jika tersedia. Jika pengiriman notifikasi gagal, sistem mencoba
mengirim ulang maksimal 3 kali. Apabila tetap gagal, notifikasi tetap tersimpan
dan dapat diakses pembeli melalui halaman riwayat pesanan.

Prioritas: Medium | Ref: US-07 (Notifikasi Pemesanan Produk)

---

### 3.8. FR-08: Registrasi Akun & Profil Toko

Sistem memungkinkan pemilik UMKM mendaftar melalui dua langkah. Pertama,
pemilik UMKM mengisi data akun (nama, email, nomor telepon, dan password);
setelah berhasil disimpan, sistem mengarahkan ke langkah berikutnya. Kedua,
pemilik UMKM mengisi data profil toko (nama toko, deskripsi, kategori, alamat,
dan kontak); setelah data dikirimkan, sistem menyimpan profil toko dengan
status "Menunggu Verifikasi Admin" dan meneruskannya kepada admin untuk
ditinjau. Jika email yang digunakan sudah terdaftar, sistem menampilkan pesan
"Email sudah digunakan. Silakan gunakan email lain atau login."

Prioritas: High | Ref: US-08 (Register Account & Store Profile)

---

### 3.9. FR-09: Login Akun Pemilik UMKM

Sistem menyediakan halaman login terpisah untuk pemilik UMKM. Pemilik
UMKM memasukkan email dan password yang valid; jika autentikasi berhasil,
sistem mengarahkan ke halaman dashboard toko. Apabila toko belum diverifikasi
admin, sistem menampilkan notifikasi informasi bahwa toko sedang dalam
proses verifikasi dan beberapa fitur mungkin belum tersedia. Jika email atau
password yang dimasukkan salah, sistem menampilkan pesan error generik tanpa
mengungkapkan field mana yang tidak sesuai.

Prioritas: High | Ref: US-09 (Login Account)

---

### 3.10. FR-10: Kontak Langsung dengan Penjual

Sistem mengarahkan pembeli ke media komunikasi milik pemilik UMKM
(WhatsApp atau kontak lain yang terdaftar) ketika pembeli memilih opsi untuk
menghubungi penjual dari halaman detail produk atau halaman toko.

Prioritas: Low | Ref: US-10 (Contact Pembeli)

Detail Teknis:
- Penggunaan WhatsApp Deep Link (wa.me) untuk mengarahkan pengguna secara otomatis

---

### 3.11. FR-11: Pembaruan Profil Toko

Sistem memungkinkan pemilik UMKM memperbarui informasi profil toko
(termasuk deskripsi, kontak, jam operasional, dan produk) kapan saja.
Perubahan yang berhasil disimpan langsung tercermin pada halaman profil toko
setelah halaman dimuat ulang.

Prioritas: Medium | Ref: US-11 (Manage Store Profile)

---

### 3.12. FR-12: Penerimaan dan Manajemen Pesanan

Sistem menampilkan daftar pesanan yang masuk pada dashboard pemilik UMKM.
Ketika pemilik UMKM memilih salah satu pesanan dari daftar, sistem
menampilkan detail pesanan tersebut secara lengkap termasuk nama pemesan,
jumlah, dan catatan.

Prioritas: High | Ref: US-12 (Receive Orders)

---

### 3.13. FR-13: Verifikasi Toko (Admin)

Sistem memungkinkan admin yang telah login untuk melihat daftar toko yang
sedang menunggu verifikasi, meninjau data pendaftaran masing-masing toko,
dan mengambil tindakan berupa persetujuan atau penolakan. Toko yang
disetujui akan tampil di direktori publik Kulaan.id.

Prioritas: High | Ref: US-13 (Verify Store Information)

---

### 3.14. FR-14: Monitoring Aktivitas Platform

Sistem menampilkan data aktivitas platform secara ringkas pada dashboard admin
(misalnya: jumlah toko terdaftar, jumlah pesanan, toko baru menunggu
verifikasi). Data diperbarui secara otomatis sehingga admin selalu melihat
informasi terkini. Apabila sistem gagal mengambil data aktivitas dari server,
sistem menampilkan pesan "Gagal memuat data aktivitas. Silakan refresh
halaman." dan tidak menampilkan data yang sudah kedaluarsa (stale data).

Prioritas: Medium | Ref: US-14 (Monitor System Activity)

## 4. Kebutuhan Non-Fungsional

Kebutuhan non-fungsional berikut mendefinisikan bagaimana sistem Kulaan.id harus bekerja, yakni mencakup standar kualitas, performa, keamanan, dan kegunaan yang dapat diverifikasi melalui pengujian konkret.

### NFR-01: Performance

**Deskripsi:**

Halaman utama (beranda), halaman pencarian, dan halaman detail produk harus termuat sepenuhnya dengan latency kurang dari 3 detik pada koneksi internet 4G standar (kecepatan unduh minimal 10 Mbps). Waktu respons API backend untuk permintaan pencarian tidak boleh melebihi 2 detik untuk 95% dari seluruh request dalam kondisi beban normal.

**Metode Verifikasi:** Pengujian menggunakan Chrome DevTools Lighthouse atau tools sejenis pada jaringan yang disimulasikan.

---

### NFR-02: Security

**Deskripsi:**

Kata sandi seluruh pengguna (pembeli, pemilik UMKM, dan admin) wajib disimpan dalam bentuk hash menggunakan algoritma bcrypt dengan cost factor minimal 10; tidak boleh ada satu pun kata sandi yang tersimpan dalam bentuk plain text di basis data. Seluruh komunikasi data antara client dan server menggunakan protokol HTTPS.

**Metode Verifikasi:** Inspeksi langsung pada basis data untuk memastikan tidak ada kolom password yang berisi plain text; audit konfigurasi SSL/TLS server.

---

### NFR-03: Usability

**Deskripsi:**

Antarmuka aplikasi Kulaan.id harus responsif dan dapat digunakan dengan nyaman pada perangkat mobile dengan lebar layar minimal 375px (setara iPhone SE) tanpa horizontal scrolling. Pengguna baru tanpa pelatihan khusus harus mampu menyelesaikan tugas utama yaitu menemukan produk dan mengisi formulir pemesanan dalam waktu kurang dari 5 menit.

**Metode Verifikasi:** Pengujian responsivitas menggunakan Chrome DevTools Device Toolbar; pengujian usability dengan minimal 3 pengguna awam yang diminta menyelesaikan skenario tugas.

---

### NFR-04: Reliability

**Deskripsi:**

Sistem Kulaan.id harus tersedia minimal 99% dari total waktu operasional dalam satu bulan, yang berarti downtime tidak melebihi sekitar 7 jam per bulan dalam kondisi operasional normal (di luar jadwal maintenance yang direncanakan).

**Metode Verifikasi:** Monitoring uptime menggunakan layanan seperti UptimeRobot atau sejenisnya selama periode operasional.

---

### NFR-05: Maintainability

**Deskripsi:**

Seluruh kode sumber proyek Kulaan.id harus mengikuti konvensi Conventional Commits sebagaimana disepakati dalam team contract, mencakup prefix: `feat`, `fix`, `docs`, `refactor`, `add`, dan `update`. Struktur repositori mengikuti standar folder `src/`, `docs/`, `tests/` dan setiap Pull Request wajib melalui proses review minimal satu anggota tim sebelum di-merge ke branch dev.

**Metode Verifikasi:** Audit riwayat commit di GitHub; pemeriksaan struktur folder dan log Pull Request.

---

## 5. Catatan dan Asumsi

### 5.1 Asumsi Utama

1. **Konektivitas Internet:** Pengguna diasumsikan memiliki akses koneksi internet yang stabil (minimal 3G/4G) saat menggunakan aplikasi, karena Kulaan.id tidak dirancang untuk mode offline.

2. **Verifikasi Manual:** Proses verifikasi toko oleh admin diasumsikan dilakukan secara manual dan responsif dalam waktu 1×24 jam pada hari kerja. Jika admin tidak aktif, toko tetap dalam status "menunggu verifikasi" dan tidak tampil ke publik.

3. **Ketersediaan Gemini API:** Semantic search menggunakan Google Gemini API diasumsikan selalu tersedia dengan latensi rendah. Fitur pencarian bisa mencari "oleh-oleh murah khas Solo" dan AI dapat mengerti maksudnya tanpa harus bergantung pada kata kunci yang persis sama.

4. **Kepemilikan Kontak:** Sistem diasumsikan bahwa nomor WhatsApp atau kontak yang didaftarkan pemilik UMKM adalah milik mereka sendiri dan aktif digunakan untuk keperluan bisnis.

5. **Single Administrator:** Pada versi 1.0, diasumsikan hanya terdapat satu atau sejumlah kecil admin yang mengelola platform secara manual. Fitur manajemen multi-admin atau role hierarchy belum diperlukan.

6. **Penerapan Google Gemini API:** Penerapan Gemini API pada Kulaan.id difokuskan secara spesifik sebagai Semantic Search Engine. Kami tidak menggunakan Gemini untuk seluruh operasional AI, melainkan memanfaatkan kemampuannya dalam Natural Language Processing (NLP) untuk melakukan embedding dan memahami makna di balik pencarian pengguna. Dengan mengubah query menjadi vektor, sistem dapat menemukan produk yang relevan meskipun kata kuncinya tidak cocok secara harfiah (contoh: mencari 'oleh-oleh khas Solo' akan memunculkan produk batik atau makanan lokal).

### 5.2 Ketergantungan Eksternal

| Ketergantungan | Deskripsi | Tingkat Kemampuan Teknis |
|---|---|---|
| Semantic Search Engine | Menggunakan Google Gemini API untuk melakukan Natural Language Processing (NLP) dalam memahami intent pencarian pengguna. Query akan diproses secara semantik (melalui query transformation atau embedding), sehingga sistem dapat menampilkan hasil yang relevan berdasarkan makna, bukan hanya kesamaan kata. Proses retrieval dikombinasikan dengan Elasticsearch menggunakan pendekatan hybrid (vector similarity dan keyword-based search). | Integrasi Gemini API untuk semantic understanding (intent detection & query transformation), Implementasi embedding-based search dan vector similarity (kNN), Hybrid search: kombinasi semantic search dan keyword search (BM25), Sinkronisasi data produk ke dalam bentuk vector dan index Elasticsearch. |
| WhatsApp Deep Link | Fitur kontak langsung pembeli ke penjual | Pembeli hanya mendapatkan nomor telepon yang ditampilkan statik |
| Hosting/Server | Operasional aplikasi secara keseluruhan | Sistem tidak dapat diakses |

### 5.3 Hal-hal di Luar Lingkup

Fitur-fitur berikut tidak termasuk dalam pengembangan Kulaan.id pada fase ini.
- Sistem pembayaran tidak diimplementasikan karena platform hanya berfungsi sebagai katalog, sehingga transaksi dilakukan secara manual melalui metode seperti Cash on Delivery (COD) atau transfer langsung guna meminimalisir risiko terkait keamanan data finansial.

- Selain itu, sistem tidak menyediakan fitur real-time tracking karena proses pengiriman sepenuhnya diserahkan kepada kesepakatan antara penjual dan pembeli melalui komunikasi eksternal, tanpa integrasi dengan API logistik seperti JNE atau J&T.

- Fitur komunikasi dalam aplikasi (in-app chat) juga tidak disediakan untuk menjaga efisiensi penggunaan resource server serta mempertahankan performa aplikasi tetap ringan. Oleh karena itu, komunikasi dialihkan ke platform pihak ketiga seperti WhatsApp.

- Kulaan.id juga tidak melakukan integrasi dengan marketplace eksternal seperti Shopee atau Tokopedia, karena sistem difokuskan sebagai platform independen untuk UMKM lokal di wilayah Jebres.

- Selain itu, autentikasi pihak ketiga seperti login menggunakan Google atau Facebook belum didukung, sehingga sistem hanya mengandalkan autentikasi berbasis email dan password.
