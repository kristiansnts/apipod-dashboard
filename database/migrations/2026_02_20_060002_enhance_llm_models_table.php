<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('llm_models', function (Blueprint $table) {
            $table->boolean('tool_support')->default(false)->after('rpd');
            $table->integer('max_context')->nullable()->after('tool_support');
            $table->integer('default_weight')->default(100)->after('max_context');
        });
    }

    public function down(): void
    {
        Schema::table('llm_models', function (Blueprint $table) {
            $table->dropColumn(['tool_support', 'max_context', 'default_weight']);
        });
    }
};
