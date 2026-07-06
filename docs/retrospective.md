# Retrospektif Tim — KULAAN.id (Kelompok 7)

## What went well? (Apa yang berjalan baik)
- Seluruh 14 User Story (US-01 s.d. US-14) berhasil diselesaikan dan berstatus "Selesai" sebelum demo akhir.
- Pembagian peran developer berjalan cukup jelas sehingga fitur pembeli, penjual (UMKM), dan admin bisa dikerjakan secara paralel.
- Unit testing (9 test case dengan pola AAA) berhasil dijalankan dengan hasil 11 passed, 0 failed, menunjukkan fungsi-fungsi kritis (isOpen, isVerified, isSeller, isBuyer) sudah teruji sesuai ekspektasi.
- Dokumentasi (README, panduan instalasi, akun demo) cukup lengkap sehingga anggota baru maupun asisten dosen dapat menjalankan proyek dari nol tanpa kendala berarti.
- Penggunaan AI (ChatGPT, Claude, Gemini, OpenCode) cukup membantu mempercepat proses setup kerangka program dan debugging, terutama untuk integrasi OAuth.

## What didn't go well? (Apa yang tidak berjalan baik)
- Beberapa fitur dashboard (pembeli dan pemilik UMKM) masih membutuhkan banyak iterasi debugging sebelum stabil, karena output awal dari AI belum sepenuhnya bebas bug.
- Koordinasi commit/push antar anggota kadang tumpang tindih, terutama menjelang deadline sehingga sempat terjadi konflik merge kecil di branch `dev`.
- Fitur pencarian semantik (Google Gemini API + Elasticsearch) yang sempat direncanakan di awal akhirnya tidak jadi diimplementasikan karena keterbatasan waktu, dan digantikan dengan pencarian berbasis kata kunci.

## What can we improve? (Apa yang bisa diperbaiki ke depan)
- Menjadwalkan sesi sinkronisasi branch (pull `dev`) lebih rutin agar konflik merge dapat diminimalkan.
- Menambahkan lebih banyak automated test (terutama Feature test untuk alur pemesanan end-to-end), tidak hanya Unit test pada model.
- Melakukan code review antar anggota sebelum merge ke `dev`, agar kualitas kode lebih konsisten sejak awal.
- Mendokumentasikan AI-Usage Log secara real-time (bukan mengingat-ingat di akhir), agar catatan lebih akurat dan detail.

## Shout-outs (Apresiasi untuk anggota tim)
- **Benedhictus Kevin Doni B.E.** : Berperan aktif dalam penulisa kode, manajemen tugas, penulisan laporan praktikum, mendebug backend, serta bertindak sebagai QA yang mengawal kualitas kode proyek.
- **Shaira Masyhita Putri Hatala** : Berperan aktif dalam penulisan kode, inovatif dan inisiatif dalam pemberian ide-ide fitur, penulisan laporan praktikum, dan mengeksekusi perbaikan fitur-fitur penting.
- **Silvi Amalia** : Berperan aktif dalam penulisan kode, memantau porsi kerja proyek, penulisan laporan praktikum, menyusun berkas CHANGELOG, mengonfigurasi integrasi autentikasi Google OAuth, serta memperbaiki bug navigasi pada fitur eksplorasi.
- **Nayyara Aqila Azra** : Berperan aktif dalam penulisan kode, membangun struktur awal kerangka program, penulisan laporan praktikum, merapikan dokumentasi berkas README utama, serta ikut mengelola pembagian tugas proyek.
- **Kerja Sama Tim** : Setiap anggota tim memiliki inisiatif tinggi yang memahami porsi kerja dan jobdesc masing-masing. Tim secara konsisten saling melakukan cross-check kode (code review) dan saling back-up jika ada kendala teknis antar perangkat sepanjang semester.
