<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_blacklist', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['hwid', 'ip', 'username', 'country', 'email']);
            $table->string('value');
            $table->text('reason')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['application_id', 'type', 'value']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_blacklist');
    }
};
