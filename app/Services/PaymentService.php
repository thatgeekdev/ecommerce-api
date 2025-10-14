<?php

namespace App\Services;

use App\Models\{Order, Payment};
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function initPayment(Order $order, string $provider): Payment
    {
        // Em produção: chamar SDK/HTTP do provedor (mpesa/emola/stripe/paypal)
        return DB::transaction(function () use ($order, $provider) {
            return Payment::create([
                'order_id' => $order->id,
                'provider' => $provider,
                'status' => 'pending',
                'amount' => $order->total,
                'currency' => $order->currency,
            ]);
        });
    }

    public function capture(Payment $payment, array $payload = []): Payment
    {
        return DB::transaction(function () use ($payment, $payload) {
            $payment->update(['status' => 'captured', 'payload' => $payload]);
            $payment->order->update(['status' => 'paid']);
            return $payment->fresh('order');
        });
    }
}
