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
        $subscription = Subscription::create([
            'sub_name' => 'Pro Plan',
            'price' => 150000,
            'system_prompt' => 'You are a helpful assistant',
        ]);

        $model1 = LlmModel::create([
            'model_name' => 'Sonnet 3.5',
            'upstream' => 'anthropic'
        ]);

        $model2 = LlmModel::create([
            'model_name' => 'Gemini Flash',
            'upstream' => 'google'
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
