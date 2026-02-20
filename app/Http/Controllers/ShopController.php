<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Xendit\Configuration;
use Xendit\Invoice\InvoiceApi;
use Xendit\Invoice\CreateInvoiceRequest;

class ShopController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        Configuration::setXenditKey(config('services.xendit.secret_key'));
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

        $externalId = 'inv-' . $user->id . '-' . time();

        $payment = Payment::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'sub_id' => $plan->sub_id,
            'external_id' => $externalId,
            'amount' => $plan->price,
            'status' => 'PENDING',
        ]);

        try {
            $apiInstance = new InvoiceApi();

            $createInvoiceRequest = new CreateInvoiceRequest([
                'external_id' => $externalId,
                'amount' => $plan->price,
                'description' => 'Purchase: ' . $plan->name,
                'invoice_duration' => 86400,
                'customer' => [
                    'given_names' => $user->name,
                    'email' => $user->email,
                ],
                'customer_notification_preference' => [
                    'invoice_created' => ['email'],
                    'invoice_reminder' => ['email'],
                    'invoice_paid' => ['email'],
                ],
                'success_redirect_url' => route('shop.success', ['payment' => $payment->id]),
                'failure_redirect_url' => route('shop.failed', ['payment' => $payment->id]),
            ]);

            $invoice = $apiInstance->createInvoice($createInvoiceRequest);

            return redirect($invoice->getInvoiceUrl());
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
