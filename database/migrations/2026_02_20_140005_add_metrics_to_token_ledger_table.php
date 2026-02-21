<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('token_ledger', function (Blueprint $table) {
            $table->smallInteger('status_code')->default(200)->after('description');
            $table->integer('latency_ms')->default(0)->after('status_code');
            $table->boolean('cache_hit')->default(false)->after('latency_ms');
        });
    }

    public function down(): void
    {
        Schema::table('token_ledger', function (Blueprint $table) {
            $table->dropColumn(['status_code', 'latency_ms', 'cache_hit']);
        });
    }
};
