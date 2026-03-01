<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->bigInteger('token_quota')->default(0)->after('duration_days');
            $table->integer('max_api_keys')->default(1)->after('token_quota');
            $table->integer('rate_limit_rpm')->nullable()->after('max_api_keys');
            $table->integer('rate_limit_tpm')->nullable()->after('rate_limit_rpm');
        });
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn(['token_quota', 'max_api_keys', 'rate_limit_rpm', 'rate_limit_tpm']);
        });
    }
};
