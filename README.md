# ☕️ WadahNgopi
> Portal Cafe Terbaik di Kalimantan

**WadahNgopi** adalah platform direktori cafe modern yang membantu kamu menemukan spot ngopi nyaman di Kalimantan. Dilengkapi dengan info fasilitas lengkap, menu digital, dan berita kopi terbaru dari berbagai portal berita.

---

## 🚀 Tech Stack

| Komponen | Teknologi |
|----------|-----------|
| Backend | Laravel 12 + PHP 8.4 |
| Admin Panel | Filament v3 |
| Frontend | Tailwind CSS v4 + Alpine.js |
| Font | Plus Jakarta Sans |
| PWA | Offline Support Ready |
| News Scraper | Symfony Dom-Crawler |

---

## ✨ Fitur Utama

- � **Explore Cafe** - Cari cafe berdasarkan lokasi terdekat, fasilitas, atau nama
- 📍 **Integrasi Google Maps** - Lihat jarak real-time ke setiap cafe
- 🏪 **Detail Lengkap** - Fasilitas, jam buka, menu, dan social media
- 💾 **Simpan Favorit** - Bookmark cafe kesukaan di browser
- 📰 **Berita Kopi** - Aggregator berita otomatis dari portal besar
- 🚀 **Performa Optimal** - Built-in caching untuk load cepat
- 📱 **Mobile First** - Desain responsif dengan bottom navigation

---

## 🛠️ Prerequisites

Pastikan sudah terinstall:
- PHP 8.4+
- Composer
- Node.js & NPM
- MySQL/SQLite
- [Laravel Herd](https://herd.laravel.com) (Recommended)

---

## ⚡️ Instalasi

### 1. Clone Repository
```bash
git clone https://github.com/2343087/wadahngopi.git
cd wadahngopi
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Setup Environment
```bash
cp .env.example .env
php artisan key:generate
```
*Sesuaikan konfigurasi database di file `.env`*

### 4. Migrasi Database
```bash
php artisan migrate --seed
```

**Login Admin**:
- URL: `/admin`
- Email: `admin@wadahngopi.test`
- Password: `password`

### 5. Link Storage & Build Assets
```bash
php artisan storage:link
npm run build
```

---

## 🛰️ News Scraper

Jalankan scraper berita kopi secara manual:
```bash
php artisan app:scrape-coffee-news
```

Untuk otomatis, tambahkan ke cron:
```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

---

## 👨‍💻 Development

```bash
# Terminal 1 - Server
php artisan serve

# Terminal 2 - Asset Watcher
npm run dev
```

Atau gunakan script gabungan:
```bash
composer run dev
```

---

## � Struktur Penting

```
app/
├── Filament/          # Admin panel resources
├── Http/Controllers/  # Logic halaman publik
├── Models/            # Eloquent models
├── Traits/            # Reusable traits (OptimizesImages)
└── Providers/         # Service providers

resources/views/
├── home.blade.php     # Landing page
├── explore.blade.php  # Halaman explore cafe
├── saved.blade.php    # Halaman favorit
└── information/       # Halaman berita
```

---

## 🎨 Design System

- **Warna Utama**: Espresso (#2C1810), Amber (#D97706)
- **Font**: Plus Jakarta Sans
- **Icon**: Phosphor Icons
- **Style**: Luxury Glassmorphism

---

**☕ Selamat Ngopi & Happy Coding!**  
*Dibuat dengan cinta oleh AK Kreatif*
