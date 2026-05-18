<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE pump_chemicals MODIFY COLUMN type ENUM('pac', 'chlorine/kaporit', 'soda ash', 'polymer')");
        
        Schema::table('pump_chemicals', function (Blueprint $table) {
            $table->enum('status', ['standby', 'running'])->default('running')->after('type');
        });
    }

    public function down(): void
    {
        Schema::table('pump_chemicals', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        
        DB::statement("ALTER TABLE pump_chemicals MODIFY COLUMN type ENUM('pac', 'chlorine/kaporit')");
    }
};
