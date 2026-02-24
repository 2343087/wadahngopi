<p align="center">
  <img src="public/wadahngopi.png" width="80" alt="WadahNgopi Logo">
</p>

<h1 align="center">WadahNgopi</h1>

<p align="center">
  <strong>Platform Pencarian Cafe & Roastery Terlengkap di Kalimantan</strong><br>
  <em>Portal Premium untuk Penikmat Kopi Sejati.</em>
</p>

<p align="center">
  <a href="https://laravel.com"><img src="https://img.shields.io/badge/Laravel-12-FF2D20?style=flat-square&logo=laravel&logoColor=white" alt="Laravel 12"></a>
  <a href="https://livewire.laravel.com"><img src="https://img.shields.io/badge/Livewire-3-FB70A9?style=flat-square&logo=livewire&logoColor=white" alt="Livewire 3"></a>
  <a href="https://tailwindcss.com"><img src="https://img.shields.io/badge/Tailwind-v4-06B6D4?style=flat-square&logo=tailwind-css&logoColor=white" alt="Tailwind v4"></a>
  <a href="https://filamentphp.com"><img src="https://img.shields.io/badge/Filament-v3-EEBB0B?style=flat-square&logo=filament&logoColor=white" alt="Filament v3"></a>
</p>

---

## ✨ Apa Itu WadahNgopi?

**WadahNgopi** adalah portal "Fresh-Premium" yang dirancang untuk mendekatkan ekosistem kopi lokal Kalimantan Timur ke dunia digital. Bukan cuma sekadar direktori, tapi sebuah pengalaman visual yang imersif buat kamu nemuin tempat ngopi paling cocok.

Gak perlu ribet scroll review satu-satu — cukup buka, jelajahi dengan interface yang mewah, dan langsung gas ke cafe atau roastery pilihanmu.

---

## 🎨 Fresh-Premium Experience

Proyek ini telah melalui tahap **UI Overhaul** untuk mencapai standar estetika tertinggi:
- **Golden Frosted Glass**: Estetika *glassmorphism* yang ringan, mewah, dan modern.
- **Reflection Shine**: Animasi "light passing through" pada title yang memberikan kesan *high-end*.
- **Jewelry-Style Iconography**: Ikon yang ringkas dan elegan, layaknya perhiasan di dalam aplikasi.
- **Immersive Layout**: Tampilan full-screen tanpa batas (`100dvh`) yang mengalir mulus di semua perangkat.

---

## 💎 Fitur Unggulan

### 🏪 Jelajahi Cafe
- **Pencarian Cerdas** — Filter berdasarkan fasilitas, kategori, kota, atau nama.
- **Urutkan dari Terdekat** — Deteksi lokasi otomatis untuk rekomendasi tempat terdekat.
- **Status Real-Time** — Jam operasional akurat, sinkron antara metadata dan tampilan.

### 🏭 Roastery Hub
- **Database Roastery** — Halaman khusus untuk para penyangrai kopi lokal.
- **Profil Biji Kopi** — Informasi lengkap mengenai beans andalan tiap roastery.

### 🎯 Bingung? Putar Aja!
- **Cafe Roulette** — Gak tau mau ke mana?Klik tombol "Bingung?", spin, dan biarkan takdir memilih cafe buatmu.
- **Micro-Animations** — Transisi halus dan feedback visual yang memuaskan.

### 📱 Teknologi Progresif
- **PWA Ready** — Install langsung di Android/iOS layaknya aplikasi native.
- **Responsive Shift** — Beradaptasi cerdas sebagai "Immersive Sheet" di HP dan "Floating Card" di Desktop.
- **Performance First** — Optimasi gambar otomatis dan lazy loading menyeluruh.

---

## 🧰 Teknologi di Balik Layar

| Komponen | Teknologi |
| :--- | :--- |
| **Framework** | Laravel 12 (PHP 8.4) |
| **Frontend** | Livewire 3 + Alpine.js |
| **Styling** | Tailwind CSS v4 |
| **Admin Panel** | Filament v3 |
| **Keamanan** | CSP Hardened + Rate Limiting |
| **Testing** | Pest 4 / PHPUnit 12 |

---

## 🚀 Instalasi Lokal

> Pastikan kamu memiliki **PHP 8.4**, **Composer**, **Node.js 20+**, dan **MySQL 8.0+**.

```bash
# Clone repo
git clone https://github.com/2343087/wadahngopi.git
cd wadahngopi

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate --seed

# Build assets & run
npm run build
php artisan serve
```

### 🧪 Automated Testing
Codebase ini dilindungi oleh **210+ automated tests** untuk memastikan stabilitas dan keamanan di setiap fitur.
```bash
php artisan test --compact
```

---

## 🔒 Standar Engineering (God-Tier)

- **Security Hardening**: Proteksi berlapis terhadap XSS, SQL Injection, dan IDOR.
- **Data Integrity**: Sinkronisasi model event untuk memastikan data selalu valid.
- **Optimized SQL**: Query yang efisien dengan indexing tepat sasaran.
- **Clean Code**: Mengikuti standar PSR-12 dan pola desain Laravel modern.

---

<p align="center">
  <strong>© 2026 WadahNgopi</strong><br>
  <em>Diseduh dengan ❤️ dan baris kode presisi dari Kalimantan.</em>
</p>
