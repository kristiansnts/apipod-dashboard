<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('token_ledger', function (Blueprint $table) {
            $table->id();
            $table->foreignId('org_id')->constrained('organizations')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('api_key_id')->nullable()->constrained('api_keys')->onDelete('set null');
            $table->string('request_id')->nullable()->unique();
            $table->string('type');              // usage, topup, adjustment, reset
            $table->string('model')->nullable();
            $table->bigInteger('input_tokens')->default(0);
            $table->bigInteger('output_tokens')->default(0);
            $table->decimal('cost_usd', 12, 6)->default(0);
            $table->bigInteger('balance_after');
            $table->string('description')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['org_id', 'created_at']);
            $table->index(['org_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('token_ledger');
    }
};
