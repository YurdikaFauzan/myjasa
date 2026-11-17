# SmartService AI - Platform Pemesanan Jasa & Perbaikan Rumah Terintegrasi AI

[![CI Pipeline](https://github.com/YurdikaFauzan/myjasa/actions/workflows/blank.yml/badge.svg)](https://github.com/YurdikaFauzan/myjasa/actions/workflows/blank.yml)
![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-777BB4?logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/Database-MySQL-4479A1?logo=mysql&logoColor=white)
![Architecture](https://img.shields.io/badge/Architecture-Native%20MVC-success)
![Testing](https://img.shields.io/badge/Unit%20Tests-32%20Passed-brightgreen)
![License](https://img.shields.io/badge/License-MIT-blue.svg)

> Proyek ini dikembangkan untuk memenuhi tugas akhir mata kuliah **Rekayasa Perangkat Lunak (RPL)**.

---

## 👥 1. Identitas Anggota Kelompok

* **Nama Kelompok**: Hambalang  
* **Program Studi**: Teknik Informatika / Rekayasa Perangkat Lunak  
* **Tema / Topik Proyek**: Jasa Perbaikan Rumah (*Home Appliance & House Repair Services*)

### Daftar Anggota:

| No | Nama Anggota | NIM | Peran / Kontribusi Utama |
| :---: | :--- | :---: | :--- |
| 1 | **Yurdika Fauzan** | **41257169** | Lead Developer, Arsitektur MVC, Integrasi AI & CI/CD Pipeline |
| 2 | **Qonita** | **41257151** | Frontend Designer, Manajemen Views & UI/UX Customer |
| 3 | **Reyhan** | **41257153** | Backend Developer, Sistem Autentikasi & WhatsApp OTP |
| 4 | **Yoga** | **41257243** | Database Administrator, Relasi Schema & Query Optimization |
| 5 | **Nasywa** | **41257144** | Frontend Developer, Portal Teknisi & Timeline Status Tracking |
| 6 | **Soleh** | **41257171** | System Tester, Perancangan Unit Test & Quality Assurance |

---

## 📌 2. Tema dan Topik Proyek

**Tema**: Jasa Perbaikan Rumah (*Home Appliance & Electronics Repair Services*)  
Sistem berbasis web yang menghubungkan pemilik rumah yang membutuhkan jasa perbaikan peralatan elektronik rumah tangga (seperti AC, Kulkas, Mesin Cuci, TV, Dispenser, dan Kompor) dengan teknisi profesional yang terverifikasi, dilengkapi asisten kecerdasan buatan (AI) untuk diagnosis kerusakan secara instan dan akurat.

---

## 📖 3. Deskripsi Aplikasi

### Penjelasan Aplikasi
**SmartService AI** adalah aplikasi web pemesanan dan manajemen jasa perbaikan peralatan rumah tangga cerdas yang dirancang dengan arsitektur **Native MVC (Model-View-Controller)** berbasis **PHP 8.2** dan **MySQL**. Aplikasi ini memfasilitasi siklus penanganan perbaikan mulai dari konsultasi keluhan, penjadwalan kunjungan teknisi, pelacakan proses pengerjaan secara waktu nyata (*real-time progress tracking*), pembayaran berbasis digital, hingga pemberian ulasan (*review & rating*).

### Permasalahan yang Ingin Diselesaikan
1. **Kesulitan Mencari Teknisi Terpercaya**: Pelanggan sering kesulitan menemukan teknisi panggilan yang jujur, bersertifikasi, dan memiliki estimasi harga transparan.
2. **Ketidaktahuan Diagnosis Kerusakan**: Pelanggan awam sering tidak mengetahui indikasi awal kerusakan alat mereka sehingga rentan mengalami salah penanganan atau eksploitasi biaya oleh oknum tidak bertanggung jawab.
3. **Ketidakpastian Jadwal & Status Servis**: Proses konvensional melalui chat telepon/pesan manual kerap tidak memiliki bukti tertulis mengenai status kedatangan teknisi, rincian biaya penggantian suku cadang, maupun bukti garansi.
4. **Manajemen Penugasan yang Tercecer**: Penyedia jasa dan admin kesulitan mendistribusikan pesanan pelanggan kepada teknisi yang sedang senggang secara terstruktur.

### Tujuan Pembuatan Aplikasi
1. Menyediakan platform pemesanan jasa perbaikan rumah yang terpadu, terstruktur, dan transparan bagi pelanggan, teknisi, serta manajemen admin.
2. Mengintegrasikan teknologi kecerdasan buatan (*Artificial Intelligence*) guna membantu pelanggan menganalisis gejala kerusakan alat, tingkat urgensi, langkah keselamatan mandiri, serta rekomendasi penanganan sebelum teknisi datang.
3. Mengotomatiskan alur bisnis mulai dari notifikasi OTP WhatsApp, pelacakan status bertahap (*timeline tracker*), penugasan teknisi (*job dispatching*), faktur transaksi, hingga evaluasi kualitas pelayanan.

---

## ⚡ 4. Fitur Utama Aplikasi

Aplikasi memiliki pemisahan hak akses berbasis 3 peran pengguna (*Role-Based Access Control*):

### A. Fitur Pelanggan (Customer)
* **Katalog Layanan Interaktif**: Menjelajahi berbagai kategori perbaikan (AC, Kulkas, Mesin Cuci, TV, Dispenser, Kompor, dan lainnya) lengkap dengan rincian biaya dasar dan durasi pengerjaan.
* **Formulir Booking Perbaikan**: Memesan teknisi dengan menentukan tanggal kunjungan, jam, alamat lengkap, deskripsi keluhan, serta opsi unggah foto bukti kerusakan.
* **Timeline Status Pengerjaan (Live Tracking)**: Memantau progres pesanan tahap demi tahap:
  $$\text{Pending} \rightarrow \text{Assigned} \rightarrow \text{Accepted} \rightarrow \text{On The Way} \rightarrow \text{Repairing} \rightarrow \text{Completed}$$
* **Sistem Pembayaran & Faktur**: Memilih metode pembayaran (QRIS atau Tunai/Cash) setelah pekerjaan selesai dan mengunduh ringkasan faktur.
* **Ulasan & Rating Bintang**: Memberikan penilaian bintang (1–5) serta ulasan testimoni terhadap teknisi yang bertugas.
* **Autentikasi Ganda**: Pilihan masuk menggunakan Email & Password atau Login Cepat tanpa sandi via **WhatsApp OTP**.

### B. Fitur Teknisi (Technician Portal)
* **Dashboard Penugasan**: Melihat pesanan baru yang ditugaskan oleh admin beserta lokasi pelanggan dan jadwal kunjungan.
* **Aksi Lapangan Cepat**: Tombol konfirmasi rute perjalanan (*On The Way*), mulai perbaikan (*Start Repairing*), dan penyelesaian pekerjaan (*Complete Job*).
* **Catatan Lapangan & Penyesuaian Harga**: Menginput rincian pengerjaan teknis, suku cadang yang diganti, serta penyesuaian tarif final secara transparan.
* **Riwayat Pekerjaan**: Rekapitulasi pesanan yang telah diselesaikan beserta perolehan rating dari pelanggan.

### C. Fitur Administrator (Admin Control Center)
* **Dashboard Analitik**: Ringkasan total pesanan aktif, pendapatan kotor, jumlah pelanggan, dan performa teknisi.
* **Manajemen Penugasan (Order Dispatcher)**: Meninjau pesanan masuk dan menugaskan teknisi yang sesuai secara manual maupun rekomendasi otomatis.
* **Master Data Kategori & Layanan**: Tambah, ubah, dan nonaktifkan kategori alat rumah tangga dan jenis paket jasa perbaikan.
* **Manajemen Pengguna**: Memverifikasi dan mengelola akun pelanggan maupun mitra teknisi.
* **Panel Konfigurasi AI**: Mengontrol provider AI aktif, mengganti model LLM secara dinamis, menguji latensi respon, serta mengelola API key yang tersimpan aman.

---

## 🤖 5. Fitur Artificial Intelligence (AI)

SmartService AI mengimplementasikan modul kecerdasan buatan (*Generative AI & LLM*) yang terintegrasi secara modular ke dalam alur aplikasi:

### 1. AI Home Appliance Diagnosis (`Services\AiDiagnosisService`)
* **Fungsi**: Menganalisis deskripsi keluhan gejala kerusakan yang dimasukkan pengguna secara bahasa alami (*Natural Language*).
* **Cara Kerja**:
  1. Input pengguna dikombinasikan dengan kategori alat dan aturan keselamatan (*Safety Rules*).
  2. Model AI memproses analisis dalam format JSON terstruktur yang menghasilkan:
     - *Possible Issues* (potensi masalah utama)
     - *Possible Causes* (akar penyebab kerusakan)
     - *Simple Checks* (pengecekan mandiri yang aman)
     - *Urgency Level* (Tingkat urgensi: Rendah, Sedang, Tinggi)
     - *Safety Warnings* (peringatan bahaya listrik/gas)
     - *Summary* (ringkasan rekomendasi perbaikan)
  3. **Local Fallback Engine**: Apabila kuota API eksternal habis atau jaringan terputus, sistem secara otomatis beralih ke mesin diagnosis berbasis *heuristic rule engine* internal sehingga aplikasi tidak pernah mengalami *crash*.

### 2. Context-Aware AI Chatbot Assistant (`Services\ChatbotService` & `ChatbotContextResolver`)
* **Fungsi**: Asisten virtual pintar yang dapat menjawab pertanyaan seputar layanan dan status pesanan spesifik milik pelanggan (mirip sistem RAG sederhana).
* **Keamanan & Guardrail**:
  - Dilengkapi proteksi *Prompt Injection*, penolakan permintaan SQL injection, dan proteksi kebocoran *system prompt* / API key.
  - Memiliki *Role Authorization Filter* sehingga pelanggan hanya dapat menanyakan nomor pesanan miliknya sendiri dan dilarang melihat data pengguna lain.

### 3. Multi-Provider AI Architecture (`Services\Ai\AiManager`)
* **Teknologi Model yang Didukung**:
  - **Groq Cloud API** (Default: `qwen/qwen3.8-27b`, `llama-3.3-70b-versatile`)
  - **Google Gemini API** (`gemini-2.5-flash`, `gemini-1.5-flash`)
  - **OpenAI API** (`gpt-4o-mini`, `gpt-3.5-turbo`)
  - **DeepSeek API** (`deepseek-chat`)
  - **Nvidia NIM API** (`meta/llama-3.1-70b-instruct`)
* **Enkripsi Kredensial**: Seluruh API key yang disimpan di database dienkripsi secara simetris menggunakan algoritma **AES-256-CBC** dengan salt unik aplikasi (`APP_KEY`).

---

## 📹 6. Link Video Demonstrasi Aplikasi

Tonton video demonstrasi lengkap alur kerja aplikasi SmartService AI dan implementasi CI/CD GitHub Actions pada tautan berikut:

[![Tonton Video Demonstrasi](https://img.shields.io/badge/YouTube-Demonstrasi%20Aplikasi%20%26%20CI%2FCD-red?style=for-the-badge&logo=youtube)](https://www.youtube.com/watch?v=PLACEHOLDER_LINK_VIDEO_UAS)

> 🔗 **Tautan YouTube**: `https://www.youtube.com/watch?v=PLACEHOLDER_LINK_VIDEO_UAS`  
> *(Silakan ganti URL di atas dengan tautan video presentasi YouTube kelompok Anda).*

---

## 🛠️ 7. Panduan Instalasi dan Menjalankan Aplikasi

Ikuti petunjuk bertahap berikut untuk menginstal dan menjalankan aplikasi pada komputer lokal:

### A. Persyaratan Perangkat Lunak (Prerequisites)
Pastikan komputer Anda telah terpasang:
* **PHP** versi **8.1** atau **8.2** (disarankan paket [XAMPP](https://www.apachefriends.org/))
  * Ekstensi PHP yang wajib aktif: `pdo_mysql`, `openssl`, `mbstring`, `curl`, `json`
* **MySQL Server** (versi 5.7+ atau MariaDB 10.4+)
* **Web Server Apache** (bawaan XAMPP) dengan modul `mod_rewrite` aktif
* **Composer** (opsional untuk menjalankan PHPUnit secara formal)
* **Git** (untuk kloning repository)
* **Web Browser** modern (Chrome, Edge, atau Firefox)

---

### B. Langkah Instalasi

#### 1. Kloning Repository
Buka terminal (Git Bash, Command Prompt, atau PowerShell), arahkan ke direktori `htdocs` XAMPP Anda:
```bash
cd D:/xampp/htdocs/
git clone https://github.com/YurdikaFauzan/myjasa.git Myjasagwuehhhhhhhhhhhhhhhhhhhhh
cd Myjasagwuehhhhhhhhhhhhhhhhhhhhh
```

#### 2. Konfigurasi Environment (`.env`)
Salin file template `.env.example` menjadi `.env`:
```bash
cp .env.example .env
```
Buka file `.env` dan sesuaikan parameter koneksi database Anda (default XAMPP biasanya tanpa password):
```ini
APP_NAME="SmartService AI"
APP_ENV=development
APP_URL=http://localhost/Myjasagwuehhhhhhhhhhhhhhhhhhhhh
APP_KEY=SmartServiceAI_Secret_Key_2026_Secure

DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=smartservice_ai
DB_USER=root
DB_PASS=

# AI Provider (Opsional, jika ingin mengaktifkan API eksternal)
AI_ENGINE=groq
GROQ_API_KEY=gsk_your_groq_api_key_here
GEMINI_API_KEY=your_gemini_api_key_here

# WhatsApp Gateway (Opsional untuk testing OTP)
FONTE_API_TOKEN=your_fonte_token_here
FONTE_API_BASE_URL=https://api.fonnte.com
```

#### 3. Import Database
1. Buka aplikasi **XAMPP Control Panel**, nyalakan modul **Apache** dan **MySQL**.
2. Buka browser dan akses **phpMyAdmin** di `http://localhost/phpmyadmin/`.
3. Buat database baru bernama: `smartservice_ai`.
4. Pilih tab **Import**, klik **Choose File**, pilih file [schema.sql](file:///d:/xampp/htdocs/Myjasagwuehhhhhhhhhhhhhhhhhhhhh/schema.sql) dari root project, lalu klik **Import / Go**.
5. Database akan otomatis membuat seluruh tabel relasi beserta data awal (*seed data*).

#### 4. Instalasi Dependensi (Opsional / untuk Testing)
Jika Anda memiliki Composer terpasang di komputer Anda:
```bash
composer install
```

---

### C. Menjalankan & Mengakses Aplikasi

1. Pastikan folder project berada di dalam `d:\xampp\htdocs\Myjasagwuehhhhhhhhhhhhhhhhhhhhh`.
2. Buka browser dan ketik alamat:
   ```text
   http://localhost/Myjasagwuehhhhhhhhhhhhhhhhhhhhh
   ```
3. Gunakan akun demo yang telah disediakan pada database untuk mencoba setiap peran:

| Peran (Role) | Alamat Email | Password Default | Akses Menu |
| :--- | :--- | :---: | :--- |
| **Admin** | `admin@smartservice.ai` | `password123` | Dashboard Admin, Kelola User, AI Config |
| **Customer** | `budi.customer@gmail.com` | `password123` | Buat Pesanan, AI Diagnosis, Bayar & Review |
| **Teknisi** | `ahmad.teknisi@gmail.com` | `password123` | Dashboard Tugas, Update Status Pengerjaan |

---

## 🧪 8. Panduan Menjalankan Unit Testing

Proyek ini telah dilengkapi **32 Unit Test** independen yang menguji fungsi-fungsi inti aplikasi tanpa memerlukan koneksi database eksternal:

### Cara 1: Menjalankan Test Runner Lokal (Tanpa Butuh Composer)
Jalankan perintah ini di PowerShell atau Terminal:
```powershell
& "D:\xampp\php\php.exe" tests/run_tests.php
```
*(atau cukup `php tests/run_tests.php` jika PHP ada di PATH sistem).*

### Cara 2: Menggunakan PHPUnit Formal
```bash
vendor/bin/phpunit --testdox
```

### Rincian Test Suite yang Diuji:
* `AiConfigurationTest`: Enkripsi/dekripsi AES-256 dan masking API key.
* `ChatbotContextResolverTest`: Deteksi prompt injection dan otorisasi data pesanan.
* `EnvTest`: Parsing file `.env`, penanganan komentar, dan default value.
* `OtpVerificationTest`: Normalisasi format nomor seluler Indonesia dan sensor nomor.
* `RouterTest`: Registrasi rute `GET`/`POST` dan pembersihan path URI.
* `ServiceRequestTest`: Validasi kelengkapan form pesanan dan status siklus perbaikan.
* `UserTest`: Validasi peran pengguna dan struktur metadata.

---

## 🚀 9. Otomasi CI/CD (GitHub Actions)

Alur kerja integrasi berkelanjutan dikonfigurasi melalui [.github/workflows/blank.yml](https://github.com/YurdikaFauzan/myjasa/blob/main/.github/workflows/blank.yml).

Setiap kali terjadi `git push` atau `pull request` ke branch `main`, GitHub Actions akan menjalankan langkah-langkah berikut secara otomatis di lingkungan cloud Ubuntu:
1. Melakukan checkout source code terbaru.
2. Memasang runtime **PHP 8.2** beserta ekstensi yang dibutuhkan.
3. Memvalidasi keabsahan struktur file `composer.json`.
4. Menginstal dependensi PHPUnit via Composer.
5. Melakukan pemeriksaan sintaks PHP (*PHP Lint*) di seluruh file proyek.
6. Menjalankan seluruh pengujian **Unit Test (PHPUnit)**.
7. Memastikan seluruh tes lulus dengan status **HIJAU (SUCCESS)**.

Status pipeline dapat dipantau langsung melalui tab **Actions** pada repository GitHub:  
👉 [https://github.com/YurdikaFauzan/myjasa/actions](https://github.com/YurdikaFauzan/myjasa/actions)

---

## 📁 10. Struktur Direktori Proyek

```text
Myjasagwuehhhhhhhhhhhhhhhhhhhhh/
├── .github/
│   └── workflows/
│       └── ci.yml               # Konfigurasi pipeline GitHub Actions CI
├── Assets/                      # File statis (CSS, JS, gambar, upload keluhan)
├── Config/                      # Konfigurasi aplikasi (Database & Env loader)
│   ├── Database.php
│   └── Env.php
├── Controllers/                 # Controller logika bisnis (MVC)
│   ├── AdminController.php
│   ├── AiConsultationController.php
│   ├── AuthController.php
│   ├── BaseController.php
│   ├── CategoryController.php
│   ├── ChatbotController.php
│   ├── CustomerController.php
│   ├── HomeController.php
│   ├── OtpAuthController.php
│   ├── ProfileController.php
│   ├── ServiceController.php
│   └── TechnicianController.php
├── Middleware/                  # Filter keamanan rute (AuthMiddleware.php)
├── Models/                      # Model representasi data & database query
│   ├── AiConfiguration.php
│   ├── AiConsultation.php
│   ├── BaseModel.php
│   ├── Category.php
│   ├── OtpVerification.php
│   ├── Payment.php
│   ├── Review.php
│   ├── Service.php
│   ├── ServiceRequest.php
│   └── User.php
├── Routes/                      # Definisi rute URL web (Router.php, web.php)
├── Services/                    # Layanan terisolasi (AI, WhatsApp, Chatbot)
│   ├── Ai/                      # Multi-provider AI (Groq, Gemini, OpenAI, dll.)
│   ├── AiDiagnosisService.php
│   ├── ChatbotContextResolver.php
│   ├── ChatbotService.php
│   └── FonteService.php
├── tests/                       # Unit Test Suite
│   ├── Polyfill/                # Polyfill test runner mandiri
│   ├── Unit/                    # File-file pengujian unit class
│   ├── bootstrap.php            # Inisialisasi testing environment
│   └── run_tests.php            # Runner pengujian lokal
├── Views/                       # Template tampilan antarmuka (HTML/PHP)
├── .env.example                 # Contoh variabel konfigurasi environment
├── .gitignore                   # Daftar file yang diabaikan Git (.env, vendor, log)
├── composer.json                # Pengaturan dependensi & autoloading PSR-4
├── index.php                    # Front Controller (Entry point aplikasi)
├── phpunit.xml                  # Konfigurasi resmi PHPUnit
├── README.md                    # Dokumentasi lengkap proyek
└── schema.sql                   # Struktur database MySQL & seed data awal
```

---

## 📄 Lisensi

Proyek ini didistribusikan di bawah lisensi **MIT License**. Hak cipta © 2026 **Kelompok Hambalang**. Seluruh kode dibuat untuk tujuan pendidikan dan pemenuhan UAS Rekayasa Perangkat Lunak.
