<p align="center">
  <img src="public/wadahngopi.png" width="96" alt="Logo WadahNgopi">
</p>

<h1 align="center">WadahNgopi</h1>

<p align="center">
  <strong>Panduan santai untuk menemukan kedai kopi dan roastery lokal di Samarinda.</strong><br>
  Untuk hari ketika kamu butuh meja yang tenang buat bekerja, mencari biji kopi segar, atau sekadar berhenti berdebat mau nongkrong di mana.
</p>

---

## Cerita di Balik WadahNgopi

Hampir semua orang di Samarinda pernah mengalami momen yang sama. Sore sudah turun, tugas masih setengah jalan, atau teman-teman di grup WhatsApp akhirnya sepakat buat ketemu—lalu muncul pertanyaan klasik:

*"Enaknya ngopi di mana, ya?"*

Setelah itu biasanya kita membuka banyak tab sekaligus. Cari nama tempat di peta, geser foto satu per satu untuk menebak ada colokan atau tidak, buka media sosialnya untuk memastikan jam tutupnya, lalu tetap saja ragu apakah tempatnya sedang buka atau sudah penuh.

**WadahNgopi** hadir untuk memotong semua keraguan itu. Satu tempat yang rapi, netral, dan jujur untuk mengenal kedai kopi serta roastery lokal di Samarinda. Tidak ada kedai yang diistimewakan di halaman depan—sorotan kedai berganti secara acak supaya setiap sudut ngopi lokal punya kesempatan yang sama untuk ditemukan.

---

## Apa yang Bisa Kamu Lakukan di Sini

### 1. Menjelajah Tanpa Ribet
Buka halaman **Jelajahi** atau **Roastery** dan temukan tempat sesuai kebutuhanmu saat itu juga. Kamu bisa mengetik nama kedai, potongan nama jalan, atau bahkan mengetik cepat tanpa spasi. Kalau sedang terburu-buru, nyalakan saringan **Sedang buka** atau **Terdekat** untuk melihat kedai yang memang masih melayani di sekitarmu. Butuh suasana tertentu? Buka panel filter untuk memilih fasilitas seperti WiFi, AC, ruang terbuka, mushola, atau ketersediaan stopkontak.

### 2. Melihat Detail Tempat Sebelum Berangkat
Setiap halaman kedai merangkum hal-hal yang paling sering bikin penasaran sebelum duduk di sana: galeri foto suasana, foto daftar menu, jam operasional, alamat lengkap beserta tautan Google Maps, kontak WhatsApp, hingga akun media sosialnya.

### 3. Tahu Seberapa Nyaman untuk Bekerja & Seramai Apa Sekarang
- **Penilaian WFC (Work From Cafe):** Pengunjung bisa berbagi penilaian objektif soal kecepatan WiFi, ketersediaan stopkontak, dan kenyamanan tempat duduk.
- **Vibe Meter:** Sedang berada di lokasi? Kamu bisa ikut memberi kabar apakah suasana kedai saat itu sedang sepi, nyaman, atau ramai. Laporan keramaian otomatis menyegarkan diri setiap beberapa jam agar selalu sesuai keadaan terbaru.

### 4. Berhenti Berdebat di Grup Lewat *Tongkrongan*
Susah menentukan tujuan saat mau jalan bareng? Buka fitur **Voting Tongkrongan**, pilih dua sampai lima kandidat kedai, lalu bagikan satu tautan ke grup teman-temanmu. Mereka bisa langsung memilih kedai favorit tanpa perlu repot membuat akun terlebih dahulu. Tautan voting aktif selama 24 jam dan hasilnya langsung terlihat bersama.

### 5. Kalau Benar-Benar Bingung, Serahkan pada *Roulette*
Di halaman beranda ada tombol **Pilihkan satu kedai**. Sekali tekan, WadahNgopi akan mengambilkan satu rekomendasi acak dari kedai yang benar-benar sedang buka saat itu juga.

### 6. Simpan Tempat Favorit & Kumpulkan Lencana
- **Simpan:** Menemukan kedai yang cocok untuk akhir pekan nanti? Tekan ikon simpan. Tanpa masuk akun pun daftar simpananmu tetap tersimpan di peramban, dan akan otomatis menyatu ke akunmu begitu kamu masuk.
- **Check-In & Lencana Kopi:** Tandai kunjunganmu saat berada di lokasi dan kumpulkan lencana kecil sebagai catatan perjalanan menyusuri kedai kopi lokal.

### 7. Kabar Kopi
Ruang baca santai berisi panduan dan catatan seputar dunia kopi—mulai dari mengenal karakter biji Arabica, Robusta, Liberica, hingga Excelsa—tanpa dipenuhi pengumuman lama yang sudah lewat.

### 8. Ruang Kelola untuk Pemilik Kedai & Roastery
Pemilik kedai dan roastery bisa masuk melalui halaman `/admin` untuk memperbarui foto tempat, daftar menu, fasilitas, jam operasional, hingga tautan kontak secara mandiri.

### 9. Ringan dan Bisa Dipasang di Ponsel
WadahNgopi bisa dibuka langsung dari peramban ponsel dan dipasang ke layar utama (*Add to Home Screen*) tanpa perlu mengunduh aplikasi besar dari toko aplikasi. Navigasi bawahnya dirancang ramah jempol agar nyaman digeser dan dipakai dengan satu tangan.

---

## Menjalankan Proyek di Komputer Lokal

Jika kamu ingin menjalankan WadahNgopi di komputermu sendiri untuk pengembangan atau uji coba:

### Kebutuhan Dasar
- PHP 8.2 atau lebih baru (disarankan PHP 8.4)
- Composer
- Node.js 20+ dan NPM
- MySQL 8.0+

### Langkah Singkat

1. **Salin repositori dan masuk ke folder proyek**
   ```bash
   git clone https://github.com/2343087/wadahngopi.git
   cd wadahngopi
   ```

2. **Pasang paket pendukung**
   ```bash
   composer install
   npm install
   ```

3. **Siapkan berkas konfigurasi lingkungan**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Sesuaikan nama database, pengguna, dan kata sandi MySQL di berkas `.env`.

4. **Siapkan database dan tautan penyimpanan gambar**
   ```bash
   php artisan migrate
   php artisan storage:link
   ```

5. **Bangun aset tampilan dan jalankan aplikasi**
   ```bash
   npm run build
   php artisan serve
   ```
   Untuk pengguna Windows dengan Laravel Herd, kamu juga bisa langsung menjalankan `start.bat` dari folder utama proyek.

6. **Membuat akun pengelola (opsional)**
   ```bash
   php artisan cms:create-developer
   ```

---

## Sekilas di Balik Layar

WadahNgopi dibangun menggunakan **Laravel 12** di sisi server, **Vue 3** dan **Vite** untuk antarmuka yang responsif, serta **MySQL 8** untuk pencarian teks dan perhitungan jarak lokasi. Pengujian otomatis berjalan lewat Pest/PHPUnit dan pemeriksaan gaya kode menggunakan Laravel Pint.

---

<p align="center">
  Dibuat dengan perhatian untuk penikmat kopi, pekerja jarak jauh, dan ruang ngopi lokal di Samarinda.
</p>
