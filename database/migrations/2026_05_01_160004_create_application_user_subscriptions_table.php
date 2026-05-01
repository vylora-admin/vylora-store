<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_user_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_user_id')
                ->constrained('application_users', 'id', 'aus_app_user_id_fk')
                ->cascadeOnDelete();
            $table->foreignId('application_subscription_id')
                ->constrained('application_subscriptions', 'id', 'aus_app_sub_id_fk')
                ->cascadeOnDelete();
            $table->timestamp('starts_at')->useCurrent();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['application_user_id', 'is_active'], 'aus_user_active_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_user_subscriptions');
    }
};
