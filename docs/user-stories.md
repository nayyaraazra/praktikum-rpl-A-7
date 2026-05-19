# User Story & Acceptance Criteria Documentation

Dokumen ini merinci kebutuhan pengguna (User Stories) dan kriteria penerimaan (Acceptance Criteria) untuk platform UMKM.

---

## Role: Pembeli

### 1. US-01: Register Account
> **As a** Pembeli,  
> **I want** to register an account,  
> **so that** I can access the platform and make purchases.

* **AC-1:**
    * **Given** pengguna membuka halaman registrasi pembeli
    * **When** pengguna mengisi nama lengkap, email, nomor telepon, dan password lalu menekan tombol *"Daftar"*
    * **Then** sistem membuat akun baru, mengirim email verifikasi, dan mengarahkan pengguna ke halaman konfirmasi
* **AC-2 (Validasi Input):**
    * **Given** pengguna mengisi form registrasi
    * **When** pengguna memasukkan email yang sudah terdaftar atau format email tidak valid
    * **Then** sistem menampilkan pesan error yang sesuai, misalnya *"Email sudah digunakan."* atau *"Format email tidak valid."*

---

### 2. US-02: Login Account
> **As a** Pembeli,  
> **I want** to login to my account,  
> **so that** I can access my profile and transaction history.

* **AC-1:**
    * **Given** pengguna berada di halaman login pembeli
    * **When** pengguna memasukkan email dan password yang valid lalu menekan tombol *"Masuk"*
    * **Then** sistem mengautentikasi pengguna dan mengarahkan ke halaman beranda
* **AC-2 (Validasi Kredensial):**
    * **Given** pengguna berada di halaman login
    * **When** pengguna memasukkan email atau password yang salah
    * **Then** sistem menampilkan pesan *"Email atau password salah. Silakan coba lagi."* dan tidak mengungkapkan field mana yang salah

---

### 3. US-03: Search Products
> **As a** Pembeli,  
> **I want** to search products,  
> **so that** I can find UMKM products easily.

* **AC-1:**
    * **Given** pengguna berada di halaman pencarian dan mengetikkan kata kunci
    * **When** pengguna menekan tombol cari atau menekan Enter
    * **Then** sistem menampilkan daftar produk yang relevan dengan kata kunci
* **AC-2:**
    * **Given** data tersedia
    * **When** halaman dimuat
    * **Then** info tampil lengkap.
* **AC-3 (Error Handling):**
    * **Given** pengguna memasukkan kata kunci yang tidak cocok dengan produk apapun
    * **When** pengguna menekan tombol cari
    * **Then** sistem menampilkan pesan informatif seperti *"Produk tidak ditemukan. Coba kata kunci lain."* dan tidak menampilkan daftar kosong tanpa keterangan

---

### 4. US-04: Filter Products
> **As a** Pembeli,  
> **I want** to filter products,  
> **so that** I can narrow down search results.

* **AC-1:**
    * **Given** pengguna memilih kategori
    * **When** filter diterapkan
    * **Then** produk sesuai kategori tampil.
* **AC-2:**
    * **Given** pengguna memilih harga
    * **When** filter diterapkan
    * **Then** produk sesuai rentang harga tampil.
* **AC-3 (Error Handling):**
    * **Given** pengguna telah menerapkan filter kategori atau harga
    * **When** tidak ada produk yang sesuai dengan kombinasi filter tersebut
    * **Then** sistem menampilkan pesan *"Tidak ada produk yang sesuai filter ini."* dan menyediakan opsi untuk mereset filter

---

### 5. US-05: View Product Details
> **As a** Pembeli,  
> **I want** to view product details,  
> **so that** I can understand product info.

* **AC-1:**
    * **Given** pengguna klik produk
    * **When** halaman produk dibuka
    * **Then** detail produk akan tampil.
* **AC-2:**
    * **Given** data tersedia
    * **When** halaman dimuat
    * **Then** info tampil lengkap.

---

### 6. US-06: Fill Order Form
> **As a** Pembeli,  
> **I want** to fill order form,  
> **so that** I can order products easily.

* **AC-1:**
    * **Given** form diisi
    * **When** dikirim
    * **Then** pesanan diteruskan ke UMKM.
* **AC-2:**
    * **Given** form tidak lengkap
    * **When** dikirim
    * **Then** muncul error.

---

### 7. US-07: Notifikasi Pemesanan Produk
> **As a** Pembeli,  
> **I want** to receive order notifications,  
> **so that** I can track the status of my orders in real-time.

* **AC-1 (Konfirmasi Pesanan):**
    * **Given** pembeli telah berhasil mengirimkan form pesanan
    * **When** pesanan diterima oleh sistem
    * **Then** sistem mengirimkan notifikasi kepada pembeli berupa *"Pesanan Anda berhasil dikirim dan sedang menunggu konfirmasi dari penjual."*
* **AC-2 (Pesanan Dikonfirmasi Penjual):**
    * **Given** pemilik UMKM telah mengonfirmasi pesanan di dashboard mereka
    * **When** status pesanan diperbarui menjadi *"Dikonfirmasi"*
    * **Then** sistem mengirimkan notifikasi kepada pembeli bahwa pesanan mereka sudah dikonfirmasi oleh penjual
* **AC-3 (Pesanan Ditolak Penjual):**
    * **Given** pemilik UMKM menolak pesanan
    * **When** status pesanan diperbarui menjadi *"Ditolak"*
    * **Then** sistem mengirimkan notifikasi kepada pembeli beserta alasan penolakan jika tersedia, dan menyarankan pembeli untuk menghubungi penjual
* **AC-4 (Error Handling):**
    * **Given** terjadi kegagalan pengiriman notifikasi
    * **When** sistem gagal mengirim notifikasi ke perangkat pembeli
    * **Then** sistem mencoba mengirim ulang notifikasi maksimal 3 kali, dan jika tetap gagal, notifikasi tetap tersimpan di halaman riwayat pesanan pembeli

---

## Role: Pemilik UMKM

### 1. US-08: Register Account & Store Profile
> **As a** Pemilik UMKM,  
> **I want** to register an account and my store profile,  
> **so that** my store and products can be recognized by a lot of people.

* **AC-1 (Register Akun):**
    * **Given** pemilik UMKM membuka halaman registrasi penjual
    * **When** pemilik UMKM mengisi data akun (nama, email, nomor telepon, password) dan menekan *"Lanjut"*
    * **Then** sistem menyimpan data akun dan mengarahkan ke langkah pengisian profil toko
* **AC-2 (Register Profil Toko):**
    * **Given** pemilik UMKM telah berhasil membuat akun dan berada di halaman pengisian profil toko
    * **When** pemilik UMKM mengisi data toko (nama toko, deskripsi, kategori, alamat, dan kontak) lalu mengirimkan data
    * **Then** sistem menyimpan profil toko dan mengubah status toko menjadi *"Menunggu Verifikasi Admin"*
* **AC-3 (Validasi Input):**
    * **Given** pemilik UMKM mengisi form registrasi
    * **When** pemilik UMKM memasukkan email yang sudah terdaftar
    * **Then** sistem menampilkan pesan *"Email sudah digunakan. Silakan gunakan email lain atau login."*

---

### 2. US-09: Login Account
> **As a** Pemilik UMKM,  
> **I want** to login to my account,  
> **so that** I can manage my store and process incoming orders.

* **AC-1:**
    * **Given** pemilik UMKM berada di halaman login penjual
    * **When** pemilik UMKM memasukkan email dan password yang valid lalu menekan tombol *"Masuk"*
    * **Then** sistem mengautentikasi pengguna dan mengarahkan ke halaman dashboard toko
* **AC-2 (Status Akun Belum Terverifikasi):**
    * **Given** pemilik UMKM berhasil login namun toko belum diverifikasi admin
    * **When** pemilik UMKM diarahkan ke dashboard
    * **Then** sistem menampilkan notifikasi *"Toko Anda sedang dalam proses verifikasi. Beberapa fitur mungkin belum tersedia."*
* **AC-3 (Validasi Kredensial):**
    * **Given** pemilik UMKM berada di halaman login
    * **When** pemilik UMKM memasukkan email atau password yang salah
    * **Then** sistem menampilkan pesan *"Email atau password salah. Silakan coba lagi."*

---

### 3. US-10: Contact Pembeli
> **As a** Pemilik UMKM,  
> **I want** to be able to contact directly to my Pembeli,  
> **so that** information can be distributed correctly.

* **Acceptance Criteria:**
    * **Given** pemilik UMKM sedang berada di halaman detail produk
    * **When** pembeli ingin menghubungi penjual
    * **Then** sistem mengarahkan pembeli ke media komunikasi yang tersedia.

---

### 4. US-11: Manage Store Profile
> **As a** Pelaku UMKM,  
> **I want** to manage my store profile,  
> **so that** my info stays updated.

* **AC-1:**
    * **Given** profil diedit
    * **When** disimpan
    * **Then** data diperbarui.
* **AC-2:**
    * **Given** perubahan berhasil
    * **When** reload
    * **Then** data terbaru tampil.

---

### 5. US-12: Receive Orders
> **As a** Pelaku UMKM,  
> **I want** to receive orders,  
> **so that** I can process them.

* **AC-1:**
    * **Given** ada pesanan
    * **When** masuk
    * **Then** tampil di dashboard.
* **AC-2:**
    * **Given** pesanan dipilih
    * **When** dibuka
    * **Then** detail tampil.

---

## Role: Admin

### 1. US-13: Verify Store Information
> **As an** Admin,  
> **I want** to memverifikasi informasi toko,  
> **so that** memastikan data yang ditampilkan valid.

* **Acceptance Criteria:**
    * **Given** admin telah login ke sistem
    * **When** admin membuka data pendaftaran toko
    * **Then** menampilkan data/daftar toko yang menunggu verifikasi.

---

### 2. US-14: Monitor System Activity
> **As an** Admin,  
> **I want** to monitor system activity,  
> **so that** I can ensure the platform runs properly.

* **AC-1:**
    * **Given** admin membuka dashboard
    * **When** dimuat
    * **Then** data aktivitas ditampilkan.
* **AC-2:**
    * **Given** ada aktivitas baru
    * **When** diperbarui
    * **Then** data terbaru muncul.
* **AC-3 (Error Handling):**
    * **Given** admin membuka halaman dashboard monitoring
    * **When** sistem gagal mengambil data aktivitas dari server
    * **Then** sistem menampilkan pesan *"Gagal memuat data aktivitas. Silakan refresh halaman."* dan tidak menampilkan data yang sudah kadaluarsa (stale data)
