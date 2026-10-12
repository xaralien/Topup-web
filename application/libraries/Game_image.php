<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Unggah gambar game: validasi ketat lalu di-encode ULANG dengan GD (maks 640px).
 * Re-encode membuang metadata/payload tersembunyi, jadi file "gambar" yang sebenarnya skrip tidak lolos.
 *
 * Tanpa ekstensi GD (mis. XAMPP default) tetap jalan: gambar disimpan APA ADANYA setelah pemeriksaan
 * tipe/ukuran/isi (tanpa resize & re-encode). Aktifkan GD (php.ini: extension=gd) untuk hasil optimal.
 */
class Game_image
{
    const DIR = 'assets/uploads/games/';
    const MAX_KB = 2048;
    const MAX_SIDE = 640;

    private $CI;
    public $error = '';

    public static function has_gd()
    {
        return function_exists('imagecreatefromstring') && function_exists('imagecreatetruecolor');
    }

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->library('upload');
    }

    /** @return string|null path relatif untuk disimpan di DB, null bila gagal (lihat $this->error). */
    public function store($field)
    {
        $this->error = '';
        if (empty($_FILES[$field]['name'])) {
            return null;                                   // tidak ada file diunggah
        }
        $dir = FCPATH . self::DIR;
        if (!is_dir($dir) && !@mkdir($dir, 0755, TRUE)) {
            $this->error = 'Folder upload tidak bisa dibuat.';
            return null;
        }
        $this->CI->upload->initialize([
            'upload_path' => $dir, 'allowed_types' => 'jpg|jpeg|png|webp', 'max_size' => self::MAX_KB,
            'encrypt_name' => TRUE, 'file_ext_tolower' => TRUE, 'overwrite' => FALSE,
        ], TRUE);
        if (!$this->CI->upload->do_upload($field)) {
            $this->error = trim(strip_tags($this->CI->upload->display_errors('', ' ')));
            return null;
        }
        $tmp = $this->CI->upload->data('full_path');
        $out = $this->reencode($tmp, $dir);
        @unlink($tmp);
        return $out;
    }

    private function reencode($file, $dir)
    {
        $info = @getimagesize($file);
        if (!$info || !in_array($info[2], [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_WEBP], TRUE)) {
            $this->error = 'File bukan gambar JPG/PNG/WebP yang valid.';
            return null;
        }
        if ($info[0] * $info[1] > 25000000) {
            $this->error = 'Dimensi gambar terlalu besar.';
            return null;
        }
        if (!self::has_gd()) {
            return $this->keep_original($file, $dir, $info);
        }
        $src = @imagecreatefromstring((string) file_get_contents($file));
        if (!$src) {
            $this->error = 'Gambar rusak atau tidak bisa dibaca.';
            return null;
        }
        $w = imagesx($src); $h = imagesy($src);
        $scale = min(1, self::MAX_SIDE / max($w, $h));
        $nw = max(1, (int) round($w * $scale)); $nh = max(1, (int) round($h * $scale));
        $dst = imagecreatetruecolor($nw, $nh);
        imagealphablending($dst, FALSE); imagesavealpha($dst, TRUE);
        imagefill($dst, 0, 0, imagecolorallocatealpha($dst, 0, 0, 0, 127));
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);

        $has_alpha = $info[2] !== IMAGETYPE_JPEG;
        $name = bin2hex(random_bytes(12)) . ($has_alpha ? '.png' : '.jpg');
        $ok = $has_alpha ? imagepng($dst, $dir . $name, 7) : imagejpeg($dst, $dir . $name, 85);
        imagedestroy($src); imagedestroy($dst);
        if (!$ok) {
            $this->error = 'Gagal menyimpan gambar.';
            return null;
        }
        @chmod($dir . $name, 0644);
        return self::DIR . $name;
    }

    /** Cadangan tanpa GD: simpan file asli dengan nama acak + ekstensi sesuai isi (bukan kiriman pengguna). */
    private function keep_original($file, $dir, array $info)
    {
        $bytes = (string) file_get_contents($file);
        // tolak bila ada penanda kode (skrip yang disisipkan di dalam "gambar")
        if (preg_match('/<\?(php|=)|<script|<%/i', $bytes)) {
            $this->error = 'File ditolak: mengandung kode yang mencurigakan.';
            return null;
        }
        $ext = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp'][$info[2]];
        $name = bin2hex(random_bytes(12)) . '.' . $ext;
        if (!@rename($file, $dir . $name)) {
            $this->error = 'Gagal menyimpan gambar.';
            return null;
        }
        @chmod($dir . $name, 0644);
        return self::DIR . $name;
    }

    /** Hapus file lama – hanya jika path benar-benar di dalam folder upload. */
    public function remove($rel)
    {
        if ($rel && strpos($rel, self::DIR) === 0 && basename($rel) === substr($rel, strlen(self::DIR))) {
            @unlink(FCPATH . $rel);
        }
    }
}
