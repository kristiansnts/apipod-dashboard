<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('plan_model', function (Blueprint $table) {
            $table->foreignId('plan_id')->constrained('plans')->onDelete('cascade');
            $table->foreignId('llm_model_id')->constrained('llm_models', 'llm_model_id')->onDelete('cascade');
            $table->primary(['plan_id', 'llm_model_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_model');
    }
};
