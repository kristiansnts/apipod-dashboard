<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->foreignId('active_model_id')->nullable()->after('daily_request_date')
                ->constrained('llm_models', 'llm_model_id')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropForeign(['active_model_id']);
            $table->dropColumn('active_model_id');
        });
    }
};
