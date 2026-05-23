<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sensor_logs', function (Blueprint $table) {
            $table->renameColumn('pressure_service', 'flow_bypass_yoss');
            $table->renameColumn('turbidity_sedimen', 'flow_bypass_vet');
        });
    }

    public function down(): void
    {
        Schema::table('sensor_logs', function (Blueprint $table) {
            $table->renameColumn('flow_bypass_yoss', 'pressure_service');
            $table->renameColumn('flow_bypass_vet', 'turbidity_sedimen');
        });
    }
};
