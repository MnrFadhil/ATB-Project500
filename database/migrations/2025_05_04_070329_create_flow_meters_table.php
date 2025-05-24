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
        Schema::create('flow_meters', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('shift_id');
            $table->enum('location', ['yos sudarso', 'veteran'])->nullable();
            $table->float('flow')->default(0);
            $table->float('totalizer')->default(0);
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
        Schema::dropIfExists('flow_meters');
    }
};
