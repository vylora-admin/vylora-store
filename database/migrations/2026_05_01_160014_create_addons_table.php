<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('addons', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('version')->default('1.0.0');
            $table->string('author')->nullable();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->boolean('is_enabled')->default(false);
            $table->json('config')->nullable();
            $table->json('manifest')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addons');
    }
};
