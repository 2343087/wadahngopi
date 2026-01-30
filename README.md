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
- � **News Coffee Aggregator** - Update otomatis berita kopi harian dari berbagai portal berita besar (Symfony Scraper).
- 🛠️ **Powerful Admin Panel** - Manajemen data cafe, fasilitas, dan berita yang mudah dengan Filament v3.
- 💾 **Safe Bookmark** - Simpan cafe idamanmu tanpa ribet login (Local Storage based).

---

## � Tech Stack & Design

| Komponen | Teknologi | Detail |
| :--- | :--- | :--- |
| **Backend** | Laravel 12 | Core Framework with PHP 8.4 |
| **Admin** | Filament v3 | High-performance dashboard |
| **Styling** | Tailwind CSS v4 | Pre-alpha performance with JIT |
| **Frontend** | Alpine.js | Reactive components & Light-weight |
| **Database** | MySQL / PostgreSQL | Robust data layers |
| **Aesthetics** | Luxury Minimalist | Dark-mode ready & Glass-theme |

---

## 🛠️ Instalasi (Local Development)

### 1. Persiapan Awal
Pastikan environment kamu sudah siap:
- PHP >= 8.4
- MySQL 8.x
- Composer & Node.js (Latest stable)

### 2. Setup Projek
```bash
# Clone repository
git clone https://github.com/2343087/wadahngopi.git
cd wadahngopi

# Install dependencies
composer install
npm install

# Setup environtment
cp .env.example .env
php artisan key:generate

# Migrasi & Seeding (PENTING)
php artisan migrate --seed
```

### 3. Konfigurasi Aset
```bash
# Link storage folder
php artisan storage:link

# Compile assets
npm run dev # development
# atau
npm run build # production
```

---

## 🛰️ Coffee News Worker

Projek ini dilengkapi dengan berita otomatis. Kamu bisa menjalankannya secara manual atau terjadwal:

```bash
# Jalankan scraper sekali
php artisan app:scrape-coffee-news

# Jalankan via Scheduler (Task Scheduling)
php artisan schedule:work
```

---

## 📂 Struktur Penting

```
.
├── app/
│   ├── Filament/          # Konfigurasi Admin Panel (Resources, Widgets, Pages)
│   ├── Http/Controllers/  # Logic Public Facing (Cafe, News, Saved)
│   ├── Models/            # Eloquent Models (Cafe, Review, Information)
│   └── Traits/            # Utility Helper (OptimizesImages)
├── public/
│   └── wadahicon.png      # Branding Logo Utama
├── resources/
│   ├── css/               # Tailwind v4 Configuration & Custom CSS
│   └── views/             # Blade Templates (Explore, Home, Show)
└── routes/
    └── web.php            # Main Routes
```

---

## 🔐 Admin Access
Setelah seeding selesai, akses panel admin di:
- **URL**: `your-domain.test/admin`
- **User**: `admin@wadahngopi.test`
- **Pass**: `password`

---

## 🤝 Kontribusi & Lisensi

Kami sangat terbuka untuk kontribusi! Silahkan buat **Pull Request** atau **Issue** jika menemukan bug atau ingin menambah fitur baru.

**Lisensi**: MIT License - silakan gunakan untuk keperluan personal maupun edukasi.

---

**☕ Selamat Ngopi & Happy Coding!**  
*Dibuat oleh (Tim AK Kreatif)*
