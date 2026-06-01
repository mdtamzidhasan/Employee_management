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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('position', 100)->nullable();
            $table->string('department', 100)->nullable();
            $table->string('phone', 20)->nullable();
            $table->decimal('salary', 15, 2)->nullable();
            $table->date('joining_date')->nullable();
            $table->text('address')->nullable();
            $table->string('profile_photo')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();



            $table->index('department');   
            $table->index('status');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
