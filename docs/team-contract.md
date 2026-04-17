# Team Contract
**Versi:** 1.0  
**Tanggal Efektif:** 30 Maret 2026  
**Durasi Proyek:** 12 Minggu  
**Nama Tim:** ELITE GLOBAL  
**Nama Proyek:** KULAAN.ID

---

## 1. Identitas Tim

| No | NIM | Nama |
|---|---|---|
| 1 | L0124006 | Benedhictus Kevin Doni B.E. |
| 2 | L0124119 | Shaira Masyhita Putri Hatala |
| 3 | L0124120 | Silvi Amalia |
| 4 | L0124138 | Nayyara Aqila Azra |

---

## 2. Peran dan Tanggung Jawab

### 2.1 Peran Awal

| Nama | Peran |
|---|---|
| Benedhictus Kevin Doni B.E. | Project Manager |
| Shaira Masyhita Putri Hatala | Quality Assurance Tester |
| Silvi Amalia | Backend Developer |
| Nayyara Aqila Azra | Frontend Developer |

---

### 2.2 Rolling Peran

Peran anggota tim akan dirotasi setiap 3 minggu untuk memastikan pemerataan pengalaman dan tanggung jawab.

| Minggu | Project Manager | Frontend Developer | Backend Developer | Quality Assurance/Docs |
|---|---|---|---|---|
| 1–3 | Silvi Amalia | Shaira Masyhita Putri Hatala | Nayyara Aqila Azra | Benedhictus Kevin Doni B.E. |
| 3–6 | Benedhictus Kevin Doni B.E. | Silvi Amalia | Shaira Masyhita Putri Hatala | Nayyara Aqila Azra |
| 6–9 | Nayyara Aqila Azra | Benedhictus Kevin Doni B.E. | Silvi Amalia | Shaira Masyhita Putri Hatala |
| 9–12 | Shaira Masyhita Putri Hatala | Nayyara Aqila Azra | Benedhictus Kevin Doni B.E. | Silvi Amalia |

---

### 2.3 Deskripsi Peran

#### Project Manager
- Merencanakan dan mengelola jalannya proyek.
- Menentukan scope, tujuan, dan timeline proyek.
- Mengatur pembagian task dan memastikan setiap task memiliki assignee.
- Memantau progres tim dan mengidentifikasi hambatan (blocker).
- Menjadi penghubung antara tim dan pihak eksternal (dosen/asisten).
- Membuat laporan sprint (sprint summary).
- Mengambil keputusan jika terjadi konflik dalam tim.

#### Quality Assurance Tester / Docs
- Menyusun test plan dan test case.
- Melakukan pengujian (fungsional, integrasi, dan UI).
- Melaporkan bug melalui GitHub Issues.
- Melakukan re-testing setelah bug diperbaiki.
- Menulis dan memperbarui dokumentasi teknis.
- Memastikan fitur memenuhi standar kualitas dan Definition of Done.

#### Backend Developer
- Mengembangkan dan mengelola server serta database.
- Membuat API untuk kebutuhan frontend.
- Menangani autentikasi dan keamanan data.
- Mengoptimalkan performa sistem.
- Melakukan debugging pada sisi backend.

#### Frontend Developer
- Mengembangkan tampilan antarmuka pengguna.
- Mengubah desain (mockup/Figma) menjadi kode.
- Memastikan tampilan responsif dan user-friendly.
- Mengintegrasikan frontend dengan backend.
- Mengoptimalkan performa dan pengalaman pengguna.

---

## 3. Jadwal Meeting & Ritme Sprint

### 3.1 Meeting Rutin
- **Sprint Planning:** Setiap awal praktikum RPL.
- **Sprint Review:** Setiap akhir sprint.
- **Daily Standup:**  
  Dilakukan melalui WhatsApp Group dan Discord.  
  Setiap anggota melaporkan:
  - Apa yang sudah dikerjakan
  - Apa yang akan dikerjakan
  - Apakah ada blocker

---

### 3.2 Durasi Sprint
- 1 sprint = **3 minggu**

---

### 3.3 Kebijakan Ketidakhadiran
- Anggota yang tidak dapat hadir meeting wajib menghubungi Project Manager minimal **3 jam sebelum meeting dimulai**.
- Anggota yang tidak hadir tetap bertanggung jawab untuk:
  - Membaca notulensi
  - Mengerjakan task yang telah di-assign
- Lebih dari **2 kali absen tanpa izin dalam satu sprint** akan dilaporkan dalam mekanisme eskalasi (lihat Bagian 7).

---

## 4. Channel Komunikasi

| Channel | Kegunaan | Response Time |
|---|---|---|
| WhatsApp Group | Komunikasi cepat, info mendadak, daily standup | Maks. 3 jam (08.00–21.00) |
| Discord | Penyimpanan file, diskusi terstruktur, pengelompokan kode/issue, serta sarana video conference untuk meeting tim | Digunakan sesuai kategori |
| GitHub Project | Dokumentasi, sprint board, task management, test case | Update setiap ada perubahan task |
| GitHub Issues | Pelaporan bug dan diskusi teknis | Maks. 1 hari kerja |
| Pull Request (GitHub) | Code review dan feedback teknis | Review dalam 1 hari kerja |

---

## 5. Branching Rule

### 5.1 Struktur Branch
main
├── staging
│ └── dev
│ └── feature/nama-fitur

### 5.2 Penjelasan Tiap Branch

- **main**  
  Branch utama yang berisi kode versi stabil dan siap digunakan (production-ready).
feature/nama-fitur → dev → staging → main

  Hanya digunakan untuk rilis resmi.

- **staging**  
  Branch untuk tahap final testing sebelum rilis.  
  Semua fitur yang sudah lolos QA akan digabungkan di sini untuk memastikan integrasi berjalan dengan baik.

- **dev**  
  Branch utama untuk development.  
  Semua fitur dari tim akan digabungkan di sini setelah melalui proses code review.

- **feature/nama-fitur**  
  Branch khusus untuk pengembangan satu fitur tertentu.  
  Dibuat dari `dev` dan digunakan oleh developer selama proses implementasi.

---

### 5.3 Flow Pengembangan
feature/nama-fitur → dev → staging → main

Alur kerja:
1. Developer membuat branch `feature/nama-fitur` dari `dev`.
2. Developer mengerjakan fitur di branch tersebut.
3. Setelah selesai, developer membuat Pull Request (PR) ke `dev`.
4. PR harus melalui code review dan disetujui minimal 1 anggota tim.
5. Setelah disetujui, branch di-merge ke `dev`.
6. QA melakukan testing terhadap fitur di `dev`.
7. Jika semua fitur dalam satu sprint sudah lolos QA, maka di-merge ke `staging`.
8. Dilakukan pengujian akhir (integration/regression testing) di `staging`.
9. Jika stabil, kode di-merge ke `main` sebagai release resmi.

---

### 5.4 Aturan Penggunaan Branch

- Dilarang melakukan push langsung ke branch `main` dan `staging`.
- Semua pengembangan harus dilakukan melalui branch `feature/nama-fitur`.
- Setiap fitur harus memiliki Pull Request sebelum digabungkan ke `dev`.
- Pull Request wajib mendapatkan minimal **1 approval** dari anggota lain.
- QA wajib memberikan **sign-off** sebelum merge ke `staging`.
- Hanya Project Manager yang berhak menyetujui merge ke `main`.
- Setiap merge harus dalam kondisi:
  - Tidak ada error
  - Tidak menyebabkan konflik
  - Lolos pengujian (testing)

---

### 5.5 Penamaan Branch

- Format:
feature/nama-fitur
- Contoh:
feature/login-auth
feature/product-list
feature/payment-integration

---

### 5.6 Pull Request Rules

Setiap Pull Request wajib:
- Menyertakan deskripsi perubahan yang jelas
- Menyebutkan fitur yang dikerjakan
- Menyertakan bukti testing (jika diperlukan)
- Tidak mengandung error atau konflik

---

### 5.7 Handling Bug Fix

- Bug diperbaiki melalui branch:
fix/nama-bug
- Flow tetap sama:
fix → dev → staging → main

---

### 5.8 Tujuan Branching Strategy

- Menjaga stabilitas kode di `main`
- Meminimalkan konflik antar developer
- Memastikan setiap fitur melalui proses review dan testing
- Memisahkan environment development, testing, dan production

---

## 6. Mekanisme Eskalasi

### 6.1 Konflik Teknis
1. Diskusi di WhatsApp atau GitHub (maks 24 jam).
2. Jika tidak ada kesepakatan, Project Manager mengambil keputusan.
3. Jika Project Manager terlibat, dilakukan voting.

### 6.2 Anggota Tidak Memenuhi Tanggung Jawab
1. Peringatan lisan dari Project Manager.
2. Peringatan tertulis dan dicatat dalam sprint summary.
3. Eskalasi ke asisten/dosen jika berlanjut.

### 6.3 Anggota Tidak Aktif
- Tidak ada kabar lebih dari 3 hari kerja berturut-turut tanpa alasan yang jelas, Project Manager langsung melaporkan ke asisten/dosen.

---

## 7. Konsekuensi Pelanggaran

| Pelanggaran | Konsekuensi |
|---|---|
| Absen tanpa izin (1–2x) | Peringatan lisan |
| Absen >2x dalam 1 sprint | Eskalasi |
| Tidak menyelesaikan task | Tetap menjadi tanggung jawab pribadi |
| Melanggar branching rule | Wajib memperbaiki sendiri |
| Tidak melakukan code review | Diingatkan Project Manager; jika berulang, dicatat di sprint summary|

---

## 8. Perubahan Kontrak

- Perubahan diajukan melalui WhatsApp atau Discord atau GitHub Project.
- Disetujui minimal **3 dari 4 anggota**.
- Setiap perubahan dicatat dalam versi baru (v1.0 → v1.1).

---

*Dokumen ini berlaku sejak tanggal efektif dan dapat diperbarui sesuai prosedur pada Bagian 8.*

