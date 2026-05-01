<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('licenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('license_key')->unique();
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->enum('type', ['trial', 'standard', 'extended', 'lifetime'])->default('standard');
            $table->enum('status', ['active', 'inactive', 'expired', 'suspended', 'revoked'])->default('active');
            $table->integer('max_activations')->default(1);
            $table->integer('current_activations')->default(0);
            $table->timestamp('issued_at')->useCurrent();
            $table->timestamp('expires_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['status', 'product_id']);
            $table->index('customer_email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('licenses');
    }
};
