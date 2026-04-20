# Software Requirements Specification (SRS) - Kulaan.id

## 1. PENDAHULUAN

### 1.1 Tujuan Dokumen
Dokumen ini disusun untuk mendefinisikan kebutuhan perangkat lunak dari aplikasi Kulaan.id sebagai acuan formal bagi tim pengembang (Kelompok 7) dan seluruh pemangku kepentingan.

### 1.2 Ruang Lingkup Sistem
Kulaan.id adalah platform katalog digital UMKM di Kelurahan Jebres yang mencakup:
- Pendaftaran dan pengelolaan profil toko UMKM.
- Pencarian produk berbasis **Semantic Search Engine**.
- Dashboard manajemen pesanan sederhana bagi penjual.

## 2. DESKRIPSI UMUM

### 2.1 Kebutuhan Fungsional (Functional Requirements)

| ID | Nama Fitur | Deskripsi | Prioritas |
| :--- | :--- | :--- | :--- |
| **FR-01** | **Semantic & Hybrid Search** | Sistem menyediakan fitur pencarian yang memahami *intent* pengguna menggunakan **Google Gemini API**. Sistem mencocokkan makna (vektor) kueri untuk menampilkan hasil relevan secara otomatis, dikombinasikan dengan metode *keyword-based* (Hybrid Search). | **High** |
| **FR-02** | **Profil Toko & Katalog** | Memungkinkan pemilik UMKM mendaftarkan toko dan mengelola katalog produk (Tambah/Ubah/Hapus). | **High** |
| **FR-03** | **Order Form & Dashboard** | Menyediakan formulir pemesanan yang diteruskan ke penjual dan dashboard monitoring pesanan. | **Medium** |
| **FR-04** | **WhatsApp Integration** | Integrasi komunikasi langsung antara pembeli dan penjual melalui fitur *WhatsApp Deep Link*. | **High** |

### 2.2 Batasan Sistem (Constraints)
- **Geografis:** Pengguna terbatas pada wilayah Kelurahan Jebres, Surakarta.
- **Pembayaran:** Tidak ada integrasi gateway pembayaran (transaksi manual/COD).
- **Logistik:** Tidak menyediakan pelacakan pengiriman (delivery tracking).

## 3. KETERGANTUNGAN EKSTERNAL

| Peran | Deskripsi | Detail Teknis |
| :--- | :--- | :--- |
| **Semantic Search** | **Google Gemini API** | Digunakan untuk NLP, *intent detection*, dan *query transformation* melalui *embedding-based search*. |
| **Vector Engine** | **Elasticsearch** | Digunakan untuk sinkronisasi data produk ke dalam bentuk vektor dan indeks pencarian kNN (*k-Nearest Neighbors*). |
| **Communication** | **WhatsApp API** | Penggunaan `wa.me` untuk memfasilitasi transaksi di luar sistem. |

## 4. HAL-HAL DI LUAR LINGKUP (WON'T HAVE)

Sesuai dengan kesepakatan pengembangan versi ini, fitur berikut **tidak** diimplementasikan:
1. **Integrated Payment Gateway:** Pembayaran dilakukan di luar sistem.
2. **Real-time Delivery Tracking:** Pengiriman dikelola secara mandiri oleh penjual.
3. **In-app Chat System:** Komunikasi dialihkan sepenuhnya ke WhatsApp.
4. **Third-party Auth:** Login melalui media sosial (Google/FB) belum didukung.

---
**[SUBMISSION] P3 Evidence**

Link Dokumen: https://docs.google.com/document/d/18v-n2ilbd-DJT3dbZA-tEcRUm978X5XvklO9pI2Bzms/edit?tab=t.2efawnuafctw#heading=h.wiv407vrj9s8
