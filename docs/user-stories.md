# User Story & Acceptance Criteria Documentation

Dokumen ini merinci kebutuhan pengguna (User Stories) dan kriteria penerimaan (Acceptance Criteria) untuk platform UMKM.

---

## Role: Pembeli

### 1. US-01: Search Products
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

### 2. US-02: Filter Products
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
* **AC-03 (Error Handling):** 
    * **Given** pengguna telah menerapkan filter kategori atau harga
    * **When** tidak ada produk yang sesuai dengan kombinasi filter tersebut
    * **Then** sistem menampilkan pesan *"Tidak ada produk yang sesuai filter ini."* dan menyediakan opsi untuk mereset filter


### 3. US-03: View Product Details
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

### 4. US-04: Fill Order Form
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

## Role: Pemilik UMKM

### 1. US-05: Register Store Profile
> **As a** Pemilik UMKM,  
> **I want** to register my store profile,  
> **so that** my product can be recognized by a lot of people.

* **Acceptance Criteria:**
    * **Given** pemilik UMKM mengisi data toko
    * **When** data toko dikirim
    * **Then** sistem dapat menyimpan dan menunggu verifikasi admin.

### 2. US-06: Contact Pembeli
> **As a** Pemilik UMKM,  
> **I want** to be able to contact directly to my Pembeli,  
> **so that** information can be distributed correctly.

* **Acceptance Criteria:**
    * **Given** pemilik UMKM sedang berada di halaman detail produk
    * **When** pembeli ingin menghubungi penjual
    * **Then** sistem mengarahkan pembeli ke media komunikasi yang tersedia.

### 3. US-07: Manage Store Profile
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

### 4. US-08: Receive Orders
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

### 1. US-09: Verify Store Information
> **As an** Admin,  
> **I want** to memverifikasi informasi toko,  
> **so that** memastikan data yang ditampilkan valid.

* **Acceptance Criteria:**
    * **Given** admin telah login ke sistem
    * **When** admin membuka data pendaftaran toko
    * **Then** menampilkan data/daftar toko yang menunggu verifikasi.

### 2. US-10: Monitor System Activity
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
    * **Then** sistem menampilkan pesan "Gagal memuat data aktivitas. Silakan refresh halaman." dan tidak menampilkan data yang sudah kadaluarsa (stale data)
 
      
