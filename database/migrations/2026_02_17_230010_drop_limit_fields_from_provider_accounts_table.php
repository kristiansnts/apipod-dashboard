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
            $table->dropColumn(['limit_type', 'limit_value']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('provider_accounts', function (Blueprint $table) {
            $table->string('limit_type')->default('rpm');
            $table->integer('limit_value')->default(10);
        });
    }
};
