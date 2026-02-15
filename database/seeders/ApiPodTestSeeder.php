<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Subscription;
use App\Models\LlmModel;
use App\Models\QuotaItem;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ApiPodTestSeeder extends Seeder {
    public function run(): void {
        $sub = Subscription::create([
            'sub_name' => 'Cursor Pro Auto',
            'price' => '50000',
            'system_prompt' => 'You are APIPod Smart AI. Be concise.'
        ]);

        $sonnet = LlmModel::create(['model_name' => 'claude-sonnet-4-5', 'upstream' => 'antigravity']);
        $flash = LlmModel::create(['model_name' => 'gemini-3-flash', 'upstream' => 'antigravity']);

        QuotaItem::create(['sub_id' => $sub->sub_id, 'llm_model_id' => $sonnet->llm_model_id, 'percentage_weight' => 20]);
        QuotaItem::create(['sub_id' => $sub->sub_id, 'llm_model_id' => $flash->llm_model_id, 'percentage_weight' => 80]);

        User::create([
            'name' => 'Test User',
            'email' => 'test@apipod.app',
            'password' => Hash::make('password'),
            'apitoken' => 'sk-test-1234567890abcdef',
            'sub_id' => $sub->sub_id,
            'active' => true
        ]);
    }
}
