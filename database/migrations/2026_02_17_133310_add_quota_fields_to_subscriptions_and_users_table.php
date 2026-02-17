<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->unsignedBigInteger('monthly_token_limit')->default(0)->after('price');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('tokens_used')->default(0)->after('sub_id');
            $table->timestamp('quota_reset_at')->nullable()->after('tokens_used');
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn('monthly_token_limit');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['tokens_used', 'quota_reset_at']);
        });
    }
};
