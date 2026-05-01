<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('file_path');
            $table->string('original_filename')->nullable();
            $table->bigInteger('size')->default(0);
            $table->string('hash', 128)->nullable();
            $table->boolean('is_encrypted')->default(false);
            $table->integer('required_level')->default(0);
            $table->foreignId('required_subscription_id')->nullable()->constrained('application_subscriptions')->nullOnDelete();
            $table->integer('download_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['application_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_files');
    }
};
