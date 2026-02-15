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
        Schema::table('llm_models', function (Blueprint $table) {
            $table->decimal('input_cost_per_1m', 10, 4)->default(0);
            $table->decimal('output_cost_per_1m', 10, 4)->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('llm_models', function (Blueprint $table) {
            $table->dropColumn(['input_cost_per_1m', 'output_cost_per_1m']);
        });
    }
};
