<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('org_id')->nullable()->after('id')->constrained('organizations')->onDelete('set null');
            $table->string('role')->default('admin')->after('org_id');
        });

        // Migrate existing users: create a default org for each
        $users = DB::table('users')->whereNull('org_id')->get();
        foreach ($users as $user) {
            $slug = Str::slug($user->name . '-' . $user->id);
            $orgId = DB::table('organizations')->insertGetId([
                'name' => $user->name . "'s Organization",
                'slug' => $slug,
                'plan_id' => null,
                'token_balance' => 0,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('users')->where('id', $user->id)->update(['org_id' => $orgId, 'role' => 'admin']);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['org_id']);
            $table->dropColumn(['org_id', 'role']);
        });
    }
};
