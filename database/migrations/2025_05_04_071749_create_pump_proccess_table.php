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
        Schema::create('pump_proccesses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('shift_id');
            $table->enum('type', ['intake a', 'intake b', 'intake c', 'distribusi a', 'distribusi b', 'distribusi c', 'distribusi d']);
            $table->enum('status', ['running', 'standby', 'normal']);
            $table->float('ampere')->default(0);
            $table->float('frequency')->default(0);
            $table->float('pressure')->default(0);
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
        Schema::dropIfExists('pump_proccesses');
    }
};
