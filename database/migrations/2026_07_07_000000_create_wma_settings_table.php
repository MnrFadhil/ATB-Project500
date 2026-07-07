<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wma_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->unsignedTinyInteger('w1');
            $table->unsignedTinyInteger('w2');
            $table->unsignedTinyInteger('w3');
            $table->timestamps();
        });

        DB::table('wma_settings')->insert([
            ['key' => 'air_baku',    'w1' => 1, 'w2' => 3,  'w3' => 30, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'dosis_kimia', 'w1' => 1, 'w2' => 1,  'w3' => 10, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('wma_settings');
    }
};
