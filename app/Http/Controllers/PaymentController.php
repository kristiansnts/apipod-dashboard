<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Midtrans\Config;
use Midtrans\Notification;

class PaymentController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
    }

    public function webhook(Request $request)
    {
        Log::info('Midtrans webhook received', $request->all());

        try {
            $notification = new Notification();

            $orderId = $notification->order_id;
            $transactionStatus = $notification->transaction_status;
            $fraudStatus = $notification->fraud_status;
            $paymentType = $notification->payment_type;

            $payment = Payment::where('external_id', $orderId)->first();

            if (!$payment) {
                Log::warning('Payment not found for order_id: ' . $orderId);
                return response()->json(['message' => 'Payment not found'], 404);
            }

            // Map Midtrans statuses to payment outcomes
            if ($transactionStatus === 'capture') {
                // For credit card: check fraud status
                if ($fraudStatus === 'accept') {
                    $this->handlePaymentSuccess($payment, $paymentType);
                } elseif ($fraudStatus === 'challenge') {
                    $payment->status = 'CHALLENGE';
                    $payment->save();
                }
            } elseif ($transactionStatus === 'settlement') {
                $this->handlePaymentSuccess($payment, $paymentType);
            } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
                $payment->status = strtoupper($transactionStatus);
                $payment->save();
            } elseif ($transactionStatus === 'pending') {
                $payment->status = 'PENDING';
                $payment->save();
            }

            return response()->json(['message' => 'Webhook processed'], 200);
        } catch (\Exception $e) {
            Log::error('Midtrans webhook error: ' . $e->getMessage());
            return response()->json(['message' => 'Webhook error'], 500);
        }
    }

    private function handlePaymentSuccess(Payment $payment, string $paymentType): void
    {
        $payment->status = 'PAID';
        $payment->payment_method = $paymentType;
        $payment->paid_at = now();
        $payment->save();

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

            Log::info('User subscription updated via Midtrans', [
                'user_id' => $user->id,
                'sub_id' => $user->sub_id,
                'expires_at' => $user->expires_at,
                'payment_type' => $paymentType,
            ]);
        }
    }
}
