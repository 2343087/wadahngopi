<p align="center">
  <img src="public/wadahngopi.png" width="100" alt="WadahNgopi Logo">
</p>

<h1 align="center">WadahNgopi ☕</h1>

<p align="center">
  <strong>Platform Pencarian Cafe & Roastery №1 di Indonesia</strong><br>
  <em>Bukan sekadar direktori — ini pengalaman jelajah kopi yang imersif.</em>
</p>

<p align="center">
  <a href="https://laravel.com"><img src="https://img.shields.io/badge/Laravel-12-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 12"></a>
  <a href="https://livewire.laravel.com"><img src="https://img.shields.io/badge/Livewire-3-FB70A9?style=for-the-badge&logo=livewire&logoColor=white" alt="Livewire 3"></a>
  <a href="https://tailwindcss.com"><img src="https://img.shields.io/badge/Tailwind-v4-06B6D4?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind v4"></a>
  <a href="https://filamentphp.com"><img src="https://img.shields.io/badge/Filament-v3-EEBB0B?style=for-the-badge&logo=filament&logoColor=white" alt="Filament v3"></a>
  <a href="https://pestphp.com"><img src="https://img.shields.io/badge/Tests-198_Passed-22C55E?style=for-the-badge&logo=checkmarx&logoColor=white" alt="198 Tests Passed"></a>
  <img src="https://img.shields.io/badge/PWA-Ready-5A0FC8?style=for-the-badge&logo=pwa&logoColor=white" alt="PWA Ready">
</p>

---

## 🤔 Apa Itu WadahNgopi?

**WadahNgopi** adalah **Progressive Web App (PWA)** yang dirancang untuk menjadi portal pencarian cafe dan roastery paling lengkap di Indonesia, dimulai dari **Kalimantan Timur**.

### Masalah yang Diselesaikan

> _"Mau ngopi tapi bingung mau kemana? Buka Google Maps ribet, info jam buka gak update, review campur aduk. Scroll Instagram capek, belum tentu cafe-nya masih buka."_

WadahNgopi hadir untuk menyelesaikan masalah itu:

| ❌ Tanpa WadahNgopi | ✅ Dengan WadahNgopi |
|:-----|:-----|
| Buka Google Maps → scroll review satu-satu | Buka → langsung lihat semua cafe + status buka/tutup |
| Jam buka gak jelas, sering outdated | Jam operasional real-time (weekday/weekend) |
| Gak tau cafe mana yang deket | Filter "Terdekat" — otomatis deteksi lokasi GPS |
| Bingung milih? Scroll tanpa arah | **Cafe Roulette** — putar & dapetin rekomendasi random |
| Info kopi berserakan di IG/TikTok | Feed berita & edukasi kopi terkurasi dalam 1 tempat |
| Harus install app besar dari Play Store | **PWA** — install langsung dari browser, < 1MB |

### Target User

- 🧑‍💻 **Gen Z & Millennials** — yang suka nongkrong di cafe estetik
- ☕ **Coffee Enthusiast** — yang cari roastery lokal berkualitas
- 🏪 **Owner Cafe/Roastery** — yang mau eksposur digital gratis & fair
- 🌏 **Wisatawan** — yang berkunjung ke kota baru dan nyari spot ngopi

---

## 💎 Fitur & Kegunaan

### 🏪 Jelajahi Cafe — _"Temuin Spot Ngopi Favoritmu"_
- **Pencarian Cerdas** — Ketik nama/alamat/deskripsi. Pakai FULLTEXT search + fallback LIKE.
- **Filter Lokasi Terdekat** — Deteksi GPS otomatis, sort by jarak via Haversine formula.
- **Status Real-Time** — Lihat langsung mana yang **BUKA** atau **TUTUP** sekarang.
- **Filter Kota** — Jelajahi per kota: Balikpapan, Samarinda, Bontang, dll.
- **Sort A-Z / Z-A** — Urutkan berdasarkan nama.
- **Fair Play Ordering** — Shuffled random per-session, semua cafe dapat giliran tampil di atas.
- **Infinite Scroll** — Load data bertahap tanpa pindah halaman.

### 🏭 Roastery Hub — _"Cari Biji Kopi Lokal Terbaik"_
- **Database Roastery** — Halaman khusus untuk roastery & penyangrai kopi.
- **Fitur sama** dengan Jelajahi Cafe: search, filter, sort, lokasi terdekat.

### 🎯 Cafe Roulette — _"Bingung? Putar Aja!"_
- Tombol floating **"BINGUNG?"** di pojok bawah.
- Klik → muncul roda putar → kasih **rekomendasi cafe random** yang lagi buka.
- Animations premium: spin, confetti, haptic feedback.

### 📰 Info & Edukasi Kopi
- **Feed Artikel** — Berita kopi, edukasi brewing, info lomba, promo cafe.
- **Kategori** — Filter: Semua, Berita, Edukasi, Lomba, Promo.
- **Auto-Scraping** — Konten berita kopi ter-update otomatis dari sumber terpercaya.
- **View Counter** — Atomic batched write ke database (bukan per-klik).

### 🔖 Simpan Favorit
- **Bookmark** cafe & roastery — disimpan di `localStorage` (tanpa login).
- **Hapus** dengan konfirmasi modal yang smooth.
- **Cross-type** — Satu halaman menampilkan cafe DAN roastery favorit.

### 📱 Progressive Web App (PWA)
- **Installable** — Di Android: "Add to Home Screen". Di iOS: Share → Add to Home Screen.
- **Offline-capable** — Service Worker caching untuk assets.
- **App-like feel** — Full screen, tanpa address bar, splash screen saat buka.
- **Ultra ringan** — Bukan 100MB app dari Play Store, cuma < 1MB.

### 🛡️ Admin Panel (Filament v3)
- **Dashboard** — Kelola cafe, roastery, artikel, user dari satu tempat.
- **Role-Based Access** — Developer, Admin Cafe, Admin Roastery.
- **CRUD lengkap** — Tambah, edit, hapus, restore (soft delete).
- **Upload Gambar** — Untuk setiap cafe, roastery, dan artikel.

---

## 🧰 Tech Stack

| Layer | Teknologi | Kenapa Ini? |
|:------|:----------|:------------|
| **Backend** | Laravel 12 (PHP 8.4) | Framework #1 di PHP, ecosystem lengkap |
| **Frontend** | Livewire 3 + Alpine.js | SPA-like tanpa JavaScript framework berat |
| **Styling** | Tailwind CSS v4 + Custom CSS | Utility-first + glassmorphism, micro-animations |
| **Build Tool** | Vite 7 | Bundler tercepat, HMR instant |
| **Admin** | Filament v3 | Admin panel Laravel terbaik, zero config |
| **Database** | MySQL 8.0+ (Spatial + FULLTEXT) | Geo-queries native, full-text search |
| **Cache** | Redis | Cached shuffled IDs, view counters, total counts |
| **Testing** | Pest 4 | 198 tests, 335 assertions |
| **PWA** | Service Worker + Manifest | Installable, offline-first |

---

## 🛡️ Security & Engineering

```
✅ CSP Headers          — Content-Security-Policy di semua response
✅ Rate Limiting         — 300/min (web), 5/min (login), 30/min (API)
✅ Input Validation      — Server-side whitelist + SQL injection prevention
✅ IDOR Protection       — Owner hanya bisa akses data miliknya
✅ Soft Deletes          — Data bisa dipulihkan setelah dihapus
✅ Observer Pattern      — Cache invalidation otomatis saat data berubah
✅ Atomic View Counter   — Cache::pull() mencegah race condition
✅ Parameter Binding     — Semua raw SQL pakai prepared statements
```

---

## 🚀 Setup & Instalasi

### Prasyarat

| Software | Versi Minimum |
|:---------|:-------------|
| PHP | 8.2+ (rekomendasi 8.4) |
| Composer | 2.x |
| Node.js | 20+ |
| MySQL | 8.0+ |
| Redis | 6+ (opsional tapi direkomendasikan) |

---

### 💻 Setup di Local (Development)

#### Menggunakan Laravel Herd (Windows/Mac) — Recommended

```bash
# 1. Clone repository
git clone https://github.com/2343087/wadahngopi.git
cd wadahngopi

# 2. Install PHP dependencies
composer install

# 3. Install Node.js dependencies
npm install

# 4. Setup environment
cp .env.example .env
php artisan key:generate
```

Edit file `.env`:
```env
APP_NAME=WadahNgopi
APP_URL=http://wadahngopi.test

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=wadahngopi
DB_USERNAME=root
DB_PASSWORD=

CACHE_STORE=redis
REDIS_HOST=127.0.0.1
```

```bash
# 5. Buat database & jalankan migrasi
php artisan migrate --seed

# 6. Buat admin user
php artisan make:filament-user

# 7. Build frontend assets
npm run build

# 8. Jalankan server (atau pakai Herd)
php artisan serve
```

Buka `http://wadahngopi.test` (Herd) atau `http://localhost:8000`.

#### Menggunakan XAMPP / Laragon

Sama seperti di atas, tapi:
- Buat database `wadahngopi` manual di phpMyAdmin
- Untuk Redis, install Redis for Windows atau skip (Laravel fallback ke `file` cache)
- Jalankan dengan `php artisan serve`

---

### 🌐 Deploy ke Hostinger (Production)

#### Step 1: Persiapan di Hostinger

1. **Beli hosting** — Minimal paket **Premium** (mendukung SSH + Node.js).
2. **Buat database MySQL** di panel Hostinger → catat nama DB, username, password.
3. **Setup domain** — Arahkan domain ke hosting Hostinger.
4. **Aktifkan SSH** — Di panel Hostinger: Advanced → SSH Access.

#### Step 2: Upload Kode

**Opsi A: Via Git (Recommended)**
```bash
# SSH ke server Hostinger
ssh -p 65002 u123456@ssh.hostinger.com

# Pindah ke public_html
cd domains/wadahngopi.com/public_html

# Clone repo
git clone https://github.com/2343087/wadahngopi.git .
```

**Opsi B: Via ZIP Upload**
1. Compress lokal project (tanpa `vendor/` dan `node_modules/`)
2. Upload via File Manager Hostinger ke `public_html/`
3. Extract

#### Step 3: Install Dependencies

```bash
# Di SSH Hostinger
cd domains/wadahngopi.com/public_html

# Install PHP dependencies (tanpa dev)
composer install --no-dev --optimize-autoloader

# Install Node.js & build assets
npm install
npm run build
```

#### Step 4: Konfigurasi Environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` di File Manager Hostinger:
```env
APP_NAME=WadahNgopi
APP_ENV=production
APP_DEBUG=false
APP_URL=https://wadahngopi.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=u123456_wadahngopi    # dari panel Hostinger
DB_USERNAME=u123456_admin         # dari panel Hostinger
DB_PASSWORD=your_db_password      # dari panel Hostinger

CACHE_STORE=file                  # Hostinger shared hosting gak ada Redis
SESSION_DRIVER=file
QUEUE_CONNECTION=sync

SESSION_SECURE_COOKIE=true
SESSION_DOMAIN=.wadahngopi.com
```

> **⚠️ Note:** Shared hosting Hostinger biasanya **tidak ada Redis**. Laravel akan otomatis pakai `file` cache. Kalau pakai **Hostinger VPS**, bisa install Redis sendiri.

#### Step 5: Jalankan Migrasi & Optimize

```bash
# Migrasi database
php artisan migrate --force

# Buat admin user
php artisan make:filament-user

# Optimize untuk production
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan icons:cache

# Set permissions
chmod -R 755 storage bootstrap/cache
```

#### Step 6: Setup Document Root

Di Hostinger panel: **Websites → Manage → Advanced → Folder Index**

Arahkan document root ke folder `public/`:
```
domains/wadahngopi.com/public_html/public
```

Atau buat `.htaccess` di root:
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

#### Step 7: Setup Scheduler (Opsional)

Di Hostinger panel → **Advanced → Cron Jobs**:
```
* * * * * cd /home/u123456/domains/wadahngopi.com/public_html && php artisan schedule:run >> /dev/null 2>&1
```

Ini akan menjalankan:
- `app:flush-view-counters` — Sinkronisasi view counter tiap 5 menit
- `app:scrape-coffee-news` — Auto-scrape berita kopi 2x sehari

---

## 🧪 Testing

```bash
# Jalankan semua test
php artisan test --compact

# Output:
# Tests: 198 passed (335 assertions)
# Duration: ~28s
```

| Area Test | Cakupan |
|:----------|:--------|
| Search & Filter | ExploreSearch, RoasterySearch, CafeSearchService |
| Security | XSS, SQL Injection, CSP, IDOR, Role-based |
| Performance | Cache invalidation, view counter, query optimization |
| Models | Cafe, Roastery, Information, City |
| Edge Cases | Empty data, overflow, boundaries, concurrent access |

---

## 🏆 Kenapa WadahNgopi Lebih Unggul?

### vs Google Maps
| | Google Maps | WadahNgopi |
|:---|:-----------|:-----------|
| **Fokus** | Semua tempat | ☕ Khusus cafe & roastery |
| **Jam Buka** | Sering outdated | ✅ Admin update real-time |
| **UI/UX** | Generic | ✅ Glassmorphism premium, Gen-Z friendly |
| **Rekomendasi** | Based on reviews | ✅ Fair Play — setiap cafe dapat giliran |
| **Fitur Unik** | ❌ | ✅ Cafe Roulette, Feed Edukasi Kopi |
| **Install** | Bawaan HP | ✅ PWA < 1MB |

### vs Instagram / TikTok
| | Instagram/TikTok | WadahNgopi |
|:---|:----------------|:-----------|
| **Data Terstruktur** | ❌ Scroll tanpa arah | ✅ Filter kota, status, jarak |
| **Info Jam Buka** | ❌ Harus DM | ✅ Langsung terlihat |
| **Bisa Cari** | ❌ Search by hashtag doang | ✅ FULLTEXT search nama + alamat |
| **Fair Exposure** | ❌ Yang viral menang | ✅ Random adil per-session |
| **Offline** | ❌ | ✅ PWA + Service Worker |

### vs Zomato / TripAdvisor
| | Zomato/TripAdvisor | WadahNgopi |
|:---|:-------------------|:-----------|
| **Fokus Regional** | Global, data Indo kurang | ✅ Deep focus Kalimantan & Indonesia |
| **Kecepatan** | ❌ App berat | ✅ Web PWA, instant load |
| **Biaya Listing** | 💰 Berbayar untuk featured | ✅ 100% gratis untuk semua cafe |
| **Admin** | ❌ Hanya via form | ✅ Filament dashboard lengkap |
| **Kopi-Spesifik** | ❌ Restoran juga | ✅ Roastery hub + edukasi kopi |

### vs Aplikasi Sejenis (Mapia, Pergi.com)
| | Kompetitor | WadahNgopi |
|:---|:----------|:-----------|
| **Tech Stack** | Legacy | ✅ Laravel 12 + Livewire 3 (modern) |
| **PWA** | ❌ Native app saja | ✅ PWA — install dari browser |
| **Open Source** | ❌ | ✅ Bisa di-fork & dikembangkan |
| **Testing** | ❓ | ✅ 198 automated tests |
| **Cafe Roulette** | ❌ | ✅ Fitur unik signature |

---

## 📂 Struktur Project

```
wadahngopi/
├── app/
│   ├── Console/Commands/     # Artisan commands (scraper, view counter)
│   ├── Http/Controllers/     # Web controllers
│   ├── Filament/Resources/   # Admin panel CRUD
│   ├── Livewire/             # Interactive components
│   ├── Models/               # Eloquent models
│   ├── Observers/            # Cache invalidation
│   ├── Policies/             # Authorization rules
│   └── Services/             # Business logic (search)
├── resources/
│   ├── css/                  # Modular CSS architecture (24 files)
│   ├── js/                   # Alpine.js + app bootstrap
│   └── views/
│       ├── layouts/          # App shell + bottom nav
│       ├── livewire/         # 7 Livewire views
│       └── components/       # Skeleton + onboarding
├── tests/Feature/            # 198 automated tests
└── public/                   # Built assets + PWA manifest
```

---

## 🤝 Kontribusi

Tertarik ikut mengembangkan WadahNgopi? Fork repo ini dan submit Pull Request. Pastikan:
1. Semua test tetap **pass** (`php artisan test`)
2. Tidak merusak UI/UX yang sudah ada
3. Ikuti coding standard project (PSR-12)

---

## 📄 Lisensi

Project ini bersifat **proprietary** — hak milik AK Kreatif. Untuk penggunaan komersial atau kolaborasi, hubungi developer.

---

<p align="center">
  <strong>© 2026 WadahNgopi — by AK Kreatif</strong><br>
  <em>Diseduh dengan ❤️ dan baris kode presisi dari Kalimantan.</em><br><br>
  <code>☕ brew(); code(); repeat();</code>
</p>
