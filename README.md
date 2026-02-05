# ☕️ WadahNgopi
> **Portal Cafe Terbaik di Kalimantan - Modern, Luxury, & Minimalist**

[![Laravel 12](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel)](https://laravel.com)
[![PHP 8.4](https://img.shields.io/badge/PHP-8.4-777BB4?style=for-the-badge&logo=php)](https://php.net)
[![Filament v3](https://img.shields.io/badge/Filament-v3-EEBB0B?style=for-the-badge&logo=filament)](https://filamentphp.com)
[![Tailwind v4](https://img.shields.io/badge/Tailwind_CSS-v4.0-06B6D4?style=for-the-badge&logo=tailwind-css)](https://tailwindcss.com)

**WadahNgopi** adalah platform direktori cafe modern yang dirancang khusus untuk mengeksplorasi spot-spot kopi terbaik di Kalimantan. Dibangun dengan estetika *Luxury Glassmorphism*, aplikasi ini memberikan pengalaman pengguna yang premium, responsif, dan fungsional.

---

## ✨ Fitur Unggulan

- 🔍 **Smart Explorer** - Temukan cafe berdasarkan nama, kategori, atau fasilitas (WiFi, Parking, Indoor/Outdoor).
- 📍 **Geolocation Ready** - Cari cafe terdekat dari posisimu dengan kalkulasi jarak real-time.
- 📱 **PWA Support** - Install aplikasi langsung ke Smartphone kamu dari browser. Offline-ready!
- 🗂️ **Digital Menu v4** - Lihat katalog produk cafe dengan visual yang bersih dan interaktif.
- 🛠️ **Powerful Admin Panel** - Manajemen data cafe, fasilitas, dan berita yang mudah dengan Filament v3.
- 💾 **Safe Bookmark** - Simpan cafe idamanmu tanpa ribet login (Local Storage based).

---

## 🔧 Tech Stack & Design

| Komponen | Teknologi | Detail |
| :--- | :--- | :--- |
| **Backend** | Laravel 12 | Core Framework with PHP 8.4 |
| **Admin** | Filament v3 | High-performance dashboard |
| **Styling** | Tailwind CSS v4 | Pre-alpha performance with JIT |
| **Frontend** | Alpine.js + Livewire 3 | Reactive components & Light-weight |
| **Database** | MySQL / PostgreSQL | Robust data layers |
| **Aesthetics** | Luxury Minimalist | Dark-mode ready & Glass-theme |

---

## 🛠️ Instalasi (Local Development)

### 📋 Prasyarat (Prerequisites)

Pastikan environment kamu sudah siap:

| Software | Versi Minimum | Link Download |
| :--- | :--- | :--- |
| PHP | >= 8.3 | [php.net](https://php.net) |
| Composer | Latest | [getcomposer.org](https://getcomposer.org) |
| Node.js | >= 20.x | [nodejs.org](https://nodejs.org) |
| MySQL | >= 8.0 | [mysql.com](https://dev.mysql.com/downloads/) |
| Git | Latest | [git-scm.com](https://git-scm.com) |

> 💡 **Tip:** Di Windows, gunakan [Laravel Herd](https://herd.laravel.com) untuk setup PHP + MySQL otomatis!

---

### 🚀 Langkah Instalasi

#### 1. Clone Repository
```bash
git clone https://github.com/2343087/wadahngopi.git
cd wadahngopi
```

#### 2. Install Dependencies
```bash
# PHP dependencies
composer install

# JavaScript dependencies
npm install
```

#### 3. Setup Environment
```bash
# Copy file environment
cp .env.example .env

# Generate application key
php artisan key:generate
```

#### 4. Konfigurasi File `.env`

Buka file `.env` dan sesuaikan setting berikut:

```dotenv
# === DATABASE ===
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=wadahngopi       # <-- Buat database dengan nama ini
DB_USERNAME=root             # <-- Username MySQL kamu
DB_PASSWORD=                 # <-- Password MySQL kamu (kosong jika default)

# === TIMEZONE (PENTING!) ===
APP_TIMEZONE=Asia/Makassar   # <-- Wajib pakai WITA!

# === APP URL ===
APP_URL=http://localhost:8000  # <-- Sesuaikan dengan URL local kamu
```

> ⚠️ **PENTING:** Timezone harus `Asia/Makassar` agar status "Buka/Tutup" cafe akurat!

#### 5. Jalankan Migrasi & Seeding
```bash
# Buat tabel database + data awal
php artisan migrate --seed
```

#### 6. Link Storage & Compile Assets
```bash
# Hubungkan folder storage ke public
php artisan storage:link

# Compile CSS & JS (pilih salah satu)
npm run dev      # Mode development (watch mode)
# ATAU
npm run build    # Mode production (optimized)
```

#### 7. Jalankan Server Lokal
```bash
php artisan serve
```

Buka browser: **http://localhost:8000** 🎉

---

## 🔐 Akses Admin Panel

Setelah menjalankan `migrate --seed`, akun admin default adalah:

| Field | Value |
| :--- | :--- |
| URL | `/admin` |
| Email | `developer@wadahngopi.test` |
| Password | `password` |

> ⚠️ **SEGERA GANTI PASSWORD** setelah login pertama di production!

---

## 🌐 Deployment ke Production (Hosting)

### ⚠️ Checklist Sebelum Deploy

Pastikan hal-hal berikut sudah dilakukan:

| No | Item | Status |
| :--- | :--- | :--- |
| 1 | Set `APP_ENV=production` | ⬜ |
| 2 | Set `APP_DEBUG=false` | ⬜ |
| 3 | Ganti `APP_URL` ke domain production | ⬜ |
| 4 | Ganti password admin default | ⬜ |
| 5 | Setup HTTPS (SSL Certificate) | ⬜ |
| 6 | Jalankan `php artisan config:cache` | ⬜ |
| 7 | Jalankan `php artisan route:cache` | ⬜ |
| 8 | Jalankan `php artisan view:cache` | ⬜ |
| 9 | Jalankan `npm run build` (bukan dev!) | ⬜ |

### 📝 Konfigurasi `.env` untuk Production

```dotenv
APP_NAME="WadahNgopi"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database Production
DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_PORT=3306
DB_DATABASE=your_db_name
DB_USERNAME=your_db_user
DB_PASSWORD=your_secure_password

# Timezone
APP_TIMEZONE=Asia/Makassar

# Session & Cache (Opsional: gunakan Redis di production)
SESSION_DRIVER=file
CACHE_STORE=file
```

### 🔒 Keamanan Production

1. **HTTPS Wajib!** - Fitur Geolocation browser HANYA berfungsi di HTTPS.
2. **Sembunyikan `.env`** - Pastikan file `.env` tidak bisa diakses publik.
3. **Disable Debug Mode** - `APP_DEBUG=false` mencegah error detail bocor ke user.
4. **Rate Limiting** - Laravel sudah include throttle middleware, pastikan aktif.

### � Deploy Commands

Setelah upload file ke server, jalankan:

```bash
# Install dependencies (tanpa dev packages)
composer install --no-dev --optimize-autoloader

# Generate key (jika belum)
php artisan key:generate

# Migrate database
php artisan migrate --force

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Build frontend assets
npm install
npm run build

# Link storage
php artisan storage:link
```

---

## �📂 Struktur Folder Penting

```
.
├── app/
│   ├── Filament/          # Konfigurasi Admin Panel (Resources, Widgets, Pages)
│   ├── Http/Controllers/  # Logic Public Facing (Cafe, News, Saved)
│   ├── Livewire/          # Livewire Components (ExploreSearch, SavedCafes)
│   ├── Models/            # Eloquent Models (Cafe, Review, Information, City)
│   └── Traits/            # Utility Helper (OptimizesImages)
├── config/
│   └── app.php            # Application Config (Timezone, Locale)
├── public/
│   └── wadahicon.png      # Branding Logo Utama
├── resources/
│   ├── css/               # Tailwind v4 Configuration & Custom CSS
│   └── views/             # Blade Templates (Explore, Home, Show)
└── routes/
    └── web.php            # Main Routes
```

---

## ❓ Troubleshooting

### 1. Error: "CSRF Token Mismatch" / "Page Expired 419"
**Solusi:** Refresh halaman. Jika masih error, clear cache:
```bash
php artisan cache:clear
php artisan config:clear
```

### 2. Geolocation Tidak Berfungsi
**Penyebab:** Browser memblokir akses lokasi di HTTP.
**Solusi:** Gunakan HTTPS. Di local development dengan Herd, jalankan `herd secure`.

### 3. Gambar/Assets Tidak Muncul
**Solusi:** Jalankan storage link:
```bash
php artisan storage:link
```

### 4. Cafe Status "Buka/Tutup" Salah
**Penyebab:** Timezone tidak sesuai.
**Solusi:** Pastikan `.env` memiliki `APP_TIMEZONE=Asia/Makassar`.

---

## 📤 Git Push (Untuk Developer)

Setelah melakukan perubahan, push ke GitHub dengan:

```bash
# Cek status perubahan
git status

# Stage semua file
git add .

# Commit dengan pesan deskriptif
git commit -m "feat: deskripsi singkat perubahan"

# Push ke branch main
git push origin main
```

---

## 🤝 Kontribusi & Lisensi

Kami sangat terbuka untuk kontribusi! Silahkan buat **Pull Request** atau **Issue** jika menemukan bug atau ingin menambah fitur baru.

**Lisensi**: MIT License - silakan gunakan untuk keperluan personal maupun edukasi.

---

**☕ Selamat Ngopi & Happy Coding!**  
*Dibuat oleh (Tim AK Kreatif)*
