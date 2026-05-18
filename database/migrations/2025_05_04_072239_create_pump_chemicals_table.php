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
        Schema::create('pump_chemicals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('shift_id');
            $table->enum('type', ['pac', 'chlorine/kaporit', 'soda ash', 'polymer']);
            $table->enum('status', ['standby', 'running'])->default('running');
            $table->float('frequency')->default(0);
            $table->float('dosage')->default(0);
            $table->float('concentration')->default(0);
            $table->float('stirring')->default(0);
            $table->float('tank_level')->default(0);
            $table->float('flow_rate')->default(0);
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
        Schema::dropIfExists('pump_chemicals');
    }
};
