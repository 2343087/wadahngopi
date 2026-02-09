# ☕️ WadahNgopi (Next-Gen)

> **Platform Direktori Kopi Paling Canggih di Kalimantan**
> *Cafe & Roastery Explorer • Hyper-Localized • Luxury Experience*

[![Laravel 12](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel)](https://laravel.com)
[![Livewire 3](https://img.shields.io/badge/Livewire-3.x-FB70A9?style=for-the-badge&logo=livewire)](https://livewire.laravel.com)
[![Tailwind v4](https://img.shields.io/badge/Tailwind_Soon-v4.0-06B6D4?style=for-the-badge&logo=tailwind-css)](https://tailwindcss.com)
[![Filament v3](https://img.shields.io/badge/Filament-v3-EEBB0B?style=for-the-badge&logo=filament)](https://filamentphp.com)

**WadahNgopi** bukan sekadar direktori. Ini adalah ekosistem digital yang menghubungkan pecinta kopi dengan *coffee shop* dan *roastery* terbaik. Dibangun dengan standar engineering "God Tier", aplikasi ini fokus pada performa, estetika *glassmorphism*, dan akurasi data.

---

## 🚀 Fitur Utama (Core Features)

### 🏪 Cafe Explorer
- **Smart Search:** Filter by *Fasilitas*, *Kategori*, *Kota*, dan *Nama*.
- **Live Geolocation:** Urutkan cafe dari yang **Terdekat** (Real-time Haversine Formula).
- **Opening Status:** Status "Buka/Tutup" yang akurat, support jam operasional kompleks (Weekend vs Weekday).

### 🏭 Roastery Hub (New!)
- **Dedicated Space:** Modul khusus untuk mencari penyangrai kopi (Roastery).
- **Beans Showcase:** Lihat biji kopi andalan setiap roastery.
- **Advanced Filter:** Filter Roastery yang "Sedang Buka" sekarang juga.

### 🎨 User Experience
- **Luxury UI:** Desain modern dengan sentuhan Glassmorphism dan Micro-interactions.
- **PWA Ready:** Install sebagai aplikasi native di Android/iOS.
- **Bookmark System:** Simpan cafe/roastery favorit ke *Saved List* (Local Storage, Privacy Friendly).

### 🛠️ Untuk Pemilik Bisnis
- **Filament Admin Panel:** Dashboard super intuitif untuk kelola data cafe, menu, dan jam buka.
- **Digital Menu:** Upload foto menu resolusi tinggi dengan *Lazy Loading* otomatis.

---

## ⚡ Tech Stack (The Engine)

Kami menggunakan teknologi terkini untuk menjamin kecepatan dan stabilitas:

| Layer | Teknologi | Highlights |
| :--- | :--- | :--- |
| **Framework** | Laravel 12 | PHP 8.4 Strict Types |
| **Frontend** | Livewire 3 + Alpine.js | SPA-like experience tanpa kompleksitas API |
| **Styling** | Tailwind CSS v4 | Next-gen CSS engine (JIT) |
| **Admin** | Filament v3 | TALL Stack Admin Panel |
| **Database** | MySQL 8 | Optimized Indexing & Spatial Data |
| **Performance** | Redis / File Cache | Aggressive Caching Strategy |

---

## 🛠️ Instalasi (Local Development)

### Prasyarat
- PHP 8.3+
- Composer
- Node.js 20+
- MySQL 8.0+

### Setup Project
```bash
# 1. Clone Repo
git clone https://github.com/2343087/wadahngopi.git
cd wadahngopi

# 2. Install Dependencies
composer install
npm install

# 3. Environment Setup
cp .env.example .env
php artisan key:generate

# 4. Database Setup (Pastikan DB 'wadahngopi' sudah dibuat)
php artisan migrate --seed

# 5. Build Assets
npm run dev

# 6. Run Server
php artisan serve
```

### � Penting: Data Sync (Roastery)
Jika kamu melakukan import database manual atau seeding ulang, jalankan command ini untuk sinkronisasi jam operasional roastery:
```bash
php artisan app:backfill-roastery-hours
```

---

## �️ Security & Performance Standards

Proyek ini mematuhi **Antigravity Protocol V2.0**:
1.  **Security First:** Proteksi berlapis terhadap XSS, SQLi, dan IDOR.
2.  **N+1 Prevention:** Semua query di-optimize dengan Eager Loading (`with()`).
3.  **Data Integrity:** Auto-sync antara JSON data dan Column database via Model Events.
4.  **Asset Optimization:** Gambar menggunakan `loading="lazy"` dan format WebP/Optimized.

---

## 🤝 Kontribusi

Ingin berkontribusi? Pastikan kode kamu mematuhi `SKILL.md` protocol kami.
1.  Fork repository.
2.  Buat branch fitur (`git checkout -b fitur-keren`).
3.  Commit perubahan (`git commit -m 'feat: nambah fitur keren'`).
4.  Push (`git push origin fitur-keren`).
5.  Buka Pull Request.

---

**© 2026 WadahNgopi Team**. *Diseduh dengan ❤️ dan baris kode.*
