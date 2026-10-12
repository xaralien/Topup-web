# MYTopup (CodeIgniter 3 + Tailwind) – prototipe gaya Minimalis Modern

Isi repo ini adalah **lapisan aplikasi** (`application/`, `assets/`). Folder `system/`, `index.php`, dan `.htaccess` berasal dari CodeIgniter 3.

## Pasang
1. Unduh CodeIgniter 3.1.x, salin `system/`, `index.php` ke root repo ini (jangan timpa `application/` yang sudah ada).
2. `application/config/config.php`: set `base_url`, `index_page = ''` (pakai `.htaccess` rewrite), `subclass_prefix = 'MY_'`.
3. `application/config/autoload.php`: `$autoload['helper'] = ['url'];`
4. Jalankan lewat Apache/Nginx atau `php -S localhost:8000` dari root.

## Dua view
- `application/views/desktop/*` dan `application/views/mobile/*`, dipilih oleh `view_mode()` (`helpers/device_helper.php`).
- Paksa tampilan: `?view=mobile` atau `?view=desktop` (disimpan 30 hari di cookie).

## Struktur
- `core/MY_Controller.php` – `render($page, $data)` merakit head + view + footer.
- `models/Game_model.php` – data dummy (ganti dengan DB/API supplier).
- `controllers/Home.php`, `Game.php` – landing & detail game (`/game/mobile-legends`).
- `assets/js/app.js` – scroll reveal, counter, countdown, logika pilih nominal.
- Tailwind lewat CDN (prototipe). Untuk produksi, build CSS dengan Tailwind CLI.
