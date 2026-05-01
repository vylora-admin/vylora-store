<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_chat_channels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('required_level')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['application_id', 'name']);
        });

        Schema::create('application_chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_chat_channel_id')->constrained('application_chat_channels')->cascadeOnDelete();
            $table->foreignId('application_user_id')->nullable()->constrained('application_users')->nullOnDelete();
            $table->string('username_snapshot');
            $table->text('message');
            $table->timestamps();

            $table->index('application_chat_channel_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_chat_messages');
        Schema::dropIfExists('application_chat_channels');
    }
};
