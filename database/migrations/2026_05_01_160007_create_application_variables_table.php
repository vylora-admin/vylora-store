<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_variables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->foreignId('application_user_id')->nullable()->constrained('application_users')->cascadeOnDelete();
            $table->string('key');
            $table->longText('value');
            $table->enum('scope', ['global', 'user', 'subscription'])->default('global');
            $table->integer('required_level')->default(0);
            $table->boolean('is_secret')->default(false);
            $table->timestamps();

            $table->index(['application_id', 'scope']);
            $table->index(['application_id', 'application_user_id', 'key'], 'app_var_app_user_key_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_variables');
    }
};
