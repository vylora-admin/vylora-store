<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_webhooks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('url');
            $table->string('secret', 128)->nullable();
            $table->json('events');
            $table->boolean('is_active')->default(true);
            $table->integer('retry_count')->default(3);
            $table->integer('timeout_seconds')->default(10);
            $table->json('headers')->nullable();
            $table->timestamps();
        });

        Schema::create('application_webhook_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_webhook_id')->constrained('application_webhooks')->cascadeOnDelete();
            $table->string('event');
            $table->json('payload');
            $table->integer('status_code')->nullable();
            $table->text('response')->nullable();
            $table->boolean('is_success')->default(false);
            $table->integer('attempts')->default(0);
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_webhook_deliveries');
        Schema::dropIfExists('application_webhooks');
    }
};
