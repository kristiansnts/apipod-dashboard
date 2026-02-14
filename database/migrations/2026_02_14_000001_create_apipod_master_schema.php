<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id('sub_id');
            $table->string('sub_name', 100)->unique();
            $table->text('price')->nullable();
            $table->text('system_prompt')->nullable();
            $table->timestamps();
        });

        Schema::create('llm_models', function (Blueprint $table) {
            $table->id('llm_model_id');
            $table->string('model_name', 200);
            $table->string('upstream', 50);
            $table->timestamps();
        });

        Schema::create('quota_items', function (Blueprint $table) {
            $table->id('quota_id');
            $table->foreignId('sub_id')->constrained('subscriptions', 'sub_id')->onDelete('cascade');
            $table->foreignId('llm_model_id')->constrained('llm_models', 'llm_model_id')->onDelete('cascade');
            $table->integer('percentage_weight')->default(100);
            $table->bigInteger('token_limit')->nullable();
            $table->timestamps();
        });

        // Add columns to users table to match Go Proxy
        Schema::table('users', function (Blueprint $table) {
            $table->string('apitoken', 200)->nullable()->unique()->after('email');
            $table->foreignId('sub_id')->nullable()->after('apitoken')->constrained('subscriptions', 'sub_id');
            $table->boolean('active')->default(true)->after('sub_id');
            $table->timestamp('expires_at')->nullable()->after('active');
            // We use standard Laravel user_id (id) for compatibility
        });

        Schema::create('usage_logs', function (Blueprint $table) {
            $table->id('usage_id');
            $table->foreignId('quota_item_id')->constrained('quota_items', 'quota_id')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('requested_model', 100)->nullable();
            $table->string('routed_model', 100)->nullable();
            $table->string('upstream_provider', 50)->nullable();
            $table->integer('token_count')->default(0);
            $table->timestamp('timestamp')->useCurrent();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('external_id')->unique();
            $table->decimal('amount', 12, 2);
            $table->string('status'); // PENDING, SETTLED, EXPIRED
            $table->string('payment_method')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('usage_logs');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['apitoken', 'sub_id', 'active', 'expires_at']);
        });
        Schema::dropIfExists('quota_items');
        Schema::dropIfExists('llm_models');
        Schema::dropIfExists('subscriptions');
    }
};
