<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Menyelaraskan migration dengan struktur kolom `notes` yang sudah
 * diubah manual di database production (varchar(255) NOT NULL -> TEXT NULL)
 * untuk mengakomodasi catatan shift yang panjang.
 *
 * Pakai raw SQL (bukan Schema::table()->change()) supaya tidak perlu
 * package doctrine/dbal yang belum terpasang di project ini.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE shifts MODIFY notes TEXT NULL');
    }

    public function down(): void
    {
        // Catatan: revert ini bisa gagal/kepotong kalau ada data notes > 255 karakter.
        DB::statement("ALTER TABLE shifts MODIFY notes VARCHAR(255) NOT NULL DEFAULT ''");
    }
};
