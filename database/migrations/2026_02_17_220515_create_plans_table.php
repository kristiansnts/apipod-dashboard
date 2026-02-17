<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2);
            $table->string('currency', 3)->default('IDR');
            $table->foreignId('sub_id')->nullable()->constrained('subscriptions', 'sub_id')->onDelete('set null');
            $table->integer('duration_days')->default(30);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('plan_id')->nullable()->after('user_id')->constrained('plans')->onDelete('set null');
            $table->foreignId('sub_id')->nullable()->after('plan_id')->constrained('subscriptions', 'sub_id')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['plan_id']);
            $table->dropForeign(['sub_id']);
            $table->dropColumn(['plan_id', 'sub_id']);
        });
        
        Schema::dropIfExists('plans');
    }
};
