<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk tabel `wma_settings` — tempat menyimpan bobot WMA (Weighted Moving Average)
 * yang bisa diubah admin tanpa perlu ubah kode (makanya disebut "bobot dinamis").
 *
 * Struktur tabel wma_settings:
 *   - id          : primary key otomatis
 *   - key         : nama pengelompokan bobot, cuma ada 2 nilai yang dipakai sistem:
 *                   'air_baku'    → dipakai WMA prediksi turbidity/pH/TDS air baku
 *                   'dosis_kimia' → dipakai WMA prediksi dosis PAC/Klorin bulanan
 *   - w1, w2, w3  : tiga angka bobot. Urutan di kolom = urutan dipakai di rumus WMA,
 *                   w1 dikalikan ke data TERLAMA, w3 dikalikan ke data TERBARU.
 *                   Contoh row 'air_baku': w1=1, w2=3, w3=30
 *   - created_at / updated_at : otomatis dari Eloquent timestamp
 *
 * Isi awal tabel ini di-seed lewat migration
 * database/migrations/2026_07_07_000000_create_wma_settings_table.php,
 * lalu bisa diubah kapan saja oleh admin lewat halaman "Evaluasi WMA" atau
 * "Prediksi Dosis WMA" (lihat method saveWeights() di WmaEvaluation.php /
 * WmaDosisPrediksi.php).
 */
class WmaSetting extends Model
{
    // Kolom yang boleh diisi lewat mass-assignment (create/update massal)
    protected $fillable = ['key', 'w1', 'w2', 'w3'];

    /**
     * Ambil bobot [w1, w2, w3] untuk satu key ('air_baku' atau 'dosis_kimia').
     *
     * Cara kerja:
     * 1. Cari 1 baris di tabel wma_settings yang key-nya cocok.
     * 2. Kalau baris ADA di database → pakai angka w1/w2/w3 dari database itu.
     *    Ini yang bikin bobot "dinamis" — kalau admin ubah angkanya lewat form,
     *    baris ini ikut berubah, dan fungsi ini langsung mengembalikan angka baru
     *    di request berikutnya (tanpa perlu deploy ulang kode).
     * 3. Kalau baris TIDAK ADA (misal tabel baru di-migrate & belum di-seed) →
     *    pakai $default yang dikirim oleh si pemanggil, supaya sistem tetap
     *    bisa jalan dengan bobot cadangan.
     *
     * @param  string $key      'air_baku' atau 'dosis_kimia'
     * @param  array  $default  bobot cadangan kalau row belum ada di DB, contoh [1, 3, 30]
     * @return array            [w1, w2, w3] — semua sudah dalam bentuk integer
     */
    public static function getWeights(string $key, array $default): array
    {
        $row = static::where('key', $key)->first();
        return $row ? [(int) $row->w1, (int) $row->w2, (int) $row->w3] : $default;
    }
}
