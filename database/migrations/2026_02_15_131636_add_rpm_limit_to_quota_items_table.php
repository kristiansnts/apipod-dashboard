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
        Schema::table('quota_items', function (Blueprint $table) {
            $table->integer('rpm_limit')->default(0)->comment('0 means no limit');
        });
    }

    public function down(): void
    {
        Schema::table('quota_items', function (Blueprint $table) {
            $table->dropColumn('rpm_limit');
        });
    }
};
