<?php

namespace Tests\Feature;

use App\Models\Subscription;
use App\Models\LlmModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionWeightTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_have_multiple_quota_items_with_weights()
    {
        $provider = \App\Models\Provider::create([
            'name' => 'Test Provider',
            'base_url' => 'https://api.test.com',
            'api_key' => 'sk-test',
            'provider_type' => 'anthropic'
        ]);

        $subscription = Subscription::create([
            'sub_name' => 'Pro Plan',
            'price' => 150000,
            'system_prompt' => 'You are a helpful assistant',
        ]);

        $model1 = LlmModel::create([
            'model_name' => 'Sonnet 3.5',
            'upstream' => 'anthropic',
            'provider_id' => $provider->id
        ]);

        $model2 = LlmModel::create([
            'model_name' => 'Gemini Flash',
            'upstream' => 'google',
            'provider_id' => $provider->id
        ]);

        $subscription->quotaItems()->create([
            'llm_model_id' => $model1->llm_model_id,
            'percentage_weight' => 40,
            'token_limit' => 1000
        ]);

        $subscription->quotaItems()->create([
            'llm_model_id' => $model2->llm_model_id,
            'percentage_weight' => 60,
            'token_limit' => 1000
        ]);

        $this->assertCount(2, $subscription->quotaItems);
        $this->assertEquals(40, $subscription->quotaItems->first()->percentage_weight);
        $this->assertEquals(60, $subscription->quotaItems->last()->percentage_weight);
    }
}
