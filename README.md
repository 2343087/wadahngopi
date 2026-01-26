# ☕️ WadahNgopi.Com
> Platform Penemuan Cafe Terbaik Buan Kita Semua!

WadahNgopi adalah PWA (Progressive Web App) yang ngebantu lu nyari tempat nongkrong paling hits, lengkap ama info jam buka, fasilitas, sampe koordinat Maps-nya. Gak perlu bingung lagi mau "healing" di mana hari ini.

---

## 🛠️ Persiapan Perang (Prerequisites)
Sebelum narik kodingan ini, pastiin spek "PC" lu udah mumpuni:
*   **PHP 8.2+** (Wajib, bro!)
*   **Composer** (Buat bumbu kodingan)
*   **Node.js & NPM** (Buat ngeracik Tailwind)
*   **MySQL/SQLite** (Buat simpan kenangan... eh, data cafe)
*   **Laravel Herd** (Sangat direkomendasi biar sat-set!)

---

## ⚡️ Cara Setup Sat-Set (Quick Start)
Gak perlu ribet ngetik manual satu-satu, pakenya jurus sakti ini:

1.  **Clone Repo:**
    ```bash
    git clone https://github.com/yourusername/wadahngopi.git
    cd wadahngopi
    ```

2.  **Gaspol Setup:**
    ```bash
    composer run setup
    ```
    *Command ini bakal otomatis instal vendor, set .env, generate key, migrate DB, ampe jalanin npm build. Tunggu bentar ampe kelar, terus seduh kopi lu.*

3.  **Akses Web:**
    Buka `http://wadahngopi.test` di browser kesayangan lu.

---

## 👨‍💻 Cara Berkontribusi
Kalo mau nambahin fitur atau benerin bug:
1.  Fork repo ini.
2.  Bikin branch baru: `git checkout -b fitur-kece-gua`.
3.  Jalanin `npm run dev` buat mantau tampilan *real-time*.
4.  Kalo udah kelar, jangan lupa `vendor/bin/pint` biar kodenya nggak berantakan.
5.  Submit Pull Request (PR).

---

## 🛡️ Security Audit Note
Projek ini udah di-scan ama **Elite Full Stack Bug Bounty Hunter**. Semua hole IDOR dan spoofing udah ditutup rapet. Jangan coba-coba nge-bot "Energy" ya, server kita sekarang udah pinter! 😉

---

**☕ Selamat Ngopi & Happy Coding!**
