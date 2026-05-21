<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sensor_logs', function (Blueprint $table) {
            $table->renameColumn('pressure_reservoir_a', 'level_reservoir_a');
            $table->renameColumn('pressure_reservoir_b', 'level_reservoir_b');
        });
    }

    public function down(): void
    {
        Schema::table('sensor_logs', function (Blueprint $table) {
            $table->renameColumn('level_reservoir_a', 'pressure_reservoir_a');
            $table->renameColumn('level_reservoir_b', 'pressure_reservoir_b');
        });
    }
};
