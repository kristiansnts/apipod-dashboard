<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('provider_accounts', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->foreignId('provider_id')->constrained()->cascadeOnDelete();
            $blueprint->string('email')->nullable();
            $blueprint->text('api_key'); // store refresh_token or ghp_token
            $blueprint->boolean('is_active')->default(true);
            $blueprint->timestamp('last_used_at')->nullable();
            $blueprint->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('provider_accounts');
    }
};
