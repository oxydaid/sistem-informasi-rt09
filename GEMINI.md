 # 🧠 Laravel + Inertia.js + Vue Engineering Standard

> **Purpose:** Engineering guideline untuk pengembangan aplikasi/web modern berbasis Laravel dengan Inertia.js + Vue.
>
> **Primary goals:** maintainability, security, scalability, performance, accessibility, SEO, testability, observability, dan production readiness.
>
> **Principle:** Gunakan aturan ini sebagai engineering standard. Tidak semua teknik harus diterapkan secara berlebihan; pilih architecture dan tooling berdasarkan kompleksitas serta kebutuhan bisnis.

---

# 1. 🎯 Engineering Principles

Setiap project harus mengikuti prinsip berikut:

### Core Principles

- **Requirement before implementation**
- **Simple by default**
- **Complexity must be justified**
- **Business logic harus terisolasi dari presentation layer**
- **Security by default**
- **Database integrity harus dijaga di level database**
- **Test critical business behavior**
- **Observable in production**
- **Performance measured, bukan diasumsikan**
- **Accessibility dan SEO dipertimbangkan sejak awal**
- **Deployment harus reproducible dan dapat di-rollback**
- **Avoid premature abstraction**
- **Prefer explicit code daripada magic yang berlebihan**

### Prioritas Engineering

Urutan prioritas:

```text
Correctness
    ↓
Security
    ↓
Maintainability
    ↓
Reliability
    ↓
Performance
    ↓
Developer Experience
```

Performance tidak boleh mengorbankan correctness atau security tanpa alasan yang jelas.

---

# 2. 🏷️ Status Aturan

Dokumentasi ini menggunakan tiga tingkat aturan:

### 🔴 REQUIRED

Wajib diterapkan kecuali terdapat alasan teknis yang terdokumentasi.

### 🟡 RECOMMENDED

Direkomendasikan sebagai default, tetapi dapat disesuaikan dengan kebutuhan project.

### 🟢 OPTIONAL

Digunakan jika memang memberikan manfaat nyata.

Contoh:

```text
Database transaction
→ REQUIRED jika satu business operation mengubah
  beberapa state yang harus atomic.

Redis
→ RECOMMENDED jika application membutuhkan caching,
  queue, rate limiting, atau distributed state.

DTO
→ RECOMMENDED ketika data melewati boundary/layer
  dan struktur datanya kompleks.

Repository pattern
→ OPTIONAL.
```

---

# 3. 🧱 Technology Stack

Default stack:

- Laravel — stable version yang compatible dengan project
- PHP — supported stable version sesuai Laravel version
- Inertia.js + Vue 3
- Tailwind CSS v4
- MySQL / MariaDB
- PostgreSQL jika dibutuhkan
- Redis jika dibutuhkan
- Vite
- Pest / PHPUnit
- Laravel Pint
- PHPStan / Larastan

---

# 4. 🔄 Version Policy

Jangan mengunci guideline terhadap nomor versi PHP/Laravel secara permanen.

Gunakan:

```text
Laravel:
stable major version yang didukung dan compatible

PHP:
versi PHP yang didukung Laravel dan dependency project

Node:
versi yang compatible dengan Vite/Tailwind/project

Database:
versi stable yang compatible dengan Laravel
```

### Rules

- Project baru menggunakan stable release yang sesuai.
- Jangan melakukan major upgrade hanya karena versi baru tersedia.
- Evaluasi compatibility sebelum upgrade.
- `composer.lock` harus di-commit untuk application project.
- Dependency harus diaudit secara berkala.
- CI harus menggunakan environment yang setara dengan production.
- Major dependency upgrade harus melalui test suite.

---

# 5. 🧭 Project Workflow

Workflow standar:

```text
Requirement
    ↓
Clarification
    ↓
PRD
    ↓
Architecture Decision
    ↓
Database Design
    ↓
Implementation Plan
    ↓
Backend / Domain
    ↓
Frontend
    ↓
Testing
    ↓
Security Review
    ↓
Performance / SEO / Accessibility
    ↓
CI
    ↓
Staging
    ↓
Production
    ↓
Monitoring
```

Jangan langsung coding jika requirement masih ambigu dan ambiguity tersebut dapat memengaruhi architecture atau database.

---

# 6. 0️⃣ Requirement Analysis

Sebelum membuat database:

### Identifikasi

- Tujuan aplikasi
- Target pengguna
- Actor/user
- Role dan permission
- Fitur utama
- Fitur pendukung
- Business rules
- Workflow utama
- Data yang disimpan
- Data sensitif
- Kebutuhan SEO
- Kebutuhan App Settings
- Kebutuhan Admin Panel
- Kebutuhan API
- Integrasi pihak ketiga
- Kebutuhan notification
- Kebutuhan queue
- Kebutuhan reporting
- Kebutuhan audit log
- Kebutuhan deployment
- Kebutuhan scalability

### Output

Buat PRD singkat:

```text
Project Goal
Actors
Core Features
Business Rules
Main User Flows
Data Requirements
External Integrations
Security Requirements
SEO Requirements
Deployment Requirements
```

---

# 7. 1️⃣ Tahap Diskusi Awal

Sebelum coding:

1. Jelaskan kembali requirement.
2. Breakdown fitur.
3. Identifikasi ambiguity.
4. Identifikasi business rules.
5. Tentukan actor dan authorization.
6. Tentukan architecture.
7. Tentukan database relationship.
8. Tentukan App Settings.
9. Tentukan kebutuhan API.
10. Tentukan integrasi eksternal.
11. Tentukan kebutuhan queue.
12. Tentukan kebutuhan testing.
13. Tentukan deployment strategy.

Jika terdapat keputusan architecture yang signifikan, dokumentasikan alasan keputusan tersebut.

Gunakan pendekatan:

```text
Requirement
    ↓
Decision
    ↓
Reason
    ↓
Trade-off
```

---

# 8. 🏗️ Architecture Strategy

Gunakan architecture yang proporsional terhadap kompleksitas project.

## Small Project

Struktur sederhana diperbolehkan:

```text
app/
├── Http/
├── Models/
├── Policies/
├── Services/
└── ...
```

## Medium / Complex Project

Boleh berkembang menjadi:

```text
app/
├── Actions/
├── Domain/
├── Data/
├── Http/
├── Jobs/
├── Listeners/
├── Models/
├── Notifications/
├── Policies/
├── Services/
└── ...
```

Jangan membuat Clean Architecture yang sangat kompleks untuk aplikasi CRUD sederhana.

---

# 9. 🧩 Application Layer

Gunakan layer berdasarkan kebutuhan.

```text
Route
  ↓
Controller / Inertia
  ↓
Action / Application Service
  ↓
Domain / Model / Service
  ↓
Database / External Service
```

Tidak semua request membutuhkan seluruh layer tersebut.

---

# 10. ⚙️ Action / Use Case

Gunakan Action class untuk business operation yang jelas memiliki satu tujuan.

Contoh:

```text
CreateOrder
UpdateProfile
PublishArticle
ApprovePayment
GenerateInvoice
CancelSubscription
```

Contoh struktur:

```text
app/Actions/
├── CreateOrder.php
├── PublishArticle.php
└── GenerateInvoice.php
```

### Gunakan Action jika:

- business operation memiliki beberapa langkah
- dipanggil dari beberapa entry point
- membutuhkan transaction
- memiliki side effect
- perlu di-test secara terisolasi

Jangan membuat Action hanya untuk membungkus satu baris CRUD sederhana.

---

# 11. 🧠 Service Layer

Service digunakan jika memang terdapat logic yang membutuhkan abstraction berkelanjutan.

Contoh:

```text
PaymentGatewayService
ShippingService
ImageProcessingService
NotificationService
```

Service sangat cocok untuk:

- external API
- reusable business capability
- complex domain operation
- infrastructure abstraction

Jangan membuat:

```text
UserService.php
PostService.php
ProductService.php
```

hanya karena setiap model dianggap wajib memiliki service.

---

# 12. 🗄️ Database Design

Database harus dirancang sebelum implementasi feature yang bergantung pada struktur data tersebut.

Gunakan:

```bash
php artisan make:model NamaModel -m
```

Migration harus:

- jelas
- reproducible
- memiliki foreign key yang tepat
- memiliki index yang relevan
- memiliki unique constraint jika diperlukan
- memperhatikan nullability
- memperhatikan cascade behavior

---

# 13. 🔐 Database Integrity

Jangan hanya mengandalkan application code.

Gunakan database constraint jika relevan:

- Foreign key
- Unique constraint
- Index
- NOT NULL
- Check constraint jika supported/relevan

Contoh:

```php
$table->foreignId('user_id')
    ->constrained()
    ->cascadeOnDelete();
```

Unique:

```php
$table->string('slug')->unique();
```

Database harus menjadi lapisan terakhir untuk menjaga integrity.

---

# 14. 📊 Database Indexing

Index dibuat berdasarkan query pattern aktual.

Pertimbangkan index untuk:

- `WHERE`
- `JOIN`
- `ORDER BY`
- `GROUP BY`
- foreign key
- unique lookup

Jangan memberikan index ke semua kolom secara otomatis.

Index memiliki cost terhadap:

- INSERT
- UPDATE
- DELETE
- storage

Untuk query kritikal, gunakan query analysis / `EXPLAIN` bila diperlukan.

---

# 15. 🔑 Primary Key Strategy

Tidak ada satu primary key strategy yang wajib untuk semua project.

### Auto Increment

Gunakan jika:

- internal relational database
- tidak membutuhkan public identifier
- sederhana dan efisien

### UUID / ULID

Pertimbangkan jika:

- identifier diekspos ke public
- membutuhkan distributed ID generation
- ingin mengurangi predictable enumeration
- sistem terdistribusi

Jangan menggunakan UUID/ULID hanya karena dianggap lebih modern tanpa kebutuhan.

---

# 16. 🏷️ Slug Strategy

Jika entity memiliki slug:

- slug harus unique jika menjadi URL identifier
- slug harus konsisten
- slug harus divalidasi
- perubahan slug harus mempertimbangkan SEO
- pertimbangkan redirect dari slug lama

Contoh:

```text
/blog/cara-membuat-website
```

Jika slug dapat berubah, pertimbangkan histori/redirect:

```text
old-slug → 301 → new-slug
```

Tidak wajib menggunakan satu metode tertentu seperti observer atau service. Pilih metode yang paling maintainable untuk project.

---

# 17. 🖼️ Media & File Management

Semua upload file harus:

### Required

- validasi file
- validasi ukuran
- validasi MIME/type sesuai kebutuhan
- storage abstraction
- filename collision prevention
- authorization
- lifecycle management

### Filename

Default gunakan identifier yang aman dan unik:

```text
UUID/ULID + extension
```

Contoh:

```text
01KABCXYZ.webp
```

Slug-based filename hanya digunakan jika memang memiliki manfaat SEO/UX yang jelas.

### Storage

Gunakan abstraction Laravel Storage:

```text
local
public
S3
S3-compatible
```

Jangan mengikat business logic ke filesystem tertentu.

---

# 18. 🗑️ File Lifecycle

Jika file terkait dengan record:

```text
Create
 ↓
Upload
 ↓
Save path
```

Update:

```text
New file
 ↓
Save new file
 ↓
Delete old file
```

Delete:

```text
Delete record
 ↓
Delete associated file
```

Perhatikan transaction boundary dan failure scenario.

Jika deletion file harus reliable, pertimbangkan asynchronous cleanup/job.

---

# 19. ⚙️ App Settings — REQUIRED

> **App Settings tetap merupakan komponen wajib dalam project sesuai engineering requirement ini.**

App Settings digunakan untuk **application-level settings yang dapat dikontrol melalui aplikasi/admin**, bukan sebagai tempat penyimpanan infrastructure secrets.

---

## 19.1 Tujuan App Settings

App Settings digunakan untuk:

- Branding
- SEO defaults
- Theme
- Social media
- Website configuration
- Business-facing configuration
- Feature-level settings yang memang perlu diubah admin

Contoh:

```text
Admin Panel
    ↓
App Settings
    ↓
Database
    ↓
Cached Settings
    ↓
Application
```

---

# 20. ⚙️ App Settings Structure

Minimal settings:

## Branding

```text
app_name
app_description
logo
favicon
default_share_image
```

## SEO

```text
default_meta_title
default_meta_description
meta_keywords
default_og_title
default_og_description
default_og_image
```

## Analytics

```text
ga4_measurement_id
```

Analytics hanya di-inject jika setting tersedia dan valid.

## Theme

```text
primary_color
secondary_color
accent_color
background_color
```

Tidak semua warna harus tersedia jika tidak diperlukan.

## Social Media

```text
facebook_url
instagram_url
twitter_url
github_url
tiktok_url
linkedin_url
youtube_url
whatsapp_number
whatsapp_url
```

Gunakan hanya field yang relevan dengan project.

---

# 21. 🔐 App Settings ≠ Secrets

Jangan menyimpan infrastructure/application secrets di App Settings.

### ❌ Jangan

```text
database_password
aws_secret_access_key
stripe_secret
private_key
mail_password
```

### ✅ Gunakan

```text
.env
Laravel configuration
secret manager
deployment secrets
```

App Settings adalah **runtime application configuration**, bukan secret management system.

---

# 22. ⚡ App Settings Caching

App Settings yang sering dibaca harus menggunakan cache.

Concept:

```text
Application
    ↓
Settings Service
    ↓
Cache
    ↓
Database
```

Contoh konsep cache key:

```text
app_settings
```

Cache harus memiliki:

- TTL yang sesuai
- invalidation strategy
- clear mechanism setelah admin update

Jangan melakukan database query untuk App Settings pada setiap request jika dapat dihindari.

---

# 23. 🧩 App Settings Service

Gunakan abstraction seperti:

```text
SettingsService
```

Contoh API konseptual:

```php
settings()->get('app_name');
```

atau:

```php
app(SettingsService::class)->get('app_name');
```

Tujuannya:

- centralized access
- caching
- testing
- future storage changes
- menghindari query langsung di view

---

# 24. 🌐 Global App Settings

App Settings boleh di-share ke presentation layer secara global jika memang diperlukan.

Namun hindari query database langsung dari `AppServiceProvider`.

Prefer:

```text
AppServiceProvider
    ↓
SettingsService
    ↓
Cache
    ↓
Database
```

bukan:

```text
AppServiceProvider
    ↓
AppSetting::first()
```

pada setiap application boot tanpa caching.

---

# 25. 🎨 Dynamic Theme System

Tailwind CSS v4 menggunakan pendekatan CSS-first.

Gunakan CSS custom properties untuk runtime theme.

Concept:

```css
:root {
    --color-primary: ...;
    --color-secondary: ...;
}
```

App Settings dapat menjadi sumber nilai tersebut.

Jangan mengubah Tailwind build configuration setiap kali admin mengganti warna.

Architecture:

```text
App Settings
     ↓
CSS Variables
     ↓
Tailwind utilities / application styles
```

Theme harus tetap:

- predictable
- accessible
- contrast-aware
- cacheable

---

# 26. 👤 Authentication & Authorization

Authentication dan authorization harus dibedakan.

```text
Authentication
→ Siapa user?

Authorization
→ Apa yang boleh dilakukan user?
```

Gunakan:

- Laravel authentication
- Policies
- Gates
- Roles/permissions jika diperlukan

Authorization tidak boleh hanya dilakukan di frontend.

---

# 27. 🛡️ Policy

Gunakan Policy untuk authorization terhadap model/resource.

Contoh:

```text
PostPolicy
OrderPolicy
UserPolicy
```

Authorization harus dilakukan di server-side.

Jangan mengandalkan:

```text
hidden button
disabled button
frontend condition
```

sebagai security boundary.

---

# 28. 📝 Validation

Validation harus dilakukan di application boundary.

### Controller/API

Gunakan:

```text
Form Request
```

### Inertia + Vue

Gunakan Laravel validation sebagai source of truth:
```text
Form Request / Validator
        ↓
Laravel Controller
        ↓
Inertia validation errors
        ↓
Vue form state
```

Gunakan `useForm` untuk state form, submission, processing state, dan validation errors bila sesuai.
### Domain-specific validation

Gunakan:

```text
Rule Object
Validator
Domain logic
```

Jangan memaksakan Form Request ke seluruh Inertia component jika tidak memberikan manfaat.

---

# 29. 🔒 Mass Assignment

Mass assignment harus dikontrol.

Gunakan:

```text
$fillable
```

atau strategy `$guarded` yang konsisten.

Jangan:

```php
Model::create($request->all());
```

Prefer:

```php
$data = $request->validated();

Model::create($data);
```

---

# 30. 🔄 Database Transaction

Gunakan transaction jika satu business operation memodifikasi beberapa state database yang harus atomic.

Contoh:

```text
Create Order
    ↓
Create Order Items
    ↓
Update Stock
    ↓
Create Payment
```

Gunakan:

```php
DB::transaction(function () {
    // business operation
});
```

Transaction sangat dianjurkan untuk:

- financial operation
- inventory
- order processing
- multi-record state change
- critical workflow

---

# 31. 📡 Events & Listeners

Gunakan Events untuk decouple side effects.

Contoh:

```text
OrderPaid
    ├── SendNotification
    ├── UpdateAnalytics
    └── SendWebhook
```

Jangan membuat satu Service menjadi terlalu besar hanya karena semua side effect dimasukkan ke dalamnya.

Gunakan Events ketika:

- banyak consumer
- side effect tidak perlu synchronous
- ingin decoupling
- workflow dapat berkembang

---

# 32. 📨 Jobs & Queue

Gunakan Queue untuk pekerjaan yang:

- lambat
- berat
- tidak harus synchronous
- membutuhkan retry
- berhubungan dengan external service
- mengirim email dalam jumlah besar
- image processing
- PDF generation
- import/export
- webhook processing

Contoh:

```text
GenerateInvoice
ProcessImage
SendBulkEmail
SyncExternalOrder
ProcessWebhook
```

Queue job sebaiknya memiliki:

- timeout
- retry strategy
- backoff
- failure handling
- idempotency jika memungkinkan

---

# 33. 🔁 Idempotency

Wajib dipertimbangkan untuk:

- payment
- webhook
- external API
- queue job
- order processing
- retryable operation

Contoh:

```text
External transaction ID
        ↓
Unique constraint
        ↓
Process only once
```

Jangan menganggap request external hanya akan datang satu kali.

---

# 34. 🌐 External API Integration

Gunakan Laravel HTTP Client.

Structure:

```text
app/Services/
└── PaymentGatewayService.php
```

atau integration-specific structure jika project besar.

Service bertanggung jawab untuk:

- request
- authentication
- timeout
- retry
- response mapping
- error handling
- logging yang aman

Jangan menaruh API integration langsung di Vue atau Controller.

---

# 35. 🚨 Exception Handling

Jangan menggunakan `try/catch` di semua method.

Gunakan `try/catch` jika perlu:

- recovery
- fallback
- retry
- transform exception
- domain-specific exception
- custom logging/context

Jika exception tidak dapat ditangani, biarkan error handling global Laravel menangani sesuai konfigurasi.

---

# 36. 🧾 DTO

DTO digunakan jika:

- data melewati beberapa layer
- external API response kompleks
- application boundary membutuhkan structure yang jelas
- service layer kompleks

Jangan membuat DTO hanya untuk mengganti array sederhana yang hanya digunakan sekali.

---

# 37. 🧱 Value Objects

Gunakan Value Object jika domain memiliki concept yang jelas.

Contoh:

```text
Money
EmailAddress
PhoneNumber
Address
Percentage
```

Value Object cocok jika terdapat:

- validation
- invariant
- behavior
- formatting
- domain semantics

---

# 38. 🧮 Enum

Gunakan PHP Enum untuk finite domain states jika membantu readability dan type safety.

Contoh:

```text
OrderStatus
PaymentStatus
UserRole
SubscriptionStatus
```

Daripada string literal yang tersebar:

```php
'pending'
'paid'
'cancelled'
```

gunakan enum jika domain memang membutuhkannya.

---

# 39. 🖥️ Controller

Controller harus tipis.

Ideal:

```text
Request
 ↓
Validation
 ↓
Action/Service
 ↓
Response
```

Controller tidak boleh menjadi tempat utama untuk:

- business logic kompleks
- external API implementation
- file processing kompleks
- multi-step transaction
- notification orchestration yang kompleks

---

# 40. ⚡ Inertia.js

Gunakan Inertia.js sebagai bridge antara Laravel dan Vue untuk aplikasi web yang membutuhkan SPA-like navigation tanpa membangun API terpisah untuk setiap halaman.

Cocok untuk:
- page navigation
- CRUD
- filtering dan search
- form submission
- validation
- pagination
- dashboard
- authenticated application
- server-driven page data

Best practice:
- Laravel tetap menjadi source of truth untuk routing, authorization, validation, dan business logic.
- Controller mengembalikan `Inertia::render()` dengan props yang memang dibutuhkan page.
- Hindari mengirim seluruh model/database record jika hanya sebagian data diperlukan.
- Gunakan partial reloads, deferred props, lazy data, dan shared data secara selektif.
- Jangan menjadikan Inertia sebagai pengganti domain/application layer.
- Jangan membuat endpoint JSON tambahan hanya untuk kebutuhan page jika Inertia sudah dapat memenuhi kebutuhan tersebut.

Architecture:
```text
Browser
  ↓
Vue Page / Component
  ↓
Inertia.js
  ↓
Laravel Route / Controller
  ↓
Form Request / Action / Service
  ↓
Domain / Model
  ↓
Database / External Service
```

Untuk endpoint API yang memang dikonsumsi mobile, third-party, atau client non-Inertia, tetap gunakan API architecture yang terpisah.


# 41. 🏝️ Vue 3

Gunakan Vue 3 untuk presentation dan client-side interaction.

Gunakan:
- Composition API
- `<script setup>`
- typed props/emits jika menggunakan TypeScript
- composables untuk logic UI yang benar-benar reusable
- local component state untuk state yang hanya dibutuhkan component
- Pinia hanya jika global client state memang dibutuhkan

Architecture:
```text
Inertia
→ page/server state dan navigation

Vue
→ presentation dan browser interaction

Composable
→ reusable client-side behavior

Pinia
→ global client state yang memang membutuhkan store
```

Hindari:
- business logic domain di component Vue
- authorization hanya di Vue
- query/database access dari frontend
- state global untuk state yang sebenarnya hanya local component state
- membuat composable/store hanya untuk membungkus satu operasi sederhana.


# 42. 🧱 Vue Components

Vue component bertanggung jawab atas presentation dan client-side interaction.

Vue component tidak boleh:
- query database
- melakukan business logic domain yang kompleks
- menyimpan secret
- menentukan authorization sebagai security boundary
- memproses data server dalam jumlah besar secara tidak perlu

Gunakan:
```text
Pages/
Layouts/
Components/
Composables/
```

Page component menerima data melalui Inertia props.
Reusable component menerima data melalui props dan berkomunikasi melalui emits/events yang jelas.

Server-side business logic tetap berada di Laravel.


# 43. 🧩 Reusable Components

Komponen UI yang digunakan berulang kali harus dibuat reusable.

Contoh:

```text
Button
Modal
Input
Select
Alert
Card
Table
Pagination
EmptyState
```

Jangan membuat abstraction terlalu dini jika component hanya digunakan sekali dan belum memiliki pattern yang stabil.

---

# 44. 🎨 Frontend Standards

Default:
- Tailwind CSS v4
- Inertia.js
- Vue 3
- Composition API + `<script setup>`
- semantic HTML
- responsive design
- reusable components
- accessible interaction
- visible focus states
- consistent spacing
- consistent typography
- TypeScript jika project membutuhkannya / mengadopsinya
- dark mode jika diperlukan

Inertia/Vue principles:
- Server state berasal dari Laravel/Inertia.
- Client UI state berada di Vue.
- Jangan menduplikasi source of truth tanpa kebutuhan.
- Gunakan `useForm` untuk form Inertia.
- Gunakan shared props secara hemat; jangan mengirim data global yang besar ke setiap page.
- Gunakan lazy/deferred props untuk data mahal yang tidak diperlukan pada initial render.
- Gunakan partial reload bila hanya sebagian props yang perlu diperbarui.
- Gunakan `Link` dan router Inertia untuk navigation internal agar tetap SPA-like.
- Gunakan normal HTML/form behavior bila Inertia tidak memberikan manfaat nyata.

Jika UI/UX tidak ditentukan client:
```text
Gunakan modern UI/UX yang sederhana,
accessible, responsive, dan sesuai konteks aplikasi.
```

Jangan mengejar tren visual jika mengorbankan usability.



Default:

- Tailwind CSS v4
- semantic HTML
- responsive design
- reusable components
- accessible interaction
- visible focus states
- consistent spacing
- consistent typography
- dark mode jika diperlukan

Jika UI/UX tidak ditentukan client:

```text
Gunakan modern UI/UX yang sederhana,
accessible, responsive, dan sesuai konteks aplikasi.
```

Jangan mengejar tren visual jika mengorbankan usability.

---

# 45. 🔍 SEO

Untuk public-facing website, SEO harus dirancang sejak awal.

Minimal:

- dynamic title
- meta description
- canonical URL
- robots meta
- OpenGraph
- Twitter/X Card
- semantic HTML
- sitemap
- robots.txt

---

# 46. 🧠 SEO Defaults + Entity SEO

Gunakan App Settings untuk global SEO defaults.

Concept:

```text
Global App Settings
        ↓
Page/Post/Product
        ↓
Specific SEO settings
        ↓
Fallback to global settings
```

Contoh:

```text
post.seo_title
      ↓ fallback
settings.default_meta_title
```

Dengan demikian App Settings tetap menjadi fondasi global tanpa menghalangi SEO per entity.

---

# 47. 🔗 URL Strategy

Prefer:

```text
/blog/cara-membuat-website
```

daripada:

```text
/blog/123
```

jika slug memiliki nilai SEO/UX.

Tetapi ID internal tetap boleh digunakan jika:

- route tidak public
- resource internal
- slug tidak memberikan manfaat
- performance/query simplicity lebih penting

---

# 48. 🧩 Structured Data

Implementasikan jika relevan.

Contoh:

```text
Organization
WebSite
Article
Product
Breadcrumb
FAQ
LocalBusiness
```

Jangan membuat structured data yang tidak sesuai dengan actual content.

---

# 49. 🗺️ Sitemap & Robots

Public website yang membutuhkan indexing harus memiliki:

```text
/sitemap.xml
/robots.txt
```

Sitemap harus:

- hanya berisi URL yang valid
- tidak memasukkan noindex pages
- tidak memasukkan halaman private
- memiliki canonical URL yang benar

---

# 50. ♿ Accessibility

Minimal target WCAG basic.

### Required

- semantic HTML
- label untuk input
- alt text
- keyboard navigation
- visible focus
- sufficient contrast
- heading hierarchy
- accessible error message
- accessible modal
- accessible interactive controls

Contoh:

```html
<label for="email">Email</label>

<input
    id="email"
    type="email"
    aria-describedby="email-error"
/>
```

Error:

```html
<p id="email-error">
    Email wajib diisi.
</p>
```

---

# 51. 📈 Performance

Performance harus berdasarkan measurement.

Prioritas:

```text
Server response
Database
Payload
Images
JavaScript
CSS
Third-party scripts
Caching
Network
```

---

# 52. 🚀 Asset Optimization

Gunakan:
- Vite production build
- minification
- tree-shaking
- dependency minimization
- dynamic import jika memang dibutuhkan
- code splitting jika relevan
- lazy/deferred Inertia props untuk data yang mahal
- partial reload untuk update data yang terbatas

Jangan menambahkan library frontend hanya untuk fitur yang dapat dilakukan dengan native browser/Tailwind/Vue/Inertia.


# 53. 🖼️ Image Optimization

Gunakan:

- AVIF/WebP jika sesuai
- responsive images
- correct dimensions
- compression
- appropriate sizing
- lazy loading untuk image di luar viewport

### Jangan lazy-load LCP/hero image secara sembarangan.

Image yang menjadi LCP harus diprioritaskan agar dapat dimuat secepat mungkin.

---

# 54. 🗃️ Query Optimization

Wajib memperhatikan:

- N+1
- eager loading
- pagination
- index
- unnecessary query
- duplicate query
- large result set

Gunakan:

```php
Model::with(['relation'])->get();
```

jika relationship memang digunakan.

Jangan eager-load relationship yang tidak diperlukan.

---

# 55. 🔎 N+1 Prevention

Development harus memperhatikan N+1.

Gunakan tooling/debugging untuk menemukan query yang tidak diperlukan.

Critical pages harus diperiksa:

```text
Query count
Query duration
Payload
Relationship loading
```

---

# 56. 🧮 Eloquent vs Query Builder

### Eloquent

Prefer untuk:

- model behavior
- relationships
- CRUD
- domain-oriented data access

### Query Builder

Gunakan ketika:

- aggregation
- reporting
- bulk operations
- complex query
- performance-sensitive query

### Raw SQL

Gunakan hanya jika memang diperlukan.

Jika menggunakan raw SQL:

- parameter harus aman
- alasan penggunaan harus jelas
- query harus dapat dipahami dan di-test

Query Builder bukan bad practice.

---

# 57. ⚡ Caching

Caching digunakan jika terdapat measurable benefit.

Candidate:

- App Settings
- expensive query
- expensive computation
- external API response
- static/rarely changing data

Setiap cache harus memiliki:

```text
Cache Key
TTL
Invalidation Strategy
Fallback
```

Jangan melakukan caching tanpa memahami invalidation.

---

# 58. 🌐 HTTP / Network Optimization

Pertimbangkan:

- HTTP/2 atau HTTP/3
- Brotli/Gzip
- CDN
- cache headers
- browser caching
- preconnect untuk domain eksternal yang benar-benar dibutuhkan
- DNS prefetch jika relevan

Jangan menambahkan preconnect/prefetch secara berlebihan.

---

# 59. 📊 Core Web Vitals

Performance harus memperhatikan:

```text
LCP
INP
CLS
```

Jangan hanya mengejar Lighthouse score.

Perhatikan juga real-user experience jika data tersedia.

---

# 60. 🎯 PageSpeed / Lighthouse Target

Baseline target:

| Category | Target |
|---|---:|
| Performance | 90+ |
| Accessibility | 90+ |
| Best Practices | 90+ |
| SEO | 90+ |

Target ideal:

| Category | Ideal |
|---|---:|
| Performance | 95–100 |
| Accessibility | 95–100 |
| Best Practices | 95–100 |
| SEO | 95–100 |

### Important

Score bukan satu-satunya production gate.

Jika score rendah:

1. identifikasi penyebab
2. pisahkan first-party dan third-party impact
3. optimasi issue yang actionable
4. dokumentasikan exception jika memang ada
5. ukur kembali

Third-party service yang tidak dapat dikontrol tidak boleh menjadi alasan untuk mengabaikan performance, tetapi juga harus dibedakan dari masalah aplikasi sendiri.

---

# 61. 🛡️ Security Baseline

Wajib memperhatikan:

- HTTPS
- CSRF
- authorization
- authentication
- password hashing
- rate limiting
- secure session
- secure file upload
- XSS prevention
- SQL injection prevention
- mass assignment
- sensitive data handling
- security headers
- environment secrets

---

# 62. 🔐 Secure Headers

Pertimbangkan:

```text
Content-Security-Policy
Strict-Transport-Security
X-Content-Type-Options
Referrer-Policy
Permissions-Policy
```

Konfigurasi harus disesuaikan dengan application architecture.

Jangan menggunakan CSP secara copy-paste tanpa menguji compatibility dengan Inertia, Vue, analytics, third-party resources, dan asset strategy.

---

# 63. 🔑 Secrets Management

Jangan commit:

```text
.env
production credentials
private keys
API secrets
database password
cloud credentials
```

Gunakan:

```text
Environment variables
CI/CD secrets
Secret manager
```

`config/*.php` digunakan sebagai application configuration abstraction, bukan tempat menyimpan secret literal.

---

# 64. 🚦 Rate Limiting

Rate limiting wajib dipertimbangkan untuk:

- login
- password reset
- public API
- search endpoint yang mahal
- OTP
- webhook
- expensive operation
- public form

Rate limit harus disesuaikan dengan use case.

---

# 65. 📋 Audit Log

Audit log digunakan jika business requirement membutuhkannya.

Sangat dianjurkan untuk:

- financial changes
- role/permission changes
- administrative destructive actions
- approval workflow
- sensitive data modification
- status changes yang critical

Tidak semua CRUD harus diaudit.

---

# 66. 🗑️ Soft Delete

Gunakan `SoftDeletes` hanya jika business requirement membutuhkan:

- restore
- historical visibility
- accidental deletion recovery

Jangan otomatis menggunakan SoftDeletes pada semua model.

Pertimbangkan konsekuensi terhadap:

- unique constraint
- query
- relationship
- storage
- data retention

---

# 67. 🧪 Testing Strategy

Testing adalah bagian dari architecture.

Gunakan testing berdasarkan behavior.

## Unit Test

Untuk:

- calculator
- value object
- domain rule
- isolated business logic

## Feature Test

Untuk:

- HTTP endpoint
- authentication
- authorization
- validation
- database behavior
- Inertia response dan Vue/browser behavior

## Integration Test

Untuk:

- external API
- payment gateway
- storage
- queue integration

## Browser / E2E Test

Untuk critical user journeys:

```text
Registration
Login
Checkout
Payment
Critical Admin Workflow
```

Tidak semua feature membutuhkan semua jenis test.

---

# 68. 🧪 Test Priority

Prioritaskan test untuk:

```text
Financial logic
Authorization
Authentication
Data mutation
Critical business rules
Payment
Order
Inventory
External integrations
```

UI cosmetic change tidak membutuhkan testing sebesar business-critical operation.

---

# 69. 🧹 Code Quality

Gunakan:

### Formatting

```text
Laravel Pint
```

### Static Analysis

```text
PHPStan / Larastan
```

### Testing

```text
Pest / PHPUnit
```

Pint adalah formatter/code-style tool, bukan static analyzer.

---

# 70. 🧑‍💻 PHP Code Standards

Gunakan:

```php
declare(strict_types=1);
```

untuk application code jika project/team menerapkannya secara konsisten.

Prefer:

- typed properties
- parameter types
- return types
- enums
- readonly jika sesuai
- explicit naming
- small methods
- meaningful classes

Hindari:

- generic `$data` tanpa alasan
- generic `$item`
- method terlalu panjang
- nested condition berlebihan
- magic behavior yang tidak perlu

---

# 71. 🏷️ Naming Convention

### Model

```text
User
BlogPost
OrderItem
```

### Table

```text
users
blog_posts
order_items
```

### Controller

```text
BlogPostController
OrderController
```

### Request

```text
StoreBlogPostRequest
UpdateBlogPostRequest
```

### Policy

```text
BlogPostPolicy
```

### Job

```text
GenerateInvoice
ProcessWebhook
```

### Action

```text
CreateOrder
PublishArticle
```

### Event

```text
OrderPaid
```

### Listener

```text
SendOrderPaidNotification
```

### Service

```text
PaymentGatewayService
```

---

# 72. 🔌 API Standards

Jika project memiliki API, tentukan:

- authentication
- authorization
- versioning
- validation
- pagination
- filtering
- sorting
- response structure
- error format
- rate limiting
- idempotency
- resource transformation
- documentation

API tidak boleh mengekspos database schema secara mentah tanpa alasan.

---

# 73. 🔑 API Authentication

Authentication harus disesuaikan dengan client.

Contoh:

```text
Browser
→ Session authentication

Mobile
→ Token-based authentication

Third-party integration
→ API key / OAuth / appropriate mechanism

Internal service
→ Appropriate service authentication
```

Jangan memilih authentication mechanism hanya berdasarkan popularitas.

---

# 74. 📦 API Response

Gunakan response yang konsisten.

Concept:

```json
{
    "data": {},
    "meta": {},
    "links": {}
}
```

Error harus memiliki format yang predictable.

Jangan mengembalikan stack trace atau sensitive exception detail ke production client.

---

# 75. 📧 Notifications

Gunakan Notification system untuk:

- email
- database notification
- broadcast
- SMS/external notification

Notification yang berat sebaiknya dikirim melalui queue.

---

# 76. 🔔 Flash Messages

UI mutation yang berhasil/gagal dapat menggunakan flash message.

Pastikan:

- message jelas
- tidak membocorkan internal error
- accessible
- tidak hanya mengandalkan warna

---

# 77. 📄 Error Pages

WAJIB menyediakan:

```text
404
403
419
429
500
503
```

Jika relevan.

Error page harus:

- consistent dengan branding
- responsive
- accessible
- tidak menampilkan sensitive debug information
- memberikan navigation yang masuk akal

---

# 78. 🧭 Admin Panel

Dokumentasi ini **tidak mengunci project pada Filament atau framework admin panel tertentu**.

Jika project membutuhkan admin panel, implementasikan dengan Laravel + Inertia.js + Vue atau gunakan solusi admin panel lain yang memang dipilih secara eksplisit oleh project.

Admin panel harus memiliki:
- authentication
- authorization
- role/permission jika diperlukan
- resource/page yang jelas
- form
- table
- filters
- search
- validation
- appropriate dashboard

Admin UI bukan security boundary. Semua authorization wajib tetap diverifikasi di server-side.

---

# 79. 📊 Admin Dashboard

Dashboard tidak harus memiliki widget tertentu secara otomatis.

Gunakan widget berdasarkan kebutuhan:
```text
Stats
Charts
Recent Activity
Pending Orders
Revenue
System Health
```

Jangan membuat chart hanya agar dashboard terlihat penuh.
Informasi harus actionable.

---

# 80. ⚙️ App Settings UI

App Settings tetap menjadi komponen wajib.

UI pengelolaan settings dapat dibuat menggunakan:
```text
Laravel Controller
        ↓
Inertia.js
        ↓
Vue Page / Form
        ↓
SettingsService
        ↓
Cache
        ↓
Database
```

Settings page harus:
- authorized
- validated
- cache-aware
- memiliki clear UX
- tidak mengekspos secrets yang tidak seharusnya dikelola melalui UI


# 81. 🧩 Third-Party Integration

Setiap external integration harus memiliki:

- dedicated service/client
- timeout
- retry strategy jika relevan
- error handling
- logging
- credential management
- test strategy
- fallback/recovery jika dibutuhkan

Contoh:

```text
Payment
Discord
WhatsApp
Shipping
Email Provider
Storage
Analytics
```

---

# 82. 📝 Logging

Logging harus memberikan context yang cukup.

Log:

- critical errors
- external integration failures
- important business events
- queue failures
- security-relevant events

Jangan log:

```text
password
API secret
access token
credit card data
sensitive personal information
```

Logging harus aman terhadap data leakage.

---

# 83. 🔭 Observability

Production-ready application harus dapat diamati.

Minimal pertimbangkan:

```text
Application Logs
Error Tracking
Queue Monitoring
Health Check
Slow Query Monitoring
```

Jika aplikasi cukup besar:

```text
Metrics
Tracing
Performance Monitoring
Alerting
```

---

# 84. ❤️ Health Check

Production application sebaiknya memiliki health endpoint/health mechanism.

Health check dapat memeriksa:

```text
Application
Database
Cache
Queue
Critical dependency
```

Jangan memasukkan informasi sensitif ke response health endpoint.

---

# 85. 🐞 Development Tooling

Gunakan tooling yang sesuai environment.

Development dapat menggunakan:

```text
Debugbar
Telescope
Query monitoring
IDE/static analysis
```

Jangan mengaktifkan debugging detail pada production.

---

# 86. 🚀 CI — Continuous Integration

Setiap push/PR sebaiknya menjalankan:

```text
Install dependencies
        ↓
Environment setup
        ↓
Lint / Pint
        ↓
Static analysis
        ↓
Tests
        ↓
Security checks
        ↓
Build assets
```

---

# 87. 🛑 CI Gates

Merge harus diblokir jika critical pipeline gagal.

Minimal:

```text
Code formatting
Static analysis
Test suite
Build
```

Security scanning diterapkan sesuai risk profile project.

---

# 88. 🔐 Dependency Security

Secara berkala:

- audit Composer dependencies
- audit npm dependencies
- update vulnerable packages
- review abandoned packages
- review major updates
- test dependency upgrades

Jangan melakukan blind update semua package ke latest.

---

# 89. 🚀 CD — Continuous Deployment

Production deployment ideal:

```text
Commit
 ↓
CI
 ↓
Build
 ↓
Artifact
 ↓
Deploy
 ↓
Migration
 ↓
Cache optimization
 ↓
Health check
 ↓
Release
```

Jika health check gagal:

```text
Rollback / recovery procedure
```

---

# 90. 🗃️ Database Migration Strategy

Migration harus:

- deterministic
- reversible jika memungkinkan
- aman terhadap production data
- diuji sebelum deployment

Untuk zero-downtime deployment:

```text
Prefer backward-compatible migration.
```

Hindari perubahan schema yang memaksa old application version langsung berhenti bekerja selama deployment.

---

# 91. 🔄 Deployment Rollback

Production harus memiliki rollback strategy.

Minimal dokumentasikan:

```text
Application rollback
Database migration recovery
Asset rollback
Queue handling
Cache clearing
```

Jangan menganggap rollback hanya:

```bash
git checkout previous-commit
```

Database migration bisa membuat rollback lebih kompleks.

---

# 92. 🕒 Laravel Scheduler

Gunakan Laravel Scheduler untuk scheduled tasks.

Production harus memiliki scheduler execution mechanism.

Shared hosting dapat menggunakan cron.

Contoh architecture:

```text
Cron
 ↓
Laravel Scheduler
 ↓
Scheduled Jobs
```

---

# 93. 🔄 Queue Worker

Untuk VPS/container:

```text
Nginx
PHP-FPM
Queue Worker
Supervisor/systemd/container process manager
Redis jika digunakan
```

Jika menggunakan Horizon, worker management harus disesuaikan dengan Horizon requirements.

---

# 94. 🖥️ VPS Deployment

Recommended baseline:

```text
Linux
Nginx
PHP-FPM
Laravel
Database
Redis (if required)
Queue Worker
Supervisor/systemd
SSL
```

Gunakan:

```text
HTTPS
Firewall
SSH hardening
Automated backup
Monitoring
Log rotation
```

---

# 95. 🐳 Docker

Docker/Compose dapat digunakan jika project membutuhkan:

- reproducible environment
- multiple services
- consistent development
- container deployment
- CI parity

Jangan menggunakan Docker hanya karena "modern".

Architecture harus tetap mudah dipahami oleh team.

---

# 96. 🌐 Shared Hosting

Shared hosting diperbolehkan jika application requirements mendukungnya.

Pertimbangkan:

- public directory
- storage symlink
- cron
- queue limitation
- PHP version
- extensions
- database
- filesystem permission

Project yang membutuhkan:

```text
Horizon
long-running workers
Redis-heavy architecture
WebSocket infrastructure
high-volume queue
```

lebih cocok menggunakan VPS/container/cloud.

---

# 97. 💾 Backup

Production harus memiliki backup strategy.

Minimal:

```text
Database backup
File/media backup
Configuration recovery
```

Backup harus:

- automated
- tested
- memiliki retention policy
- disimpan di lokasi berbeda jika memungkinkan

Backup yang tidak pernah diuji bukan recovery strategy.

---

# 98. 🔒 Production Environment

Production harus terpisah dari:

```text
local
testing
staging
```

Minimal:

```text
Environment
Database
Storage
Credentials
Queue
Cache
```

tidak boleh tercampur antar environment.

---

# 99. 📦 Production Optimization

Sebelum production:

```text
composer install --no-dev --optimize-autoloader
```

Gunakan Laravel optimization commands yang sesuai dengan deployment strategy.

Pastikan:

- config cache
- route cache jika sesuai
- view cache
- optimized assets

Tidak semua optimization harus dilakukan secara manual jika deployment pipeline telah menanganinya.

---

# 100. 🧪 Staging

Project medium/large sebaiknya memiliki staging environment.

Flow:

```text
Feature Branch
    ↓
Pull Request
    ↓
CI
    ↓
Staging
    ↓
QA
    ↓
Production
```

Staging harus semirip mungkin dengan production.

---

# 101. 📋 Production Readiness Checklist

Sebelum release:

## Application

- [ ] Requirement selesai
- [ ] Business rules tervalidasi
- [ ] Database migration reviewed
- [ ] Authorization reviewed
- [ ] Validation reviewed
- [ ] Error handling reviewed
- [ ] Critical workflow tested

## Security

- [ ] HTTPS
- [ ] Secrets tidak committed
- [ ] Authorization
- [ ] Rate limiting
- [ ] Secure upload
- [ ] Security headers
- [ ] Production debug disabled

## Database

- [ ] Foreign keys
- [ ] Unique constraints
- [ ] Required indexes
- [ ] Transaction untuk critical operations
- [ ] Backup strategy

## Frontend

- [ ] Responsive
- [ ] Accessibility
- [ ] Loading state
- [ ] Error state
- [ ] Empty state
- [ ] Keyboard navigation
- [ ] Semantic HTML

## SEO

- [ ] Title
- [ ] Meta description
- [ ] Canonical
- [ ] OpenGraph
- [ ] Twitter/X Card
- [ ] Sitemap
- [ ] Robots
- [ ] Structured data jika relevan

## Performance

- [ ] N+1 reviewed
- [ ] Images optimized
- [ ] Assets optimized
- [ ] Critical JS minimized
- [ ] Cache reviewed
- [ ] Core Web Vitals reviewed
- [ ] Lighthouse reviewed

## Testing

- [ ] Unit tests
- [ ] Feature tests
- [ ] Integration tests jika diperlukan
- [ ] Critical browser tests jika diperlukan
- [ ] CI passed

## Deployment

- [ ] Production environment ready
- [ ] Migration strategy
- [ ] Queue worker
- [ ] Scheduler
- [ ] SSL
- [ ] Backup
- [ ] Health check
- [ ] Rollback strategy
- [ ] Monitoring

---

# 102. 📊 Quality Gates

Project dinyatakan production-ready jika:

### Required

```text
Tests pass
CI pass
No critical security issue
Database integrity verified
Authorization verified
Production configuration verified
Backup available
Deployment tested
Critical user flows tested
```

### Recommended

```text
Lighthouse ≥ 90
Accessibility ≥ 90
SEO ≥ 90
Best Practices ≥ 90
Performance ≥ 90
```

Score dapat memiliki exception yang terdokumentasi jika terdapat third-party limitation atau business requirement tertentu.

---

# 103. 🧠 Architecture Decision Record

Untuk keputusan architecture penting, dokumentasikan:

```text
Decision
Context
Alternatives
Reason
Trade-offs
Consequences
```

Contoh:

```text
Decision:
Gunakan Redis untuk queue dan cache.

Reason:
Application memiliki background jobs tinggi dan
memerlukan shared cache.

Alternative:
Database queue.

Trade-off:
Menambah infrastructure dependency.
```

---

# 104. 🚫 Anti-Patterns

Hindari:

```text
$request->all()
```

untuk data yang tidak tervalidasi.

Hindari:

```text
DB query langsung di Vue
```

Hindari:

```text
Business logic kompleks di Controller
```

Hindari:

```text
Business logic kompleks di Vue component
```

Hindari:

```text
Mengirim atau mengambil data server berulang kali tanpa alasan
```

Hindari:

```text
Service class ratusan/ribuan baris
```

Hindari:

```text
Repository pattern hanya karena "best practice"
```

Hindari:

```text
Interface untuk setiap class tanpa kebutuhan
```

Hindari:

```text
Cache tanpa invalidation strategy
```

Hindari:

```text
SoftDeletes untuk semua model
```

Hindari:

```text
UUID untuk semua table tanpa alasan
```

Hindari:

```text
Dashboard widget hanya untuk memenuhi checklist
```

Hindari:

```text
Lighthouse score sebagai satu-satunya ukuran performance
```

---

# 105. 🧩 Decision Rules

Gunakan decision tree berikut.

## Perlu Service?

```text
Simple CRUD?
→ Tidak perlu.

Complex business logic?
→ Pertimbangkan Action.

External integration?
→ Service/Integration class.

Reusable domain capability?
→ Service.
```

## Perlu DTO?

```text
Simple local array?
→ Tidak perlu.

Cross-layer structured data?
→ Pertimbangkan DTO.

External API mapping?
→ DTO sangat dianjurkan.

Domain concept dengan invariant?
→ Pertimbangkan Value Object.
```

## Perlu Queue?

```text
Harus synchronous?
→ Jangan queue.

Lambat / berat?
→ Queue.

External API?
→ Pertimbangkan queue.

Email bulk?
→ Queue.

Image/PDF processing?
→ Queue.
```

## Perlu Cache?

```text
Data murah?
→ Tidak perlu.

Query mahal dan sering dipanggil?
→ Pertimbangkan cache.

Data jarang berubah?
→ Cache cocok.

Tidak punya invalidation strategy?
→ Jangan cache dulu.
```

---

# 106. 📁 Recommended Project Organization

Untuk medium/large Laravel project:

```text
app/
├── Actions/
├── Console/
├── Data/
├── Domain/
├── Enums/
├── Exceptions/
├── Http/
│   ├── Controllers/
│   ├── Middleware/
│   └── Requests/
├── Jobs/
├── Listeners/
├── Models/
├── Notifications/
├── Policies/
├── Rules/
├── Services/
└── Support/
```

Tidak semua directory harus ada.

Buat directory ketika memang dibutuhkan.

---

# 107. 📦 Resources & Presentation

Untuk API:

```text
Http/Resources
```

digunakan jika diperlukan untuk mengontrol API representation.

Jangan expose model/database object secara langsung jika API contract membutuhkan abstraction.

---

# 108. 🔄 Code Review Standard

Setiap code review harus memperhatikan:

### Correctness

- Apakah behavior benar?
- Edge case?

### Security

- Authorization?
- Validation?
- Sensitive data?

### Database

- Query?
- Index?
- Transaction?
- N+1?

### Maintainability

- Naming?
- Complexity?
- Duplication?
- Appropriate abstraction?

### Testing

- Apakah behavior critical sudah diuji?

### Performance

- Query?
- Payload?
- Unnecessary rendering?

---

# 109. 📝 Documentation

Project harus memiliki dokumentasi minimum:

```text
README
Installation
Environment variables
Database setup
Development commands
Testing
Build
Deployment
Queue/Scheduler
External integrations
App Settings
Architecture decisions jika relevan
```

README harus dapat membantu developer baru menjalankan project.

---

# 110. 🤖 AI / Coding Agent Workflow

Jika dokumentasi ini digunakan sebagai instruction untuk AI coding agent:

### AI tidak boleh langsung coding ketika:

- requirement ambiguous
- database relationship belum jelas
- authorization belum jelas
- critical business rule belum diketahui
- external integration behavior belum diketahui

### AI harus:

1. memahami requirement
2. mengidentifikasi ambiguity
3. memberikan architecture plan
4. memberikan database plan
5. meminta confirmation jika keputusan signifikan
6. implementasi secara bertahap
7. menambahkan validation
8. menambahkan authorization
9. menambahkan test
10. melakukan review
11. menjelaskan trade-off

---

# 111. 🤖 AI Coding Rules

AI harus:

- mengikuti existing project conventions
- membaca code sebelum mengubahnya
- tidak membuat abstraction tanpa kebutuhan
- tidak menghapus existing behavior tanpa alasan
- tidak mengganti dependency tanpa alasan
- tidak melakukan major upgrade dependency tanpa permission
- tidak membuat migration destructive tanpa warning
- tidak menambahkan library jika native Laravel solution cukup
- tidak memasukkan secret ke source code
- tidak membuat query langsung di Vue
- tidak menggunakan `$request->all()` untuk trusted persistence
- tidak melewati authorization
- tidak melewati validation
- tidak mengabaikan existing tests

---

# 112. 🔍 Final Review

Sebelum menyatakan feature selesai:

```text
Requirement
    ↓
Business Rule
    ↓
Database
    ↓
Authorization
    ↓
Validation
    ↓
Business Logic
    ↓
UI
    ↓
Testing
    ↓
Security
    ↓
Performance
    ↓
SEO
    ↓
Accessibility
    ↓
Observability
    ↓
Deployment
```

Semua layer yang relevan harus diperiksa.

---

# 113. 🏁 Definition of Done

Feature dianggap selesai jika:

- [ ] Requirement terpenuhi
- [ ] Business rule benar
- [ ] Database sesuai
- [ ] Migration aman
- [ ] Validation tersedia
- [ ] Authorization tersedia
- [ ] Business logic berada pada layer yang tepat
- [ ] UI responsive
- [ ] Accessibility diperhatikan
- [ ] Loading/error/empty state tersedia
- [ ] Test tersedia untuk behavior penting
- [ ] Tidak ada critical security issue
- [ ] Tidak ada obvious N+1
- [ ] Performance reasonable
- [ ] SEO diterapkan jika public-facing
- [ ] Logging/observability tersedia jika diperlukan
- [ ] CI passed
- [ ] Deployment impact reviewed
- [ ] Documentation diperbarui jika diperlukan

---

# 114. 🏆 Prinsip Developer

Developer harus:

- berpikir sebelum coding
- memahami business requirement
- memilih architecture berdasarkan complexity
- tidak over-engineering
- tidak under-engineering
- menjaga database integrity
- menjaga security
- menjaga maintainability
- menulis test untuk behavior penting
- mengukur performance
- memperhatikan accessibility
- memperhatikan SEO
- memikirkan deployment sejak awal
- membuat system yang observable
- menjelaskan technical decision
- mendokumentasikan trade-off

---

# 115. 🧠 Final Engineering Philosophy

Gunakan prinsip berikut sebagai pedoman utama:

```text
Simple when possible.
Structured when necessary.
Secure by default.
Validated at boundaries.
Authorized on the server.
Consistent at the database level.
Tested where behavior matters.
Queued when work does not need to block requests.
Cached when measurement justifies it.
Observable in production.
Accessible by default.
SEO-aware for public content.
Deployable and recoverable.
```
