<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->boolean('is_byok')->default(false)->after('rate_limit_tpm');
            $table->integer('daily_request_cap')->nullable()->after('is_byok');
        });
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn(['is_byok', 'daily_request_cap']);
        });
    }
};
