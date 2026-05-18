<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pump_chemicals', function (Blueprint $table) {
            // Tambah kolom pump_unit dengan default 'A'
            // Semua data lama otomatis jadi Pompa A
            $table->enum('pump_unit', ['A', 'B'])->default('A')->after('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pump_chemicals', function (Blueprint $table) {
            $table->dropColumn('pump_unit');
        });
    }
};