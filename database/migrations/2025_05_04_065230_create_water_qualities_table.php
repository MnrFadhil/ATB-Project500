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
        Schema::create('water_qualities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('shift_id');
            $table->enum('type', ['air baku', 'sedimentation', 'reservoir']);
            $table->float('ph')->default(0);
            $table->float('turbidity')->default(0);
            $table->float('color')->default(0);
            $table->float('tds')->default(0);
            $table->float('free_chlor')->default(0);
            $table->float('orp')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('shift_id')->references('id')->on('shifts')
                ->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('water_qualities');
    }
};
