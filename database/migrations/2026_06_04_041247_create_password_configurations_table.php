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
     Schema::create('password_configurations', function (Blueprint $table) {
        $table->id();
        $table->integer('min_length')->default(8);
        $table->integer('max_length')->default(64);
        $table->integer('min_words')->default(0);
        $table->boolean('require_uppercase')->default(true);
        $table->boolean('require_lowercase')->default(true);
        $table->boolean('require_number')->default(true);
        $table->boolean('require_special_char')->default(false);
        $table->integer('password_expiry_days')->default(90);
        $table->integer('change_cooldown_hours')->default(24);
        $table->integer('password_history_count')->default(5);
        $table->timestamps();
     });
   }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('password_configurations');
    }
};
