<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('secret', 128)->unique();
            $table->string('owner_uid', 64)->unique();
            $table->string('version')->default('1.0');
            $table->text('download_url')->nullable();
            $table->text('description')->nullable();
            $table->string('icon_url')->nullable();
            $table->boolean('is_paused')->default(false);
            $table->text('pause_reason')->nullable();
            $table->boolean('hwid_check_enabled')->default(true);
            $table->boolean('integrity_check_enabled')->default(false);
            $table->string('integrity_hash')->nullable();
            $table->boolean('disable_user_panel')->default(false);
            $table->boolean('allow_register')->default(true);
            $table->boolean('allow_login')->default(true);
            $table->boolean('allow_extend')->default(true);
            $table->integer('default_subscription_days')->default(30);
            $table->json('webhook_events')->nullable();
            $table->string('discord_webhook_url')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();

            $table->index('is_paused');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
