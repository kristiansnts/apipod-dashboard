<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MidtransPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_payment_record(): void
    {
        $user = User::factory()->create();

        $payment = Payment::create([
            'user_id' => $user->id,
            'external_id' => 'ORDER-123',
            'amount' => 50000,
            'status' => 'PENDING'
        ]);

        $this->assertDatabaseHas('payments', [
            'external_id' => 'ORDER-123',
            'status' => 'PENDING'
        ]);
    }
}
