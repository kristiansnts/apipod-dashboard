<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PaymentController extends Controller
{
    public function webhook(Request $request)
    {
        Log::info('Xendit webhook received', $request->all());

        $externalId = $request->input('external_id');
        $status = $request->input('status');

        $payment = Payment::where('external_id', $externalId)->first();

        if (!$payment) {
            Log::warning('Payment not found for external_id: ' . $externalId);
            return response()->json(['message' => 'Payment not found'], 404);
        }

        $payment->status = $status;

        if ($status === 'PAID' || $status === 'SETTLED') {
            $payment->payment_method = $request->input('payment_method');
            $payment->paid_at = now();

            $user = User::find($payment->user_id);
            
            if ($user && $payment->sub_id) {
                $plan = $payment->plan;
                
                $user->sub_id = $payment->sub_id;
                $user->active = true;

                if ($user->expires_at && Carbon::parse($user->expires_at)->isFuture()) {
                    $user->expires_at = Carbon::parse($user->expires_at)->addDays($plan->duration_days);
                } else {
                    $user->expires_at = now()->addDays($plan->duration_days);
                }

                $user->tokens_used = 0;
                $user->quota_reset_at = now()->addMonth();
                
                $user->save();

                Log::info('User subscription updated', [
                    'user_id' => $user->id,
                    'sub_id' => $user->sub_id,
                    'expires_at' => $user->expires_at
                ]);
            }
        }

        $payment->save();

        return response()->json(['message' => 'Webhook processed'], 200);
    }
}
