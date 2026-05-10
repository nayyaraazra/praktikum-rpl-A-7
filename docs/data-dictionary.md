# Data Dictionary KULAAN.ID

## Tabel: User

| Tabel | Kolom | Tipe Data | Constraint | Keterangan |
|-------|-------|-----------|------------|------------|
| User | id_user | INT | PK | ID user |
| User | name | VARCHAR(255) | NOT NULL | Nama pengguna |
| User | email | VARCHAR(255) | UNIQUE, NOT NULL | Email untuk login |
| User | password | VARCHAR(255) | NOT NULL | Password hash |
| User | phone_number | VARCHAR(20) | UNIQUE, NOT NULL | Nomor telepon |
| User | role | ENUM | NOT NULL | buyer / seller |

---

## Tabel: Store

| Tabel | Kolom | Tipe Data | Constraint | Keterangan |
|-------|-------|-----------|------------|------------|
| Store | id_store | INT | PK | ID toko |
| Store | id_user | INT | FK | ID pemilik toko |
| Store | phone_number | VARCHAR(20) | FK | Nomor telepon penjual |
| Store | store_name | VARCHAR(255) | NOT NULL | Nama toko |
| Store | description | TEXT | - | Deskripsi toko |
| Store | address | TEXT | - | Alamat toko |
| Store | verification_status | ENUM | NOT NULL | menunggu / disetujui / dibatalkan |
| Store | operating_hours | VARCHAR(100) | NOT NULL | Jam operasional toko |
| Store | district | VARCHAR(100) | NOT NULL | Lokasi kecamatan/kelurahan toko |
| Store | created_at | TIMESTAMP | DEFAULT NOW() | Tanggal toko didaftarkan |

---

## Tabel: Categories

| Tabel | Kolom | Tipe Data | Constraint | Keterangan |
|-------|-------|-----------|------------|------------|
| Categories | id_category | INT | PK | ID kategori |
| Categories | name | VARCHAR(255) | NOT NULL | Nama kategori |

---

## Tabel: Products

| Tabel | Kolom | Tipe Data | Constraint | Keterangan |
|-------|-------|-----------|------------|------------|
| Products | id_product | INT | PK | ID produk |
| Products | id_store | INT | FK | ID toko |
| Products | id_category | INT | FK | ID kategori |
| Products | name | VARCHAR(255) | NOT NULL | Nama produk |
| Products | description | TEXT | - | Deskripsi produk |
| Products | price | DECIMAL | NOT NULL | Harga produk |
| Products | stock | INT | NOT NULL | Stok produk |
| Products | image_product | VARCHAR(255) | - | Gambar produk |
| Products | unit | VARCHAR(50) | NOT NULL | Satuan produk (pcs, kg) |
| Products | min_order | INT | NOT NULL, DEFAULT 1 | Minimum jumlah pemesanan |
| Products | rating | DECIMAL | DEFAULT 0.0 | Rata-rata rating produk |
| Products | review_count | INT | DEFAULT 0 | Total ulasan produk |
| Products | is_active | TINYINT(1) | DEFAULT 0 | Status produk aktif (1) atau dinonaktifkan (0) by seller |

---

## Tabel: Orders

| Tabel | Kolom | Tipe Data | Constraint | Keterangan |
|-------|-------|-----------|------------|------------|
| Orders | id_order | INT | PK | ID order |
| Orders | id_user | INT | FK, NOT NULL | ID pembeli (relasi ke User) |
| Orders | name | VARCHAR(255) | FK, NOT NULL | Nama pembeli (relasi ke User) |
| Orders | id_product | INT | FK, NOT NULL | ID produk |
| Orders | quantity | INT | NOT NULL | Jumlah produk (maks. stok produk) |
| Orders | order_date | TIMESTAMP | DEFAULT NOW(), NOT NULL | Tanggal order |
| Orders | total_order | DECIMAL | NOT NULL | Harga total (price x quantity) |
| Orders | status | ENUM | NOT NULL | menunggu / diproses / selesai / dibatalkan |
| Orders | payment_method | ENUM | NOT NULL | cod / transfer |
| Orders | shipping_address | TEXT | NOT NULL | Alamat pengiriman |
| Orders | note | TEXT | - | Catatan pembeli |

---

## Tabel: PaymentAccounts

| Tabel | Kolom | Tipe Data | Constraint | Keterangan |
|-------|-------|-----------|------------|------------|
| PaymentAccounts | id_payment | INT | PK | ID rekening pembayaran |
| PaymentAccounts | id_store | INT | FK, NOT NULL | ID toko pemilik rekening |
| PaymentAccounts | bank_name | VARCHAR(100) | NOT NULL | Nama bank (cth: BCA, BNI, Mandiri) |
| PaymentAccounts | account_number | VARCHAR(50) | NOT NULL | Nomor rekening penjual |
| PaymentAccounts | account_name | VARCHAR(255) | NOT NULL | Nama pemilik rekening (a.n.) |
| PaymentAccounts | qris_code | VARCHAR(255) | - | URL/kode QRIS, tampil 'QRIS tersedia · scan kode' di Form Pesanan |
| PaymentAccounts | is_active | TINYINT(1) | DEFAULT 0 | Status aktif rekening (1=aktif, 0=nonaktif) |

---

## Tabel: Reviews

| Tabel | Kolom | Tipe Data | Constraint | Keterangan |
|-------|-------|-----------|------------|------------|
| Reviews | id_review | INT | PK | ID ulasan |
| Reviews | id_order | INT | FK, NOT NULL | ID order yang diulas |
| Reviews | id_user | INT | FK, NOT NULL | ID pembeli yang memberi ulasan |
| Reviews | id_product | INT | FK, NOT NULL | ID produk yang diulas |
| Reviews | rating | TINYINT | NOT NULL, CHECK (1-5) | Nilai bintang 1-5 |
| Reviews | comment | TEXT | - | Komentar opsional dari pembeli |
| Reviews | created_at | TIMESTAMP | DEFAULT NOW() | Tanggal ulasan dibuat |
