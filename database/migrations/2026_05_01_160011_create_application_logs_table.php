<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->foreignId('application_user_id')->nullable()->constrained('application_users')->nullOnDelete();
            $table->enum('level', ['debug', 'info', 'warning', 'error', 'critical'])->default('info');
            $table->string('event_type', 64);
            $table->text('message');
            $table->json('context')->nullable();
            $table->string('ip', 45)->nullable();
            $table->string('hwid', 128)->nullable();
            $table->timestamps();

            $table->index(['application_id', 'event_type']);
            $table->index('level');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_logs');
    }
};
