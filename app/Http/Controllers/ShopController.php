<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Midtrans\Config;
use Midtrans\Snap;

class ShopController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        Config::$serverKey = config('services.midtrans.server_key');
        Config::$clientKey = config('services.midtrans.client_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized = config('services.midtrans.is_sanitized');
        Config::$is3ds = config('services.midtrans.is_3ds');
    }

    public function index()
    {
        $plans = Plan::where('is_active', true)
            ->with('subscription')
            ->orderBy('price')
            ->get();

        return view('shop.index', compact('plans'));
    }

    public function purchase(Request $request, Plan $plan)
    {
        if ($request->isMethod('get')) {
            return redirect()->route('shop.index');
        }

        $user = Auth::user();

        $orderId = 'ORDER-' . $user->id . '-' . time();

        $payment = Payment::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'sub_id' => $plan->sub_id,
            'external_id' => $orderId,
            'amount' => $plan->price,
            'status' => 'PENDING',
        ]);

        // Handle free plans — activate immediately without payment gateway
        if ($plan->price <= 0) {
            $payment->update([
                'status' => 'PAID',
                'payment_method' => 'free',
                'paid_at' => now(),
            ]);

            $user->sub_id = $plan->sub_id;
            $user->active = true;
            $user->expires_at = now()->addDays($plan->duration_days);
            $user->tokens_used = 0;
            $user->quota_reset_at = now()->addMonth();
            $user->save();

            return redirect()->route('shop.success', ['payment' => $payment->id]);
        }

        try {
            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => (int) $plan->price,
                ],
                'customer_details' => [
                    'first_name' => $user->name,
                    'email' => $user->email,
                ],
                'item_details' => [
                    [
                        'id' => $plan->id,
                        'price' => (int) $plan->price,
                        'quantity' => 1,
                        'name' => $plan->name,
                    ],
                ],
                'callbacks' => [
                    'finish' => route('shop.success', ['payment' => $payment->id]),
                ],
            ];

            $snapToken = Snap::getSnapToken($params);

            return view('shop.checkout', [
                'snapToken' => $snapToken,
                'payment' => $payment,
                'plan' => $plan,
                'clientKey' => config('services.midtrans.client_key'),
                'isProduction' => config('services.midtrans.is_production'),
            ]);
        } catch (\Exception $e) {
            $payment->delete();
            return back()->with('error', 'Failed to create payment: ' . $e->getMessage());
        }
    }

    public function success(Payment $payment)
    {
        return view('shop.success', compact('payment'));
    }

    public function failed(Payment $payment)
    {
        return view('shop.failed', compact('payment'));
    }
}
