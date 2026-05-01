<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->string('username');
            $table->string('email')->nullable();
            $table->string('password_hash');
            $table->string('hwid', 128)->nullable();
            $table->string('last_ip', 45)->nullable();
            $table->string('country', 8)->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->integer('level')->default(1);
            $table->json('variables')->nullable();
            $table->string('discord_id')->nullable();
            $table->boolean('two_factor_enabled')->default(false);
            $table->string('two_factor_secret')->nullable();
            $table->boolean('is_banned')->default(false);
            $table->text('ban_reason')->nullable();
            $table->timestamp('banned_at')->nullable();
            $table->timestamps();

            $table->unique(['application_id', 'username']);
            $table->index(['application_id', 'email']);
            $table->index('hwid');
            $table->index('is_banned');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_users');
    }
};
