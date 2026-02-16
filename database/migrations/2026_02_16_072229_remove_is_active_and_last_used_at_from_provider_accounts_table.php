<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('provider_accounts', function (Blueprint $table) {
            $table->dropColumn(['is_active', 'last_used_at']);
        });
    }

    public function down(): void
    {
        Schema::table('provider_accounts', function (Blueprint $table) {
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_used_at')->nullable();
        });
    }
};
