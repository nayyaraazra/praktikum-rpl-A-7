# Data Dictionary - Platform UMKM (Kulaan.ID)

| Nama Tabel | Kolom | Tipe Data | Constraint | Keterangan |
|---|---|---|---|---|
| User | id_user | INT | PK | ID user |
| User | name | VARCHAR(255) | NOT NULL | Nama pengguna |
| User | email | VARCHAR(255) | UNIQUE, NOT NULL | Email untuk login |
| User | password | VARCHAR(255) | NOT NULL | Password hash |
| User | phone_number | VARCHAR(255) | UNIQUE | Nomor telepon |
| User | role | ENUM('buyer','seller','admin') | NOT NULL | Role pengguna |
| User | created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Tanggal akun dibuat |
| Store | id_store | INT | PK | ID toko |
| Store | id_user | INT | FK | ID pemilik toko |
| Store | store_name | VARCHAR(255) | NOT NULL | Nama toko |
| Store | description | TEXT | NULL | Deskripsi toko |
| Store | address | TEXT | NOT NULL | Alamat toko |
| Store | phone_number | VARCHAR(255) | NULL | Nomor telepon penjual |
| Store | verification_status | ENUM('pending','approved','rejected') | DEFAULT 'pending' | Status verifikasi toko |
| Store | verification_by | INT | FK | ID admin yang memverifikasi |
| Store | created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Tanggal toko dibuat |
| Categories | id_category | INT | PK | ID kategori |
| Categories | name_category | VARCHAR(255) | NOT NULL | Nama kategori |
| Products | id_product | INT | PK | ID produk |
| Products | name | VARCHAR(255) | NOT NULL | Nama produk |
| Products | description | TEXT | NULL | Deskripsi produk |
| Products | price | DECIMAL(10,2) | NOT NULL | Harga produk |
| Products | stock | INT | NOT NULL | Stok produk |
| Products | image_product | VARCHAR(255) | NULL | Gambar produk |
| Products | id_store | INT | FK | ID toko |
| Products | id_category | INT | FK | ID kategori |
| Orders | id_order | INT | PK | ID order |
| Orders | id_user | INT | FK | ID pembeli |
| Orders | id_product | INT | FK | ID produk |
| Orders | order_date | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Tanggal order |
| Orders | total_order | DECIMAL(10,2) | NOT NULL | Harga total order |
| Orders | status | ENUM('pending','processed','completed','cancelled') | DEFAULT 'pending' | Status order |
| Orders | note | TEXT | NULL | Catatan pembeli |
