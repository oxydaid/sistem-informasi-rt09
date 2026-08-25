# 🏢 Sistem Informasi RT 09 & Karang Taruna Tunas Karya

<div align="center">

![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.5-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Inertia.js](https://img.shields.io/badge/Inertia.js-v3-9553E9?style=for-the-badge&logo=inertia&logoColor=white)
![Vue.js](https://img.shields.io/badge/Vue.js-3.5-4FC08D?style=for-the-badge&logo=vue.js&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-v4-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![Vite](https://img.shields.io/badge/Vite-8.2-646CFF?style=for-the-badge&logo=vite&logoColor=white)
![Pest](https://img.shields.io/badge/Pest_PHP-Tested-009CDE?style=for-the-badge)

**Portal Informasi Publik Warga & Sistem Manajemen Administrasi Terpadu RT 09 RW 04 Kelurahan Sukamaju**

[Fitur Utama](#-fitur-utama) • [Panduan Instalasi](#-panduan-instalasi) • [Akun Pengguna](#-akun-pengguna-default) • [Arsitektur & Tech Stack](#-arsitektur--tech-stack)

---

</div>

## 📌 Ringkasan Sistem

**Sistem Informasi RT 09** adalah platform berbasis web modern yang dirancang untuk mendukung transparansi, komunikasi, dan pengelolaan organisasi warga secara mandiri, rapi, dan aman. Platform ini melayani dua entitas utama di lingkungan warga:
1. **Pengurus RT 09**: Struktur kepengurusan rukun tetangga, pendataan anggota/pengurus, agenda lingkungan, dan transparansi administrasi.
2. **Karang Taruna Tunas Karya**: Wadah pemuda dan generasi muda lingkungan RT 09 untuk menyelenggarakan kegiatan sosial, olahraga, dan kreativitas warga.

---

## ✨ Fitur Utama

### 🌐 1. Portal Publik Warga
- **Beranda Interaktif (*Home Page*)**:
  - Banner Utama (*Hero Section*) dengan **3 Kartu Showcase Foto Kegiatan** yang dapat dikustomisasi langsung oleh admin.
  - Sekilas profil RT 09 & Karang Taruna.
  - Widget agenda terdekat, pengumuman darurat, dan dokumentasi kegiatan terbaru.
- **Profil & Struktur Organisasi**:
  - Halaman Profil Pengurus RT 09 dan Karang Taruna lengkap dengan visi, misi, dan kontak sekretariat.
  - Bagan Struktur Kepengurusan dinamis berdasarkan **Periode Jabatan** aktif maupun riwayat arsip masa bakti sebelumnya.
- **Pusat Informasi & Konten Warga**:
  - **Pengumuman**: Informasi surat edaran, jadwal kerja bakti, dan pemberitahuan penting.
  - **Agenda Acara**: Jadwal kegiatan lingkungan, lokasi, dan peta petunjuk arah.
  - **Dokumentasi Kegiatan**: Liputan foto dan deskripsi kegiatan gotong royong, peringatan HUT RI, dll.
  - **Artikel / Berita Warga**: Artikel edukasi lingkungan, kesehatan warga, dan prestasi pemuda.
- **Pustaka Galeri Foto Warga**:
  - Album galeri visual dokumentasi warga dengan tampilan *grid modern* dan *lightbox popup* resolusi penuh.
- **Transparansi Buku Kas Internal** *(Dapat diaktifkan/dinonaktifkan melalui menu pengaturan)*.

---

### 🛡️ 2. Portal Administrasi Pengurus (Admin Dashboard)
- **Modern App Shell & Navigasi**:
  - Layout *full-viewport* dengan header tersemat, navigasi sidebar accordion collapsible, serta tombol keluar (*Logout*) yang selalu terlihat.
  - Navigasi instan berbasis *Inertia v3 Eager Component Resolution & Prefetching* (bebas delay & FOUC).
- **Manajemen Keanggotaan & Jabatan Organisasi**:
  - Form pendataan biodata anggota/pengurus yang bersih (Nama, Gender, Kontak, Avatar, dll).
  - Modal Khusus **"Atur Jabatan"**: Menautkan anggota ke jabatan tertentu pada periode kepengurusan aktif/arsip, lengkap dengan catatan nomor SK dan kontrol kuota maksimal posisi.
- **Manajemen Periode & Jabatan**:
  - Pengaturan masa bakti (e.g. 2024 - 2029) dengan penanda status Aktif / Arsip.
  - Pengaturan hierarki jabatan (Ketua, Sekretaris, Bendahara, Seksi Humas, dll) beserta urutan tampilan (*sort order*).
- **Buku Kas & Keuangan Internal**:
  - Pencatatan kas masuk dan kas keluar dengan pembagian akun kas (Kas Utama RT, Kas Karang Taruna).
  - Kategori transaksi kustom dengan ikon dan warna penanda.
  - Fitur pembatalan transaksi (*Void*) yang aman disertai audit log tanpa merusak urutan pembukuan.
- **Manajemen Konten & Media Library**:
  - Editor teks kaya (*Rich Text Editor*) Tiptap minimalis ala Laravel Filament (dukungan heading, list, kutipan, bold/italic/underline, link, serta tabel dengan garis kontras dan toggle header).
  - Pengunggahan galeri foto banyak (*multiple upload*) dan lampiran dokumen (PDF/Word/Excel).
- **Pengaturan Branding & Visual Website**:
  - Unggah **Logo Website** (format PNG, SVG, WEBP) dengan pratinjau instan.
  - Unggah **Favicon Browser Tab** (`.ico`, `.png`, `.svg`) otomatis terhubung ke tag `<head>`.
  - Atur **3 Foto Showcase Hero Beranda** beserta judul/labelnya.
  - Konfigurasi kontak sekretariat, hotline WhatsApp langsung, dan metadata SEO.
- **Hak Akses & Log Audit Keamanan**:
  - Role-based Access Control (Super Admin, Admin RT, Admin Karang Taruna).
  - Log audit aktivitas untuk mencatat pembuatan, perubahan, dan penghapusan data penting.

---

## 🛠️ Arsitektur & Tech Stack

| Komponen | Teknologi | Keterangan |
| :--- | :--- | :--- |
| **Backend Framework** | Laravel 12 (PHP 8.5) | RESTful Controller, Form Request Validation, Policy, Audit Service |
| **Frontend Adapter** | Inertia.js v3 | Single-Page Application (SPA) tanpa kerumitan API publik terpisah |
| **Frontend UI** | Vue 3 (Composition API) | `<script setup>`, Typed Props, Wayfinder Route Actions |
| **Styling & Theme** | Tailwind CSS v4 | *Trust Blue Pay Design System* (`#001B52`, `#003087`, `#009CDE`) |
| **Icons** | Lucide Vue Next | Paket ikon vektor modern yang ringan |
| **Rich Text Editor** | Tiptap Editor v2 | Editor WYSIWYG clean styling dengan ekstensi tabel lengkap |
| **Database** | MySQL / MariaDB | Skema relasional dengan foreign key, cascade on delete, dan indeks teroptimasi |
| **Testing** | Pest PHP | Unit & Feature Testing |
| **Code Formatter** | Laravel Pint | Standard PSR-12 / Laravel styling |

---

## 🚀 Panduan Instalasi

Ikuti langkah-langkah berikut untuk menjalankan project di komputer lokal (*Local Development*):

### 1. Prasyarat Sistem
- **PHP** `>= 8.2` (disarankan PHP 8.4 atau 8.5)
- **Composer** `>= 2.x`
- **Node.js** `>= 18.x` dan **pnpm** (atau npm)
- **MySQL / MariaDB**

---

### 2. Kloning Repository & Setup Environment
```bash
# 1. Masuk ke direktori kerja
cd d:/LARAVEL/rt09

# 2. Salin file konfigurasi environment
cp .env.example .env

# 3. Install dependency PHP backend
composer install

# 4. Generate Application Encryption Key
php artisan key:generate
```

---

### 3. Konfigurasi Database & Storage Link
Buka file `.env` dan sesuaikan koneksi database Anda:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=rt09
DB_USERNAME=root
DB_PASSWORD=
```

Jalankan migrasi database beserta data awal (*seeders*):
```bash
# Jalankan migrasi dan isi data awal (seeder)
php artisan migrate:fresh --seed

# Hubungkan symbolic link public storage untuk foto/lampiran
php artisan storage:link
```

---

### 4. Install Dependency Frontend & Kompilasi
```bash
# Install dependency javascript
pnpm install

# Kompilasi aset untuk mode pengembangan (Hot Reload)
pnpm run dev

# ATAU kompilasi bundle produksi
pnpm run build
```

---

### 5. Menjalankan Server Aplikasi
```bash
php artisan serve
```
Aplikasi kini dapat diakses melalui browser di: **`http://localhost:8000`**

---

## 🔐 Akun Pengguna Default

Saat database di-*seed*, sistem otomatis membuat 3 akun pengurus untuk keperluan pengujian:

| Role / Hak Akses | Email Login | Password Default | Wewenang Akses |
| :--- | :--- | :--- | :--- |
| **Super Admin** | `superadmin@rt09.id` | `password` | Akses penuh seluruh modul (RT, Karang Taruna, Keuangan, Pengguna, & Log) |
| **Admin RT 09** | `admin.rt@rt09.id` | `password` | Pengelolaan data organisasi RT 09, kegiatan RT, dan buku kas RT |
| **Admin Karang Taruna** | `admin.kt@rt09.id` | `password` | Pengelolaan data anggota pemuda, kegiatan kepemudaan, dan kas KT |

> ⚠️ **Catatan Keamanan:** Segera ubah password akun default saat diterapkan di lingkungan produksi (*production*).

---

## 🧪 Pengujian & Kualitas Kode

Proyek ini telah dilengkapi dengan rangkaian automated test menggunakan **Pest PHP**:

```bash
# Menjalankan seluruh test suite
php artisan test

# Menjalankan pemformat kode otomatis (Laravel Pint)
vendor/bin/pint --format agent
```

---

## 📂 Struktur Direktori Proyek

```text
d:/LARAVEL/rt09/
├── app/
│   ├── Http/Controllers/
│   │   ├── Admin/          # Controller administrasi pengurus (Member, Finance, Content, Setting, dll)
│   │   ├── Auth/           # Controller autentikasi login/logout
│   │   └── PublicController.php # Controller halaman publik warga
│   ├── Models/             # Model Eloquent (Organization, Member, Content, CashTransaction, dll)
│   └── Services/           # Service layer (FileUploadService, SettingsService, AuditLogService)
├── database/
│   ├── migrations/         # Skema tabel database berintegritas tinggi
│   └── seeders/            # Data inisial organisasi, posisi, akun kas, dan admin
├── resources/
│   ├── css/                # Style global Tailwind v4 & Tiptap styling
│   ├── js/
│   │   ├── components/     # Komponen UI reusable (Button, Modal, Card, Input, TiptapEditor)
│   │   ├── layouts/        # Layout PublicLayout.vue dan AdminLayout.vue
│   │   ├── lib/            # Helper utilitas dan route generator
│   │   └── pages/          # Halaman Inertia (public/ dan admin/)
│   └── views/
│       ├── app.blade.php   # Single-page template root
│       └── errors/         # Halaman penanganan kesalahan HTTP (403, 404, 500, dll)
└── routes/
    ├── web.php             # Definisi rute publik dan admin portal
    └── console.php
```

---

## 📄 Lisensi & Kontribusi

Sistem Informasi ini dikembangkan dengan dedikasi untuk kemaslahatan, kerukunan, dan kemajuan warga **RT 09 RW 04 Kelurahan Sukamaju**. Bebas digunakan, dipelajari, dan dikembangkan lebih lanjut.

<div align="center">
  <sub>Dibuat dengan ❤️ untuk Warga RT 09 & Karang Taruna Tunas Karya.</sub>
</div>
