<?php

namespace Tests\Feature;

use App\Models\LlmModel;
use App\Models\Subscription;
use App\Models\User;
use App\Models\UsageLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@apipod.app',
            'password' => Hash::make('password'),
        ]);
    }

    /** @test */
    public function it_can_manage_subscriptions()
    {
        $sub = Subscription::create([
            'sub_name' => 'Premium Plan',
            'price' => '200000',
            'system_prompt' => 'Be elite.'
        ]);

        $this->assertDatabaseHas('subscriptions', ['sub_name' => 'Premium Plan']);
        
        $sub->update(['price' => '250000']);
        $this->assertEquals('250000', $sub->fresh()->price);
    }

    /** @test */
    public function it_can_manage_llm_models()
    {
        $model = LlmModel::create([
            'model_name' => 'gpt-4o',
            'upstream' => 'nvidia'
        ]);

        $this->assertDatabaseHas('llm_models', ['model_name' => 'gpt-4o']);
    }

    /** @test */
    public function it_can_manage_users_with_api_tokens()
    {
        $sub = Subscription::create(['sub_name' => 'Free', 'price' => '0']);
        
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
            'apitoken' => 'sk-test-secret',
            'sub_id' => $sub->sub_id,
            'active' => true
        ]);

        $this->assertDatabaseHas('users', ['email' => 'john@example.com', 'apitoken' => 'sk-test-secret']);
        
        $user->update(['active' => false]);
        $this->assertFalse($user->fresh()->active);
    }

    /** @test */
    public function it_can_record_usage_logs()
    {
        $sub = Subscription::create(['sub_name' => 'Free', 'price' => '0']);
        $model = LlmModel::create(['model_name' => 'gemini-flash', 'upstream' => 'antigravity']);
        
        // Setup Quota Item
        $quota = \App\Models\QuotaItem::create([
            'sub_id' => $sub->sub_id,
            'llm_model_id' => $model->llm_model_id,
            'percentage_weight' => 100
        ]);

        $user = User::create([
            'name' => 'User', 'email' => 'u@e.com', 'password' => 'p', 'sub_id' => $sub->sub_id
        ]);

        UsageLog::create([
            'quota_item_id' => $quota->quota_id,
            'user_id' => $user->id,
            'requested_model' => 'cursor-sonnet',
            'routed_model' => 'gemini-flash',
            'upstream_provider' => 'antigravity',
            'token_count' => 150
        ]);

        $this->assertDatabaseHas('usage_logs', ['token_count' => 150]);
    }
}
