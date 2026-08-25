# PRD — Sistem Informasi RT & Karang Taruna

## 1. Ringkasan Produk

### 1.1 Nama Produk

**Sistem Informasi RT & Karang Taruna**

### 1.2 Deskripsi

Sistem Informasi RT & Karang Taruna adalah aplikasi web berbasis Laravel yang berfungsi sebagai pusat informasi, publikasi kegiatan, pengelolaan organisasi, dokumentasi, serta pengelolaan keuangan RT dan Karang Taruna.

Aplikasi **tidak berfokus pada administrasi kependudukan**, seperti pembuatan surat, pendataan KK, mutasi penduduk, atau administrasi pemerintahan lainnya.

Fokus aplikasi:

- Profil RT.
- Profil Karang Taruna.
- Struktur organisasi.
- Manajemen anggota Karang Taruna.
- Periode kepengurusan.
- Kas RT.
- Kas Karang Taruna.
- Pengumuman.
- Agenda.
- Kegiatan.
- Artikel/informasi.
- Dokumentasi kegiatan.
- Custom page.
- Media dan lampiran.
- Dashboard.
- RBAC.

---

# 2. Tujuan Produk

## 2.1 Tujuan Utama

Membangun platform digital yang membantu organisasi RT dan Karang Taruna dalam:

1. Menyediakan informasi lingkungan secara terpusat.
2. Menampilkan profil dan struktur organisasi.
3. Mendokumentasikan kegiatan.
4. Mengelola konten website tanpa perubahan kode.
5. Mengelola keuangan secara terstruktur.
6. Meningkatkan transparansi kas.
7. Memisahkan data dan hak akses RT dengan Karang Taruna.

## 2.2 Tujuan Non-Fungsional

Aplikasi harus:

- Mudah digunakan oleh pengurus non-teknis.
- Responsive pada mobile, tablet, dan desktop.
- Modular.
- Mudah dikembangkan.
- Memiliki RBAC yang jelas.
- Memisahkan data antar organisasi.
- Menghindari hardcoded content.
- Aman untuk penggunaan publik.

---

# 3. Scope Produk

## 3.1 Termasuk

### Organization

- Profil RT.
- Profil Karang Taruna.
- Struktur organisasi RT.
- Struktur organisasi Karang Taruna.
- Periode kepengurusan.
- Anggota Karang Taruna.

### Finance

- Kas RT.
- Kas Karang Taruna.
- Kategori transaksi.
- Pemasukan.
- Pengeluaran.
- Riwayat transaksi.
- Saldo.
- Transparansi kas.

### Content

- Pengumuman.
- Artikel/informasi.
- Agenda/event.
- Kegiatan.
- Dokumentasi.
- Custom page.
- Media.
- File attachment.

### System

- Authentication.
- User management.
- RBAC.
- Dashboard.
- Settings.
- Audit log.

## 3.2 Tidak Termasuk

Aplikasi tidak menangani:

- Data KK.
- Data NIK.
- Pendataan penduduk.
- Surat pengantar.
- Surat keterangan.
- Mutasi penduduk.
- Administrasi kependudukan.
- Pelayanan administrasi pemerintah.
- Sistem pemerintahan desa/kelurahan.

---

# 4. Role & RBAC

Role utama:

```text
Super Admin
Admin RT
Admin Karang Taruna
```

## 4.1 Super Admin

Memiliki akses penuh.

Permission:

- Manage users.
- Manage roles & permissions.
- Manage RT.
- Manage Karang Taruna.
- Manage organization structure.
- Manage members.
- Manage RT finance.
- Manage Karang Taruna finance.
- Manage content.
- Manage media.
- Manage settings.
- View audit logs.

## 4.2 Admin RT

Hanya mengelola data RT.

Dapat:

- Manage RT profile.
- Manage RT structure.
- Manage RT leadership period.
- Manage RT finance.
- Manage RT content.
- Manage RT events.
- Manage RT activities.
- Manage RT media.

Tidak dapat:

- Mengakses kas Karang Taruna.
- Mengubah anggota Karang Taruna.
- Mengubah struktur Karang Taruna.
- Mengelola system roles/permissions.

## 4.3 Admin Karang Taruna

Dapat:

- Manage Karang Taruna profile.
- Manage Karang Taruna structure.
- Manage Karang Taruna members.
- Manage leadership period.
- Manage Karang Taruna finance.
- Manage Karang Taruna content.
- Manage events.
- Manage activities.
- Manage media.

Tidak dapat:

- Mengakses kas RT.
- Mengubah struktur RT.
- Mengubah profil RT.
- Mengubah system roles/permissions.

---

# 5. Organization

Sistem memiliki dua organisasi utama:

```text
RT
└── RT 01

Karang Taruna
└── Karang Taruna RT 01
```

Setiap data yang berhubungan dengan organisasi harus memiliki `organization_id`.

Contoh:

```text
contents.organization_id
events.organization_id
activities.organization_id
cash_accounts.organization_id
organization_members.organization_id
organization_periods.organization_id
```

Tujuan:

- Mencegah data tercampur.
- Mempermudah authorization.
- Mempermudah pengembangan organisasi di masa depan.

---

# 6. Profil RT

Admin RT dapat mengelola:

- Nama RT.
- Nomor RT.
- Nomor RW.
- Desa/Kelurahan.
- Kecamatan.
- Kabupaten/Kota.
- Provinsi.
- Kode Pos.
- Alamat.
- Logo.
- Foto lingkungan.
- Deskripsi.
- Kontak.
- Email.
- Social media.
- Lokasi/Google Maps.

Halaman publik:

```text
/rt
```

---

# 7. Profil Karang Taruna

Admin Karang Taruna dapat mengelola:

- Nama.
- Logo.
- Foto.
- Deskripsi.
- Kontak.
- Social media.
- Informasi tambahan.

Halaman publik:

```text
/karang-taruna
```

---

# 8. Struktur Organisasi

Struktur organisasi harus bersifat dinamis dan tidak hardcoded.

Contoh RT:

```text
Ketua RT
├── Sekretaris
├── Bendahara
├── Seksi humas
├── Seksi Kebersihan
└── Seksi-seksi lain
```

Contoh Karang Taruna:

```text
Ketua
├── Wakil Ketua
├── Sekretaris
├── Bendahara
├── Seksi humas
└── seksi-seksi lain
```

## 8.1 Organization Position

Data:

- Nama jabatan.
- Deskripsi.
- Urutan.
- Parent position.
- Periode.
- Status.

## 8.2 Organization Member

Data:

- Nama.
- Foto.
- Jabatan.
- Periode.
- Deskripsi.
- Status.

---

# 9. Periode Kepengurusan

Sistem harus mendukung histori kepengurusan.

Contoh:

```text
Periode 2025–2028
Periode 2028–2031
```

Data:

- Nama periode.
- Tahun mulai.
- Tahun selesai.
- Status aktif.
- Catatan.

Struktur organisasi harus dapat dikaitkan dengan periode.

Periode lama tidak boleh hilang ketika periode baru dibuat.

---

# 10. Anggota Karang Taruna

Modul anggota hanya berlaku untuk Karang Taruna.

Data:

- Nama.
- Foto.
- Jenis kelamin, jika dibutuhkan.
- Tahun bergabung.
- Jabatan.
- Status.
- Bio.
- Kontak opsional.

Status:

```text
active
inactive
alumni
```

### Pemisahan User dan Member

Anggota Karang Taruna tidak otomatis menjadi user aplikasi.

Contoh:

```text
40 anggota Karang Taruna
        ↓
4 orang memiliki akun dashboard
```

`users` dan `organization_members` harus merupakan entitas berbeda.

---

# 11. Finance

Sistem menggunakan satu engine keuangan untuk dua organisasi.

```text
Cash Accounts
├── Kas RT
└── Kas Karang Taruna
```

## 11.1 Cash Account

Data:

- Nama.
- Organization.
- Saldo.
- Status.
- Deskripsi.

## 11.2 Transaction

Jenis:

```text
income
expense
```

Data:

- Cash account.
- Type.
- Amount.
- Category.
- Transaction date.
- Description.
- Attachment.
- Created by.
- Created at.

## 11.3 Category

Contoh pemasukan:

- Iuran.
- Donasi.
- Sumbangan.
- Bantuan.
- Pendapatan kegiatan.
- Lainnya.

Contoh pengeluaran:

- Kebersihan.
- Keamanan.
- Kegiatan.
- Peralatan.
- Konsumsi.
- Sosial.
- Perbaikan.
- Lainnya.

Kategori dapat dikelola secara dinamis.

---

# 12. Financial Rules

Operasi transaksi harus menggunakan database transaction.

Flow:

```text
Validate
   ↓
Database Transaction
   ↓
Create Transaction
   ↓
Update/Recalculate Balance
   ↓
Commit
```

Sistem harus mencegah:

- Nominal negatif.
- Cash account tidak valid.
- Transaksi antar organisasi.
- Double submission.
- Saldo tidak konsisten.

Transaksi final sebaiknya **tidak menggunakan hard delete**.

Jika transaksi perlu dibatalkan, gunakan:

```text
void
reversal
```

atau mekanisme koreksi transaksi.

---

# 13. Transparansi Keuangan

Admin dapat menentukan apakah keuangan ditampilkan ke publik.

Setting:

```text
public_finance = true / false
```

Jika aktif, website dapat menampilkan:

```text
Kas RT

Saldo
Rp 5.250.000

Pemasukan
Rp 2.000.000

Pengeluaran
Rp 1.250.000
```

Riwayat transaksi dapat ditampilkan berdasarkan pengaturan.

Informasi sensitif tidak boleh dipublikasikan.

---

# 14. Content Management System

Pengumuman, artikel, agenda, kegiatan, dan page menggunakan satu **Content System**.

Jenis:

```text
announcement
article
event
activity
page
```

Field umum:

```text
id
organization_id
title
slug
excerpt
content
thumbnail
status
published_at
author_id
created_at
updated_at
```

Status:

```text
draft
published
archived
```

Keuntungan:

- Tidak membuat editor berbeda untuk setiap modul.
- Reusable.
- Mudah menambahkan content type baru.
- Tidak perlu hardcode page.

---

# 15. Tiptap Editor

Editor utama menggunakan:

**Tiptap + Vue**

Tiptap menjadi editor standar untuk semua content.

Digunakan pada:

- Page.
- Article.
- Announcement.
- Event.
- Activity.

## 15.1 Basic Formatting

- Paragraph.
- Heading H1-H6.
- Bold.
- Italic.
- Strike.
- Underline.
- Link.
- Inline code.

## 15.2 Lists

- Bullet list.
- Ordered list.
- Task list.

## 15.3 Block

- Blockquote.
- Code block.
- Horizontal rule.

## 15.4 Alignment

- Left.
- Center.
- Right.
- Justify.

## 15.5 Media

- Image.
- YouTube/embed.
- File attachment.

## 15.6 Table

Tabel merupakan fitur wajib.

Fitur:

- Insert table.
- Add row.
- Delete row.
- Add column.
- Delete column.
- Merge cell.
- Split cell.
- Header row.
- Cell selection.
- Delete table.

Contoh:

```text
┌──────────┬──────────────┬──────────────────┐
│ Hari     │ Jadwal       │ Penanggung Jawab │
├──────────┼──────────────┼──────────────────┤
│ Senin    │ Ronda        │ Kelompok A       │
│ Selasa   │ Ronda        │ Kelompok B       │
│ Rabu     │ Ronda        │ Kelompok C       │
└──────────┴──────────────┴──────────────────┘
```

## 15.7 Utility

- Undo.
- Redo.
- Placeholder.
- Keyboard shortcuts.

---

# 16. Tiptap Storage

**Tiptap JSON menjadi source of truth.**

Database:

```text
contents.content JSON
```

Contoh:

```json
{
  "type": "doc",
  "content": [
    {
      "type": "heading",
      "attrs": {
        "level": 2
      },
      "content": [
        {
          "type": "text",
          "text": "Tentang RT"
        }
      ]
    }
  ]
}
```

HTML tidak menjadi source of truth.

HTML hanya merupakan hasil render dari Tiptap JSON.

Flow:

```text
Tiptap JSON
     ↓
Renderer
     ↓
HTML
     ↓
Public Website
```

---

# 17. Tiptap Image Upload

Gambar yang dimasukkan melalui editor harus menggunakan upload endpoint Laravel.

Flow:

```text
Tiptap Editor
      ↓
Select / Drag & Drop / Paste Image
      ↓
Laravel Upload Endpoint
      ↓
Validate File
      ↓
Store Media
      ↓
Return Media ID + URL
      ↓
Insert Image Node
```

Editor harus mendukung:

- File picker.
- Drag & drop.
- Paste image dari clipboard.

---

# 18. Media Management

Media disimpan sebagai entity terpisah.

Entitas:

```text
media
```

Data:

- ID.
- File path.
- Original filename.
- Stored filename.
- MIME type.
- Extension.
- File size.
- Width.
- Height.
- Alt text.
- Caption.
- Disk.
- Uploaded by.
- Created at.

Storage tidak boleh menyimpan file sebagai binary/blob di database.

Contoh storage:

```text
storage/
└── app/public/
    └── media/
        ├── images/
        │   └── 2026/
        │       └── 08/
        └── attachments/
            └── 2026/
                └── 08/
```

Struktur direktori dapat disesuaikan berdasarkan kebutuhan deployment.

---

# 19. Image Node

Image node Tiptap menyimpan referensi media.

Contoh:

```json
{
  "type": "image",
  "attrs": {
    "mediaId": 123,
    "src": "/storage/media/abc123.webp",
    "alt": "Kegiatan kerja bakti",
    "title": "Kerja Bakti RT"
  }
}
```

`mediaId` digunakan untuk mengetahui file sebenarnya.

`src` digunakan sebagai URL render.

---

# 20. Media Reference

Sistem harus dapat mengetahui media digunakan oleh content mana.

Tujuan:

- Mencegah penghapusan file yang masih digunakan.
- Mempermudah media management.
- Menghindari broken image.
- Mempermudah cleanup unused media.

Media tidak boleh langsung dihapus jika masih direferensikan oleh content.

Jika media tidak digunakan:

```text
unused media
      ↓
candidate for deletion
```

Penghapusan dapat dilakukan secara manual atau melalui cleanup process.

---

# 21. File Attachment

File attachment harus dipisahkan dari image editor.

Contoh:

```text
📎 Proposal Kegiatan.pdf
📎 Laporan Keuangan.xlsx
📎 SK Kepengurusan.pdf
```

Struktur:

```text
contents
   ↓
content_attachments
   ↓
media
```

`content_attachments`:

```text
id
content_id
media_id
display_name
sort_order
created_at
updated_at
```

Content dapat memiliki banyak attachment.

---

# 22. Attachment pada Tiptap

Tiptap dapat menyediakan node attachment khusus.

Contoh tampilan:

```text
┌────────────────────────────────────┐
│ 📎 Proposal Kegiatan.pdf            │
│ PDF • 2.4 MB                       │
│                         Download ↓ │
└────────────────────────────────────┘
```

Namun file tetap disimpan pada `media`.

Tiptap hanya menyimpan referensi:

```text
mediaId
```

Dengan demikian:

```text
File
 ↓
Media
 ↓
Content Attachment
 ↓
Tiptap Node
```

---

# 23. File Validation

Upload harus memiliki validasi:

- MIME type.
- Extension.
- Maximum size.
- Filename sanitization.
- Storage isolation.

Jenis file yang diizinkan harus ditentukan berdasarkan kebutuhan.

Contoh:

```text
Image:
jpg
jpeg
png
webp

Document:
pdf

Office:
doc
docx
xls
xlsx
ppt
pptx
```

File executable seperti:

```text
php
exe
sh
bat
js
```

tidak boleh diupload sebagai attachment.

Validasi harus dilakukan berdasarkan MIME type dan extension, bukan hanya nama file.

---

# 24. Markdown Import

Admin dapat mengupload:

```text
.md
.markdown
```

Flow:

```text
Upload Markdown
       ↓
Parse Markdown
       ↓
Convert ke Tiptap JSON
       ↓
Preview
       ↓
Save
```

Optional frontmatter:

```yaml
---
title: Tentang RT
slug: tentang-rt
status: published
---
```

Content:

```markdown
## Tentang Kami

RT 01 merupakan bagian dari...
```

---

# 25. Markdown Mode

Editor dapat menyediakan dua mode:

```text
[ Visual ] [ Markdown ]
```

### Visual Mode

Menggunakan Tiptap.

### Markdown Mode

Admin dapat mengedit Markdown secara langsung.

Keduanya tetap menghasilkan:

```text
Tiptap JSON
```

sebagai source of truth.

---

# 26. Markdown Export

Content dapat diekspor:

```text
Tiptap JSON
     ↓
Markdown Serializer
     ↓
.md
```

Tujuan:

- Backup.
- Portability.
- Developer workflow.
- Editing offline.
- Migrasi content.

---

# 27. Event / Agenda

Event digunakan untuk informasi kegiatan yang memiliki waktu tertentu.

Field:

- Content ID.
- Start date/time.
- End date/time.
- Location.
- Organization.
- Status.

Contoh:

```text
Kerja Bakti Lingkungan

23 Agustus 2026
07:00 WIB

Balai RT
```

Event dapat ditampilkan dalam:

- List.
- Calendar.
- Detail page.

---

# 28. Activity / Kegiatan

Activity digunakan untuk dokumentasi kegiatan.

Field:

- Content ID.
- Date.
- Location.
- Organization.
- Cover.
- Gallery.
- Video/link.

Activity memiliki banyak media:

```text
activity
    ↓
activity_media
    ↓
media
```

Dokumentasi tidak perlu menjadi CMS type terpisah.

---

# 29. Gallery

Gallery merupakan kumpulan media yang berhubungan dengan Activity.

Contoh:

```text
Kerja Bakti RT

[Image 1]
[Image 2]
[Image 3]
[Image 4]
```

Fitur:

- Upload multiple images.
- Sort.
- Caption.
- Alt text.
- Delete.
- Reorder.

---

# 30. Dashboard

## Super Admin

Menampilkan:

- Total user.
- Total content.
- Total kegiatan.
- Saldo Kas RT.
- Saldo Kas Karang Taruna.
- Content terbaru.
- Aktivitas terbaru.

## Admin RT

Menampilkan:

- Saldo Kas RT.
- Transaksi bulan berjalan.
- Content RT.
- Agenda mendatang.
- Kegiatan terbaru.
- Pengumuman aktif.

## Admin Karang Taruna

Menampilkan:

- Saldo Kas Karang Taruna.
- Jumlah anggota.
- Transaksi bulan berjalan.
- Agenda mendatang.
- Kegiatan terbaru.
- Content terbaru.

---

# 31. Public Website

Website publik tidak membutuhkan login.

Halaman minimal:

```text
/
├── Profil RT
├── Karang Taruna
├── Struktur Organisasi
├── Pengumuman
├── Agenda
├── Kegiatan
├── Artikel/Informasi
├── Galeri
├── Transparansi Kas
└── Custom Page
```

Content hanya ditampilkan apabila:

```text
status = published
```

---

# 32. Public Content

Content publik harus mendukung:

- SEO title.
- SEO description.
- Open Graph image.
- Canonical URL.
- Slug.
- Published date.

URL:

```text
/pengumuman/{slug}
/artikel/{slug}
/agenda/{slug}
/kegiatan/{slug}
/page/{slug}
```

---

# 33. Authentication

Dashboard membutuhkan authentication.

Fitur:

- Login.
- Logout.
- Forgot password.
- Reset password.
- Session management.

Registrasi publik tidak wajib.

User dibuat oleh Super Admin.

---

# 34. Authorization

Authorization wajib diterapkan pada:

1. Route.
2. Controller/action.
3. Policy.
4. Query/data scope.
5. UI.

Menyembunyikan menu saja tidak dianggap sebagai authorization.

Contoh:

Admin RT tidak boleh mengakses:

```text
/admin/karang-taruna/finance
```

meskipun URL dimasukkan secara manual.

---

# 35. Audit Log

Audit log digunakan untuk aktivitas penting.

Contoh:

```text
Admin RT
mengubah transaksi kas
Rp 500.000
```

atau:

```text
Admin Karang Taruna
menghapus content
"Turnamen Futsal"
```

Data:

- User.
- Action.
- Model.
- Model ID.
- Old values.
- New values.
- IP.
- User agent.
- Timestamp.

Audit log hanya dapat diakses Super Admin.

---

# 36. Settings

Super Admin dapat mengatur:

- Site name.
- Site description.
- Logo.
- Favicon.
- Contact.
- Social media.
- SEO.
- Public finance.
- General settings.

Contoh:

```text
site_name
site_description
site_logo
site_favicon
contact_email
contact_phone
public_finance
```

---

# 37. Database Entities

Entitas utama:

```text
users
roles
permissions

organizations
organization_periods
organization_positions
organization_members

cash_accounts
cash_categories
cash_transactions

contents
events
activities
activity_media

media
content_attachments

settings
audit_logs
```

Relasi utama:

```text
Organization
├── Periods
├── Positions
├── Members
├── Cash Accounts
├── Contents
├── Events
└── Activities

Cash Account
└── Transactions

Content
├── Attachments
└── Content References

Activity
└── Media

Media
├── Image
└── Attachment
```

---

# 38. Content Architecture

Content system menggunakan satu engine.

Content umum:

```text
contents
├── title
├── slug
├── excerpt
├── content
├── thumbnail
├── organization_id
├── author_id
├── status
└── published_at
```

Content khusus:

```text
events
├── content_id
├── start_at
├── end_at
└── location
```

```text
activities
├── content_id
├── activity_date
└── location
```

Pendekatan ini memungkinkan content engine tetap reusable tanpa membuat tabel `contents` terlalu kompleks.

---

# 39. Frontend Component Architecture

Komponen editor harus reusable.

Contoh:

```text
resources/js/
└── components/
    └── content-editor/
        ├── Editor.vue
        ├── Toolbar.vue
        ├── BubbleMenu.vue
        ├── LinkDialog.vue
        ├── ImageUploader.vue
        ├── AttachmentNode.vue
        ├── TableControls.vue
        └── extensions/
```

Penggunaan:

```vue
<ContentEditor v-model="form.content" />
```

Semua content type menggunakan editor yang sama.

---

# 40. Backend Architecture

Contoh struktur:

```text
app/
├── Actions/
├── Enums/
├── Http/
│   ├── Controllers/
│   ├── Requests/
│   └── Resources/
├── Models/
├── Policies/
├── Services/
└── Support/
```

Frontend:

```text
resources/js/
├── components/
├── layouts/
├── pages/
│   ├── admin/
│   │   ├── rt/
│   │   ├── karang-taruna/
│   │   ├── finance/
│   │   ├── content/
│   │   └── users/
│   └── public/
└── lib/
```

Jika aplikasi berkembang, struktur dapat dipindahkan ke pendekatan feature-based/module-based.

---

# 41. Technology Stack

## Backend

- Laravel.
- PHP.
- PostgreSQL atau MySQL.
- Laravel Storage.
- Laravel Queue jika dibutuhkan.
- Laravel Scheduler jika dibutuhkan.

## Frontend

- Vue.
- Inertia.js.
- Tailwind CSS.
- Tiptap.

## Editor

- Tiptap.
- ProseMirror extensions yang diperlukan.
- Markdown parser.
- Markdown serializer.

---

# 42. Security

Sistem harus:

- Menggunakan Laravel authentication.
- Menggunakan Policy/Gate.
- Menggunakan CSRF protection.
- Menggunakan rate limiting.
- Memvalidasi seluruh input.
- Memvalidasi upload.
- Membatasi ukuran file.
- Memvalidasi MIME type.
- Mengisolasi storage.
- Mencegah executable upload.
- Menggunakan database transaction untuk financial operation.
- Menghindari mass assignment.
- Mencegah IDOR.
- Mencegah XSS dari rich content.
- Men-sanitize output/content sesuai kebutuhan.
- Tidak mempercayai HTML dari client.

Tiptap content harus diperlakukan sebagai data yang tidak terpercaya sampai melalui proses rendering/sanitization yang sesuai.

---

# 43. Performance

Public website harus:

- Menggunakan pagination.
- Menghindari N+1 query.
- Mengoptimalkan image.
- Menggunakan lazy loading.
- Menggunakan responsive image jika memungkinkan.
- Menggunakan cache untuk content yang jarang berubah.
- Mengoptimalkan query dashboard.
- Menggunakan queue untuk proses upload/processing berat.

---

# 44. Image Processing

Untuk gambar yang diupload:

- Validasi dimensi.
- Validasi ukuran.
- Kompresi.
- Generate optimized version.
- Generate thumbnail jika diperlukan.

Format modern seperti WebP dapat digunakan untuk hasil render.

Original file dapat dipertahankan jika dibutuhkan.

---

# 45. Backup & Data Recovery

Sistem production harus memiliki strategi backup.

Minimal:

- Database backup.
- Media/file backup.
- Backup schedule.
- Retention policy.
- Restore procedure.

Database dan media harus dapat dipulihkan secara independen.

---

# 46. MVP Roadmap

## Phase 1 — Foundation

- Laravel setup.
- Vue + Inertia.
- Authentication.
- RBAC.
- User management.
- Organization.
- Settings.
- Dashboard dasar.

## Phase 2 — Organization

- Profil RT.
- Profil Karang Taruna.
- Periode kepengurusan.
- Struktur organisasi.
- Anggota Karang Taruna.

## Phase 3 — Content

- CMS.
- Tiptap.
- Media management.
- Image upload.
- Attachment.
- Table.
- Markdown import.
- Markdown export.
- Page.
- Article.
- Announcement.
- Event.
- Activity.
- Gallery.

## Phase 4 — Finance

- Cash account.
- Category.
- Income.
- Expense.
- Transaction.
- Balance.
- Financial report.
- Public transparency.

## Phase 5 — Public Website

- Homepage.
- Profil RT.
- Karang Taruna.
- Struktur organisasi.
- Content listing.
- Event.
- Activity.
- Gallery.
- Finance transparency.
- Custom page.

## Phase 6 — Hardening

- Audit log.
- Security review.
- Performance optimization.
- SEO.
- Image optimization.
- Backup.
- Responsive optimization.

---

# 47. Fitur di Luar MVP

Fitur berikut tidak perlu dibuat pada MVP:

- Forum warga.
- Polling.
- Marketplace warga.
- Lost & found.
- Chat.
- Push notification.
- Mobile application.
- Online payment.
- Iuran online.
- Administrasi surat.
- Pendataan penduduk.

Fitur dapat ditambahkan berdasarkan kebutuhan nyata setelah MVP digunakan.

---

# 48. Acceptance Criteria

MVP dianggap berhasil apabila:

### Organization

- Super Admin dapat mengelola RT dan Karang Taruna.
- Admin RT hanya dapat mengelola RT.
- Admin Karang Taruna hanya dapat mengelola Karang Taruna.
- Struktur organisasi dapat dibuat tanpa hardcode.
- Periode kepengurusan tersimpan.
- Histori kepengurusan tetap tersedia.
- Anggota Karang Taruna dapat dikelola.
- User dan anggota organisasi merupakan entitas terpisah.

### Finance

- Kas RT dapat dikelola.
- Kas Karang Taruna dapat dikelola.
- Pemasukan dapat dibuat.
- Pengeluaran dapat dibuat.
- Kategori dapat dikelola.
- Saldo dihitung secara konsisten.
- Transaksi final tidak dihapus secara sembarangan.
- Data antar organisasi tidak tercampur.
- Transparansi kas dapat diaktifkan/nonaktifkan.

### Content

- Content dapat dibuat.
- Content dapat diedit.
- Content dapat dipublish.
- Content dapat diarsipkan.
- Tiptap dapat digunakan.
- Image dapat diupload melalui editor.
- Image dapat di-drag & drop.
- Image dapat di-paste.
- Image tersimpan melalui Media Management.
- File attachment dapat ditambahkan.
- File attachment tersimpan melalui Media Management.
- Tabel dapat dibuat.
- Row/column dapat ditambah dan dihapus.
- Cell dapat di-merge/split.
- Markdown dapat diimport.
- Markdown dapat diekspor.
- Custom page dapat dibuat tanpa coding.

### Public Website

- Website dapat diakses tanpa login.
- Hanya published content yang tampil.
- Content memiliki SEO metadata.
- Activity dapat memiliki gallery.
- Image tampil responsive.
- Website responsive.

### Security

- Authorization diterapkan di backend.
- Admin tidak dapat mengakses organisasi lain.
- Upload file tervalidasi.
- Rich content tidak dapat digunakan untuk melakukan XSS.
- Audit log mencatat aktivitas penting.

---

# 49. Prinsip Pengembangan

## Simple First

Jangan membuat fitur kompleks jika kebutuhan belum ada.

## Reusable

Editor, media, finance engine, permission, dan content system harus reusable.

## Organization Isolation

Data RT dan Karang Taruna harus terisolasi.

## Server-side Authorization

Frontend bukan security boundary.

## No Hardcoded Content

Konten website dikelola melalui CMS.

## Financial Integrity

Transaksi keuangan harus konsisten dan dapat diaudit.

## Content as Data

Tiptap JSON menjadi source of truth.

## Media as Entity

File tidak disimpan sebagai bagian langsung dari content/database blob.

## Mobile Friendly

Dashboard harus nyaman digunakan melalui smartphone.

## Future Ready

Arsitektur harus memungkinkan penambahan modul tanpa rewrite besar.

---

# 50. Arsitektur Konseptual

```text
                         PUBLIC WEBSITE
                               │
                               ▼
                     ┌───────────────────┐
                     │  Content Renderer │
                     └─────────┬─────────┘
                               │
                         Tiptap JSON
                               │
              ┌────────────────┴────────────────┐
              │                                 │
        CONTENT SYSTEM                     MEDIA SYSTEM
              │                                 │
      ┌───────┼────────┐              ┌─────────┼─────────┐
      │       │        │              │         │         │
     Page   Article   Event         Images   Attachments Gallery
                       │
                    Activity
                       │
                    Gallery


                         ADMIN SYSTEM
                              │
              ┌───────────────┼────────────────┐
              │               │                │
             RT         KARANG TARUNA      SUPER ADMIN
              │               │                │
       ┌──────┼──────┐ ┌──────┼──────┐         │
       │      │      │ │      │      │         │
    Profile Structure Kas  Profile Structure Kas
       │      │      │ │      │      │         │
       └──────┴──────┘ └──────┴──────┘         │
              │               │                │
              └───────────────┼────────────────┘
                              │
                         RBAC / POLICY
                              │
                         Laravel Backend
                              │
                    ┌─────────┴─────────┐
                    │                   │
                 Database            Storage
```

---

# 51. Kesimpulan

Produk ini diposisikan sebagai:

> **Platform informasi, publikasi, organisasi, dokumentasi, dan transparansi keuangan lingkungan RT & Karang Taruna.**

Bukan aplikasi administrasi kependudukan.

Fondasi utama sistem:

```text
Laravel
+
Inertia
+
Vue
+
Tailwind
+
Tiptap
+
RBAC
+
Content Management
+
Media Management
+
Finance Engine
```

Dengan pendekatan tersebut:

- RT dan Karang Taruna tetap terpisah.
- Konten tidak hardcoded.
- Satu editor dapat digunakan di seluruh CMS.
- Markdown tetap didukung.
- Tiptap JSON menjadi source of truth.
- Gambar dan file memiliki sistem media tersendiri.
- Tabel tersedia di editor.
- Attachment tidak bercampur dengan image.
- Kas RT dan Karang Taruna menggunakan engine yang sama tetapi data tetap terisolasi.
- Struktur organisasi dapat berubah berdasarkan periode.
- Sistem tetap sederhana untuk MVP namun memiliki fondasi yang cukup untuk dikembangkan.