<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->foreignId('application_user_id')->nullable()->constrained('application_users')->nullOnDelete();
            $table->string('session_token', 128)->unique();
            $table->string('hwid', 128)->nullable();
            $table->string('ip', 45)->nullable();
            $table->string('country', 8)->nullable();
            $table->string('user_agent')->nullable();
            $table->boolean('is_validated')->default(false);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['application_id', 'application_user_id']);
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_sessions');
    }
};
