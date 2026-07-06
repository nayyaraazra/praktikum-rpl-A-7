# AI-Usage Log — KULAAN.id

Log ini mendokumentasikan penggunaan AI yang signifikan selama pengembangan proyek, sesuai prinsip **Responsible AI Use** (transparansi, verifikasi, keamanan data, dan pemahaman).

| Tanggal | Anggota | Tool AI | Ringkasan Prompt | Ringkasan Output | Modifikasi / Verifikasi |
|---|---|---|---|---|---|
| 2026-04-15 | Nayyara A. | ChatGPT | Menyusun draft user stories dan kerangka SRS awal proyek berdasarkan masalah UMKM di Kelurahan Jebres. | Draft 14 user story beserta format dokumen SRS awal. | Direview bersama tim, disesuaikan dengan kondisi UMKM Jebres yang sebenarnya. |
| 2026-04-28 | Lia | Claude | Membuat wireframe alur pembeli (search, filter produk, order form). | Sketsa wireframe low-fidelity untuk 3 halaman utama sisi pembeli. | Disesuaikan komponen dan alur navigasi dengan hasil diskusi tim. |
| 2026-05-10 | Kevin | Gemini | Merancang skema ERD dan migration database Laravel (users, stores, products, orders). | Struktur tabel beserta relasi antar tabel dan draft file migration. | Diverifikasi relasi foreign key, disesuaikan dengan business logic aplikasi. |
| 2026-05-18 | Shaira | ChatGPT | Membuat komponen Vue untuk halaman katalog produk dengan filter kategori & rentang harga. | Kode komponen `ProductCatalog.vue` lengkap dengan state filter. | Disesuaikan styling Tailwind, ditambahkan penanganan state hasil pencarian kosong. |
| 2026-05-25 | Lia | Claude & Gemini | Membantu menambahkan Google OAuth login dengan arahan dasar membuat OAuth 2.0 client ID melalui Google Cloud Console. | Panduan step-by-step dan bantuan penyelesaian masalah saat terjadi error dalam penerapannya di aplikasi. | Diuji dan dicek dari device rekan tim; error handling dilakukan dengan menemukan root problem, lalu perbaikan di-push ulang. |
| 2026-06-02 | Kevin | Antigravity | Debugging error validasi form pemesanan (order form) yang gagal submit pada beberapa kombinasi input. | Identifikasi letak bug pada rule validasi request Laravel beserta saran perbaikan. | Diuji ulang manual dengan berbagai kombinasi input, dipastikan pesan error tampil sesuai. |
| 2026-06-07 | Nayyara A. | OpenCode | Membangun kerangka program dari sisi role pembeli dan pemilik UMKM; membuat implementation plan untuk beberapa fitur must have. | Logic kerangka program yang sudah berjalan tanpa error; fitur dashboard pembeli dan pemilik UMKM yang belum sempurna. | Diuji dan dilakukan debug dengan error handling untuk menemukan letak bug. |
| 2026-06-14 | Shaira | Antigravity | Membuat fitur notifikasi status pesanan (menunggu/dikonfirmasi/ditolak) di riwayat pemesanan pembeli. | Kode komponen Vue beserta endpoint API untuk status notifikasi pesanan. | Diuji dengan data dummy, diverifikasi status berubah sesuai aksi dari sisi penjual. |
| 2026-06-20 | Kevin | ChatGPT | Menulis unit test untuk fungsi `isOpen()` dan `isVerified()` pada model `Store` mengikuti pola AAA. | Draft kode PHPUnit test case beserta teknik mocking waktu menggunakan Carbon. | Direview dan disesuaikan, ditambahkan edge case tambahan sebelum dijalankan dengan `php artisan test`. |
| 2026-06-25 | Nayya | Claude | Membantu menyusun draft README.md (deskripsi proyek, daftar fitur, panduan instalasi). | Draft README lengkap dengan struktur folder proyek dan badge teknologi. | Direvisi ulang oleh tim, disesuaikan dengan struktur repo dan langkah instalasi yang sebenarnya. |
| 2026-07-02 | Lia | Antigravity | Menyusun draft CHANGELOG.md dan AI-Usage Log untuk kebutuhan submission P11. | Draft changelog format Keep a Changelog serta tabel AI-usage log. | Diverifikasi ulang dan disesuaikan dengan data penggunaan AI asli tim sebelum dikumpulkan. |

## Refleksi Penggunaan AI

- **Yang berhasil:** AI (ChatGPT, Claude, Gemini, OpenCode, dan Antigravity) cukup efektif membantu di hampir seluruh tahap: mulai dari penyusunan user story/SRS, wireframe, desain ERD, pembuatan komponen Vue, hingga debugging dan penulisan unit test. Penggunaan yang konsisten sejak awal mempercepat banyak proses berulang seperti boilerplate kode dan penulisan dokumentasi.
- **Yang kurang berhasil:** Output AI untuk fitur yang lebih kompleks (dashboard pembeli/pemilik UMKM, validasi form pemesanan) masih memerlukan banyak penyesuaian manual sebelum benar-benar bebas bug.
- **Verifikasi:** Setiap output AI diuji ulang secara manual (menjalankan aplikasi, mengecek dari device lain, serta debugging langsung ke root cause) sebelum digabungkan ke branch utama.
- **Keamanan data:** Tidak ada credential, `.env`, atau data sensitif proyek yang dimasukkan ke dalam prompt AI.
