# MYTopup (CodeIgniter 3 + Tailwind) – prototipe gaya Minimalis Modern

Isi repo ini adalah **lapisan aplikasi** (`application/`, `assets/`). Folder `system/`, `index.php`, dan `.htaccess` berasal dari CodeIgniter 3.

## Pasang
1. Buat database & isi data:
   ```
   mysql -u root -p -e "CREATE DATABASE mytopup CHARACTER SET utf8mb4"
   mysql -u root -p mytopup < database/schema.sql
   mysql -u root -p mytopup < database/seed.sql      # katalog contoh (ganti dengan data asli)
   ```
   Database lama (dari versi sebelumnya)? Jalankan `database/migrations/002_admin_and_images.sql`.
2. `cp .env.example .env`, lalu isi `DB_*` dan kredensial Duitku (`DUITKU_MERCHANT_CODE`, `DUITKU_API_KEY`). `.env` sudah di-`.gitignore`.
3. Jalankan: Apache/Nginx, atau `php -S localhost:8000` dari root (URL berbentuk `/index.php/...`).

## Admin dashboard
- URL: `/admin` (di `index.php/admin` bila tanpa rewrite). Buat admin pertama lewat terminal (password diketik interaktif, tidak tampil):
  ```
  php index.php cli create_admin namaanda
  ```
  Admin berikutnya bisa dibuat/dihapus dan password diganti di menu **Akun Admin**.
- **Dashboard**: pesanan & pendapatan hari ini / 30 hari, pesanan menunggu bayar, pesanan perlu dikirim.
- **Pesanan**: filter status, cari no. pesanan/email/ID akun, detail + log pembayaran, tombol *Tandai berhasil / gagal* setelah item dikirim.
- **Game & Produk**: tambah/edit/nonaktifkan/hapus game, **unggah gambar** (JPG/PNG/WebP, maks 2 MB; di-encode ulang & dikecilkan ke 640px), kelola nominal & harga & SKU supplier.
- Keamanan: password di-hash (bcrypt), CSRF di semua form, sesi httponly + regenerasi saat login, kunci login 5x gagal/15 menit per IP, upload divalidasi & di-encode ulang, folder upload tanpa eksekusi skrip (`assets/uploads/.htaccess`; di Nginx tolak eksekusi PHP di `/assets/uploads/`).
- Sebaiknya pasang HTTPS dan batasi `/admin` (IP/VPN/basic auth) di server produksi.

## Alur pembayaran (Duitku)
1. Pembeli isi ID, pilih nominal & metode di `/game/<slug>` -> `POST /order/create`.
2. Server memvalidasi, mengambil **harga dari database** (bukan dari form), memanggil Duitku `v2/inquiry`, menyimpan pesanan (`pending`), lalu redirect ke `/order/<no>`.
3. Halaman pesanan menampilkan tombol bayar (`paymentUrl`) / nomor VA, timer, dan polling status tiap 5 detik.
4. Duitku memanggil `POST /payment/callback` (signature `md5(merchantCode+amount+merchantOrderId+apiKey)` diverifikasi, nominal dicocokkan, idempoten). Bila callback tak sampai, halaman pesanan merekonsiliasi lewat `transactionStatus`.
5. `paid` -> `Fulfillment` (driver `manual`: status `processing`; operator menyelesaikan lewat CLI).

**Callback harus bisa diakses publik.** Saat development gunakan tunnel (ngrok/cloudflared) dan set `DUITKU_PUBLIC_BASE=https://xxxx.ngrok.app`.

## Operator (CLI)
```
php index.php cli orders [status]
php index.php cli complete <order_no>   # item sudah dikirim -> success
php index.php cli fail <order_no>
php index.php cli expire                # jalankan via cron tiap menit
```
Pengiriman item otomatis butuh supplier (Digiflazz, VIP Reseller, dst.): tambahkan `driver_<nama>()` di `libraries/Fulfillment.php` dan set `FULFILLMENT_DRIVER`.

## Dua view
- `application/views/desktop/*` dan `application/views/mobile/*`, dipilih oleh `view_mode()` (`helpers/device_helper.php`).
- Paksa tampilan: `?view=mobile` atau `?view=desktop` (disimpan 30 hari di cookie).

## Struktur
- `core/MY_Controller.php` – `render($page, $data)` merakit head + view + footer.
- `models/Game_model.php` (katalog dari DB), `models/Order_model.php` (pesanan, transisi status atomik).
- `libraries/Duitku.php` (klien API), `libraries/Fulfillment.php`, `controllers/Order.php`, `controllers/Payment.php` (webhook), `controllers/Cli.php`.
- `controllers/Home.php`, `Game.php` – landing & detail game (`/game/mobile-legends`).
- `assets/js/app.js` – scroll reveal, counter, countdown, logika pilih nominal.
- `controllers/Catalog.php` – `/games` (semua game + filter kategori), `/search?q=` (game + produk), `/search/suggest` (JSON saran saat mengetik).
- Tailwind lewat CDN (prototipe). Untuk produksi, build CSS dengan Tailwind CLI.
