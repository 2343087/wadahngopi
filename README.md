<p align="center">
  <img src="public/wadahicon.png" width="80" alt="WadahNgopi Logo">
</p>

<h1 align="center">WadahNgopi</h1>

<p align="center">
  <strong>Platform Pencarian Cafe & Roastery Terlengkap di Kalimantan</strong><br>
  <em>Temukan tempat ngopi terbaik — kapan aja, di mana aja.</em>
</p>

<p align="center">
  <a href="https://laravel.com"><img src="https://img.shields.io/badge/Laravel-12-FF2D20?style=flat-square&logo=laravel&logoColor=white" alt="Laravel 12"></a>
  <a href="https://livewire.laravel.com"><img src="https://img.shields.io/badge/Livewire-3-FB70A9?style=flat-square&logo=livewire&logoColor=white" alt="Livewire 3"></a>
  <a href="https://tailwindcss.com"><img src="https://img.shields.io/badge/Tailwind-v4-06B6D4?style=flat-square&logo=tailwind-css&logoColor=white" alt="Tailwind v4"></a>
  <a href="https://filamentphp.com"><img src="https://img.shields.io/badge/Filament-v3-EEBB0B?style=flat-square&logo=data:image/svg+xml;base64,&logoColor=white" alt="Filament v3"></a>
</p>

---

## ✨ Apa Itu WadahNgopi?

**WadahNgopi** adalah platform yang bantu kamu nemuin cafe dan roastery paling cocok buat kamu. Gak perlu ribet scroll review satu-satu — cukup buka, cari, dan langsung gas ke tempat yang lo mau.

Semua data real-time. Buka tutupnya akurat. Lokasinya bisa diurutin dari yang paling deket. Simpel, cepat, dan tampilannya gak murahan.

---

## � Fitur Unggulan

### 🏪 Jelajahi Cafe
- **Pencarian Cerdas** — Filter berdasarkan fasilitas, kategori, kota, atau langsung ketik nama
- **Urutkan dari Terdekat** — Aktifin lokasi, langsung muncul cafe paling deket dari lo
- **Status Buka / Tutup** — Real-time, akurat, termasuk jam weekend vs weekday

### 🏭 Roastery Hub
- **Halaman Khusus Roastery** — Nyari penyangrai kopi? Ada tempatnya sendiri
- **Info Biji Kopi** — Liat biji kopi andalan tiap roastery
- **Filter Jam Buka** — Cuma tampilin roastery yang lagi buka sekarang

### 🎯 Bingung? Putar Aja!
- **Cafe Roulette** — Gak tau mau ke mana? Klik tombol "Bingung?", spin, dan dapetin rekomendasi random
- **Animasi Seru** — Setiap spin hasilnya beda dan gak bisa ketebak
- **Langsung Gas** — Dapet hasilnya, klik, dan langsung liat detail cafe-nya

### 💾 Simpan Favorit
- **Bookmark** — Simpan cafe atau roastery favorit kamu tanpa perlu login
- **Privasi Aman** — Data tersimpan di perangkat kamu sendiri, bukan di server

### 📱 Pengalaman Premium
- **Desain Modern** — Tampilan glassmorphism yang clean dan elegan
- **Responsif Total** — Nyaman di HP, tablet, maupun desktop
- **PWA Ready** — Bisa dipasang kayak aplikasi native di Android & iOS
- **Splash Screen** — Animasi branding keren pas pertama buka web
- **Pull-to-Refresh** — Tarik ke bawah buat refresh, lengkap sama animasi kopi

### 🛠 Buat Pemilik Bisnis
- **Dashboard Admin** — Kelola data cafe, menu, jam buka, dan fasilitas dengan mudah
- **Upload Menu** — Foto menu resolusi tinggi, otomatis di-optimize

---

## 🧰 Teknologi di Balik Layar

| Komponen | Teknologi |
| :--- | :--- |
| Framework | Laravel 12 (PHP 8.4) |
| Frontend | Livewire 3 + Alpine.js |
| Styling | Tailwind CSS v4 |
| Admin Panel | Filament v3 |
| Database | MySQL 8 |
| Cache | Redis / File Cache |

---

## � Cara Jalanin di Lokal

> Pastikan lo udah punya **PHP 8.3+**, **Composer**, **Node.js 20+**, dan **MySQL 8.0+**.

```bash
# Clone repo
git clone https://github.com/2343087/wadahngopi.git
cd wadahngopi

# Install semua dependensi
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Bikin database 'wadahngopi', lalu jalanin migrasi
php artisan migrate --seed

# Build & jalanin
npm run dev
php artisan serve
```

### ⚠️ Catatan Penting
Kalau lo import database manual atau seeding ulang, jalanin ini buat sinkronisasi data roastery:
```bash
php artisan app:backfill-roastery-hours
```

---

## 🔒 Standar Keamanan

- Semua input divalidasi ketat sebelum masuk database
- Proteksi dari serangan umum (XSS, SQL Injection, IDOR)
- Data pengguna tidak disimpan di server tanpa consent
- Semua query database di-optimize biar gak lemot
- Gambar di-load secara lazy biar hemat bandwidth

---

## 🤝 Mau Kontribusi?

1. Fork repo ini
2. Bikin branch fitur (`git checkout -b fitur-keren`)
3. Commit perubahan (`git commit -m 'feat: nambah fitur keren'`)
4. Push (`git push origin fitur-keren`)
5. Buka Pull Request

---

<p align="center">
  <strong>© 2026 WadahNgopi</strong><br>
  <em>Diseduh dengan ❤️ dan baris kode dari Kalimantan.</em>
</p>
