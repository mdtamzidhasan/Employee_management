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
    Schema::create('security_logs', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
        $table->string('event_type');        // login_success, login_failed, etc.
        $table->string('severity');          // info, warning, critical
        $table->string('ip_address', 45);
        $table->text('user_agent')->nullable();
        $table->string('url')->nullable();
        $table->text('description')->nullable();
        $table->json('metadata')->nullable(); // extra data
        $table->timestamps();

        // Fast query এর জন্য indexes
        $table->index('event_type');
        $table->index('severity');
        $table->index('ip_address');
        $table->index('created_at');
        $table->index(['user_id', 'created_at']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_logs');
    }
};
