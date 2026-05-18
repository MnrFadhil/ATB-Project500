<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Alter enum type untuk menambah 'polymer'
        DB::statement("ALTER TABLE pump_chemicals MODIFY COLUMN type ENUM('pac', 'chlorine/kaporit', 'soda ash', 'polymer') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert ke enum lama
        DB::statement("ALTER TABLE pump_chemicals MODIFY COLUMN type ENUM('pac', 'chlorine/kaporit', 'soda ash') NOT NULL");
    }
};