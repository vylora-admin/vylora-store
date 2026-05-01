<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('license_activations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('license_id')->constrained()->onDelete('cascade');
            $table->string('machine_name')->nullable();
            $table->string('hardware_id')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('domain')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('activated_at')->useCurrent();
            $table->timestamp('deactivated_at')->nullable();
            $table->timestamps();

            $table->index(['license_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('license_activations');
    }
};
