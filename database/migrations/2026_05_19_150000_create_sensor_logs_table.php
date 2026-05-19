<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sensor_logs', function (Blueprint $table) {
            $table->id();
            $table->timestamp('timestamp');
            $table->float('pressure_intake')->nullable();
            $table->float('flow_intake')->nullable();
            $table->float('turbidity_baku')->nullable();
            $table->float('ph_baku')->nullable();
            $table->float('pressure_reservoir_a')->nullable();
            $table->float('pressure_reservoir_b')->nullable();
            $table->float('pressure_distribusi')->nullable();
            $table->float('turbidity_reservoir')->nullable();
            $table->float('ph_reservoir')->nullable();
            $table->float('free_chlorine')->nullable();
            $table->float('flow_yos_sudarso')->nullable();
            $table->float('flow_veteran')->nullable();
            $table->float('pressure_service')->nullable();
            $table->float('turbidity_sedimen')->nullable();
            $table->float('pressure_backwash')->nullable();
            $table->float('flow_backwash')->nullable();
            $table->float('pressure_service2')->nullable();
            $table->float('turbidity_filter')->nullable();
            $table->float('flow_sludgepond')->nullable();
            $table->float('pressure_kompressor')->nullable();
            $table->float('scm')->nullable();
            $table->float('freq_distribusi_a')->nullable();
            $table->float('freq_distribusi_c')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sensor_logs');
    }
};
