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
        Schema::create('wtps', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('shift_id');
            $table->string('flokulator_a')->nullable();
            $table->string('flokulator_b')->nullable();
            $table->string('clarifier_a')->nullable();
            $table->string('clarifier_b')->nullable();
            $table->string('filtration')->nullable();
            $table->float('gravity_filter_a')->default(0);
            $table->enum('gravity_filter_a_status', ['running', 'maintenance', 'standby']);
            $table->float('gravity_filter_b')->default(0);
            $table->enum('gravity_filter_b_status', ['running', 'maintenance', 'standby']);
            $table->float('gravity_filter_c')->default(0);
            $table->enum('gravity_filter_c_status', ['running', 'maintenance', 'standby']);
            $table->float('gravity_filter_d')->default(0);
            $table->enum('gravity_filter_d_status', ['running', 'maintenance', 'standby']);
            $table->float('gravity_filter_e')->default(0);
            $table->enum('gravity_filter_e_status', ['running', 'maintenance', 'standby']);
            $table->float('gravity_filter_f')->default(0);
            $table->enum('gravity_filter_f_status', ['running', 'maintenance', 'standby']);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('shift_id')->references('id')->on('shifts')
                ->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wtps');
    }
};
