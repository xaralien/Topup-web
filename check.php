<?php
/**
 * Diagnostik instalasi MYTopup. Buka: http://localhost/<folder>/check.php
 * Hanya bisa dibuka dari localhost. Tidak menampilkan password/kunci apa pun. HAPUS file ini di server produksi.
 */
if (!in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true)) {
    http_response_code(403);
    exit('Diagnostik hanya untuk localhost.');
}
$root = __DIR__;

// .env sederhana (sama seperti index.php)
if (is_readable("$root/.env")) {
    foreach (file("$root/.env", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $l) {
        $l = trim($l);
        if ($l === '' || $l[0] === '#' || strpos($l, '=') === false) continue;
        list($k, $v) = explode('=', $l, 2);
        if (getenv(trim($k)) === false) putenv(trim($k) . '=' . trim($v, " \t\"'"));
    }
}

$rows = [];
function add(&$rows, $group, $label, $ok, $hint = '') { $rows[] = [$group, $label, $ok, $hint]; }

// 1. PHP
add($rows, 'PHP', 'Versi PHP ' . PHP_VERSION, version_compare(PHP_VERSION, '7.4', '>='), 'Minimal PHP 7.4');
foreach (['mysqli', 'curl', 'gd', 'mbstring', 'openssl'] as $e) {
    add($rows, 'PHP', "Ekstensi $e", extension_loaded($e), "Aktifkan extension=$e di php.ini (XAMPP: Config > PHP) lalu restart Apache.");
}


// 1b. Rewrite (URL tanpa index.php)
$cfgtxt = @file_get_contents("$root/application/config/config.php") ?: '';
preg_match("/\\\$config\['index_page'\]\s*=\s*'([^']*)'/", $cfgtxt, $m);
add($rows, 'Rewrite', "config index_page = '" . ($m[1] ?? '?') . "'", isset($m[1]) && $m[1] === '', "Ubah menjadi \$config['index_page'] = ''; di application/config/config.php agar link tidak memuat index.php.");
add($rows, 'Rewrite', '.htaccess ada & berisi RewriteRule', is_readable("$root/.htaccess") && strpos((string) @file_get_contents("$root/.htaccess"), 'RewriteRule') !== false, 'Pastikan file bernama persis ".htaccess" (bukan ".htaccess.txt") di folder proyek.');
if (function_exists('apache_get_modules')) {
    add($rows, 'Rewrite', 'Apache: mod_rewrite aktif', in_array('mod_rewrite', apache_get_modules(), true), 'Buka C:\xampp\apache\conf\httpd.conf, hapus tanda # pada baris "LoadModule rewrite_module modules/mod_rewrite.so", lalu restart Apache.');
}
if (PHP_SAPI === 'cli-server') {
    add($rows, 'Rewrite', 'Tes otomatis dilewati (server bawaan PHP)', true);
} else {
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $base0 = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
    $probe = function ($path) use ($host) {
        $ch = curl_init("http://$host$path");
        curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 6, CURLOPT_FOLLOWLOCATION => false]);
        $body = (string) curl_exec($ch); $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE); curl_close($ch);
        return [$code, $body];
    };
    $isCI = function ($b) { return strpos($b, 'The page you requested was not found') !== false || strpos($b, 'A PHP Error') !== false || strpos($b, '<!doctype html') !== false; };
    list($c1, $b1) = $probe("$base0/index.php/__rewrite_probe");
    list($c2, $b2) = $probe("$base0/__rewrite_probe");
    add($rows, 'Rewrite', "Kontrol: $base0/index.php/... sampai ke CodeIgniter (HTTP $c1)", $isCI($b1), 'index.php tidak dieksekusi: cek PHP/Apache.');
    $clean = $isCI($b2);
    add($rows, 'Rewrite', "URL bersih $base0/... sampai ke CodeIgniter (HTTP $c2)", $clean,
        'URL tanpa index.php TIDAK diteruskan ke index.php. Penyebab umum di XAMPP: (1) mod_rewrite belum aktif (httpd.conf), (2) <Directory "C:/xampp/htdocs"> masih AllowOverride None -> ubah ke All, lalu restart Apache. (3) coba aktifkan baris "RewriteBase ' . $base0 . '/" di .htaccess.');
}

// 2. File penting (penyebab umum 404 = file belum ter-pull)
$need = [
    'index.php', '.htaccess', 'system/core/CodeIgniter.php',
    'application/controllers/Home.php', 'application/controllers/Auth.php', 'application/controllers/Account.php',
    'application/controllers/Games.php', 'application/controllers/Game.php', 'application/controllers/Order.php',
    'application/controllers/admin/Home.php', 'application/core/MY_Controller.php',
    'application/models/User_model.php', 'application/models/Admin_model.php',
    'application/views/auth/login.php', 'application/views/auth/register.php', 'application/views/auth/setup.php',
];
foreach ($need as $f) {
    add($rows, 'File', $f, is_file("$root/$f"), 'File tidak ada -> jalankan: git pull origin claude/sleepy-knuth-bhazcd (lihat juga git status).');
}
$old = ['application/controllers/Catalog.php', 'application/controllers/admin/Auth.php', 'application/controllers/admin/Dashboard.php'];
foreach ($old as $f) {
    if (is_file("$root/$f")) add($rows, 'File', "$f (versi LAMA, harusnya sudah dihapus)", false, 'Kode lama masih ada -> pull belum selesai / ada konflik.');
}

// 3. Konfigurasi
$cfg = @file_get_contents("$root/application/config/config.php") ?: '';
add($rows, 'Konfigurasi', 'csrf_protection aktif', (bool) preg_match("/csrf_protection'\]\s*=\s*TRUE/", $cfg), 'config.php Anda belum memuat versi terbaru.');
add($rows, 'Konfigurasi', 'sess_cookie_name = mytopup_sess', strpos($cfg, "mytopup_sess") !== false, 'config.php belum versi terbaru.');
$routes = @file_get_contents("$root/application/config/routes.php") ?: '';
add($rows, 'Konfigurasi', 'routes.php tanpa custom route', !preg_match('/\$route\[\'(?!default_controller|404_override|translate_uri_dashes)/', $routes), 'routes.php masih berisi route lama.');
add($rows, 'Konfigurasi', '.env ada', is_file("$root/.env"), 'Opsional: salin .env.example ke .env bila DB Anda bukan root tanpa password.');
add($rows, 'Konfigurasi', 'DUITKU_MERCHANT_CODE & DUITKU_API_KEY terisi', getenv('DUITKU_MERCHANT_CODE') && getenv('DUITKU_API_KEY'), 'Perlu untuk pembayaran (login tidak butuh).');

// 4. Folder yang harus bisa ditulis
foreach (['application/sessions', 'application/logs', 'application/cache', 'assets/uploads/games'] as $d) {
    if (!is_dir("$root/$d")) @mkdir("$root/$d", 0755, true);
    add($rows, 'Folder tulis', $d, is_dir("$root/$d") && is_writable("$root/$d"), 'Folder harus ada & bisa ditulis oleh Apache.');
}

// 5. Database
$db = null; $dbmsg = '';
mysqli_report(MYSQLI_REPORT_OFF);
$host = getenv('DB_HOST') ?: 'localhost'; $user = getenv('DB_USER') ?: 'root'; $pass = getenv('DB_PASS') ?: ''; $name = getenv('DB_NAME') ?: 'mytopup';
if (extension_loaded('mysqli')) {
    $db = @new mysqli($host, $user, $pass, $name);
    if ($db->connect_errno) { $dbmsg = $db->connect_error; $db = null; }
}
add($rows, 'Database', "Koneksi $user@$host / $name", $db !== null, $dbmsg ?: 'Cek DB_HOST/DB_USER/DB_PASS/DB_NAME di .env dan pastikan database sudah dibuat.');
if ($db) {
    foreach (['games', 'packs', 'orders', 'payment_logs', 'admins', 'login_attempts', 'users'] as $t) {
        $r = $db->query("SHOW TABLES LIKE '$t'");
        $ok = $r && $r->num_rows > 0;
        $hint = in_array($t, ['users'], true) ? 'Jalankan database/migrations/003_customer_accounts.sql'
              : (in_array($t, ['admins', 'login_attempts'], true) ? 'Jalankan database/migrations/002_admin_and_images.sql' : 'Impor database/schema.sql');
        add($rows, 'Database', "Tabel $t", $ok, $hint);
    }
    $r = @$db->query("SHOW COLUMNS FROM games LIKE 'image'");
    add($rows, 'Database', 'Kolom games.image', $r && $r->num_rows > 0, 'Jalankan database/migrations/002_admin_and_images.sql');
    $r = @$db->query("SHOW COLUMNS FROM orders LIKE 'user_id'");
    add($rows, 'Database', 'Kolom orders.user_id', $r && $r->num_rows > 0, 'Jalankan database/migrations/003_customer_accounts.sql');
    $r = @$db->query('SELECT COUNT(*) c FROM admins');
    $n = $r ? (int) $r->fetch_assoc()['c'] : -1;
    add($rows, 'Database', 'Jumlah admin: ' . ($n < 0 ? '?' : $n), $n >= 0, $n === 0 ? 'Belum ada admin: buka /auth/setup' : 'Tabel admins belum ada.');
    $r = @$db->query('SELECT COUNT(*) c FROM games');
    $n = $r ? (int) $r->fetch_assoc()['c'] : -1;
    add($rows, 'Database', 'Jumlah game: ' . ($n < 0 ? '?' : $n), $n > 0, 'Kosong -> impor database/seed.sql');
}

// 6. URL
$base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
$bad = count(array_filter($rows, function ($r) { return !$r[2]; }));
?>
<!doctype html><html lang="id"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Diagnostik MYTopup</title>
<style>
body{font:15px/1.5 system-ui,Segoe UI,sans-serif;background:#f6f7fb;color:#0f172a;margin:0;padding:24px}
.w{max-width:860px;margin:auto}h1{margin:0 0 4px}.s{padding:12px 16px;border-radius:12px;font-weight:600;margin:16px 0}
.ok{background:#ecfdf5;color:#065f46}.no{background:#fff1f2;color:#9f1239}
table{width:100%;border-collapse:collapse;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 1px 3px #0001}
td,th{padding:9px 14px;text-align:left;border-bottom:1px solid #eef0f6;vertical-align:top}th{background:#fafbfe;font-size:12px;text-transform:uppercase;color:#64748b}
.g td{background:#eef2ff;font-weight:700;color:#4338ca}.h{color:#9f1239;font-size:13px}code{background:#eef2ff;padding:1px 6px;border-radius:6px}
</style><div class="w">
<h1>Diagnostik MYTopup</h1><div style="color:#64748b">Folder: <code><?= htmlspecialchars($root) ?></code> · URL dasar: <code><?= htmlspecialchars($base ?: '/') ?></code></div>
<div class="s <?= $bad ? 'no' : 'ok' ?>"><?= $bad ? "$bad masalah ditemukan. Perbaiki yang bertanda ✖ (lihat petunjuk merah)." : 'Semua pemeriksaan lolos ✔. Coba buka: ' . htmlspecialchars($base) . '/auth/login' ?></div>
<table><?php $g = ''; foreach ($rows as $r): if ($r[0] !== $g): $g = $r[0]; ?><tr class="g"><td colspan="2"><?= htmlspecialchars($g) ?></td></tr><?php endif; ?>
<tr><td style="width:55%"><?= $r[2] ? '✔' : '✖' ?> <?= htmlspecialchars($r[1]) ?></td><td class="h"><?= $r[2] ? '' : htmlspecialchars($r[3]) ?></td></tr><?php endforeach; ?></table>
<p style="color:#64748b;font-size:13px">Tes rewrite: buka <code><?= htmlspecialchars($base) ?>/index.php/auth/login</code> (dengan index.php). Jika ini jalan tetapi tanpa index.php tidak, masalahnya <code>mod_rewrite</code>/<code>AllowOverride All</code> di Apache.</p>
</div></html>
