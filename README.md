# ☕️ WadahNgopi: Hyper-Local Coffee Portal
> Portal Informasi Cafe & Berita Kopi Paling "Kekinian" di Indonesia.

**WadahNgopi** adalah platform aggregator dan direktori cafe modern yang dibangun dengan teknologi terbaru. Bukan cuma buat nyari tempat nongkrong, tapi juga buat dapet info fasilitas detail (Live Musik, WiFi, Outlets), Menu Digital yang estetik, sampe berita kopi terbaru yang ditarik otomatis dari berbagai portal berita besar.

---

### 🚀 Tech Stack (Gak Kaleng-Kaleng)
Proyek ini pake "senjata" paling mumpuni di dunia web dev saat ini:
*   **Core**: Laravel 12 (Modern & Super Aman)
*   **Runtime**: PHP 8.4.15 (Speed paling ngebut)
*   **Admin Dashboard**: Filament v3 (Rapi & Mantap)
*   **Mobile Ready**: PWA (Progressive Web App) dengan Offline Support.
*   **Frontend UI**: Tailwind CSS v4 + Plus Jakarta Sans Font
*   **Interactivity**: Alpine.js v3 (Lincah & Ringan)
*   **Intelligence**: Manual Scraper (News Aggregator) via Symfony Dom-Crawler

---

### 🛠️ Persiapan Perang (Prerequisites)
Pastiin OS lu udah "sehat" dan udah ke-install barang-barang ini:
1.  **PHP 8.4** (Paling wajib, di bawah ini bakal error)
2.  **Composer** (Manajer paket PHP)
3.  **Node.js & NPM** (Buat ngeracik UI)
4.  **MySQL** atau **SQLite** (Buat database)
5.  **Laravel Herd** (Sangat disaranin buat user Mac/Windows biar setup local domain `.test` gampang banget)

---

### ⚡️ Cara Install (Langkah demi Langkah)

Ikutin langkah ini satu-satu, jangan ada yang kelewat biar gak ada fitur yang "patah":

#### 1. Clone & Masuk Folder
```bash
git clone https://github.com/2343087/wadahngopi.git
cd wadahngopi
```

#### 2. Install Bumbu-Bumbu (Backend & Frontend)
```bash
composer install
npm install
```

#### 3. Setup Environment
Copy file dummy `.env` dan bikin key baru:
```bash
cp .env.example .env
php artisan key:generate
```
*(Jangan lupa atur koneksi DB lu di file `.env` kalo bukan pake SQLite default)*

#### 4. Migrasi & Suntik Data (Seeder)
Biar web gak kosong melompong, kita migrasi database dan suntik data cafe & fasilitas default:
```bash
php artisan migrate --seed
```
> **LOGIN ADMIN**: Setelah seeding, lu bisa login ke dashboard di `/admin` pake:
> - **Email**: `admin@wadahngopi.test`
> - **Password**: `password`

#### 5. Linking Storage
Biar gambar menu dan cafe tampil, kita perlu "nyambungin" folder storage:
```bash
php artisan storage:link
```

#### 6. Kompilasi Asset UI
Proyek ini pake Tailwind v4, jadi wajib di-build biar tampilannya "Premium":
```bash
npm run build
```

---

### 🛰️ Fitur Spesial: News Scraper
Web ini punya fitur auto-crawl berita kopi. Lu bisa jalanin manual buat ngetes:
```bash
php artisan app:scrape-coffee-news
```
*Script ini bakal keliling ke portal berita (Liputan6, Detik, dll) buat narik berita terbaru soal kopi.*

---

### 👨‍💻 Development Mode
Kalo lu mau ngutek-ngutek kodenya, jalanin ini di dua terminal terpisah:
*   **Terminal 1**: `php artisan serve` (Jalanin server)
*   **Terminal 2**: `npm run dev` (Pantau perubahan UI secara real-time)

---

### 🛡️ Bug Bounty & Best Practices
Proyek ini udah di-review pake standar keamanan kelas dunia:
*   **Linting**: Pake `vendor/bin/pint` biar kode seragam rapi.
*   **Security**: Udah aman dari XSS, SQL Injection, dan IDOR (thanks to Laravel's core).
*   **Maintainability**: Pake pola MVC Laravel yang solid.

---

**☕ Selamat Ngopi & Happy Coding!**  
*Dibuat dengan cinta dan kafein tinggi.*
