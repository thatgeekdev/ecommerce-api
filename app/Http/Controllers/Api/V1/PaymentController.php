<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\{Order, Payment};
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(private PaymentService $paymentService) {}

    public function init(Request $request, Order $order)
    {
        $data = $request->validate([
            'provider' => ['required', 'in:mpesa,emola,stripe,paypal,manual']
        ]);


        if ($order->user_id !== $request->user()->id) abort(403);

        if (!in_array($order->status, ['awaiting_payment', 'pending'])) {
            return response()->json(['message' => 'Encomenda não está elegível para pagamento.'], 422);
        }

        $payment = $this->paymentService->initPayment($order, $data['provider']);

        return response()->json([
            'message' => 'Pagamento iniciado',
            'payment' => $payment,
        ], 201);
    }

    // Webhook público: provider chama este endpoint para confirmar/cancelar
    public function webhook(Request $request, string $provider)
    {
        // TODO: validar assinatura HMAC do provider (X-Signature), idempotency key, etc.
        $payload = $request->all();
        $paymentId = $payload['payment_id'] ?? null;
        $status = $payload['status'] ?? null; // e.g., 'captured', 'failed'

        if (!$paymentId || !$status) {
            return response()->json(['message' => 'Payload inválido'], 400);
        }

        /** @var Payment $payment */
        $payment = Payment::query()->where('id', $paymentId)->where('provider', $provider)->first();
        if (!$payment) {
            return response()->json(['message' => 'Pagamento não encontrado'], 404);
        }

        if ($status === 'captured') {
            $this->paymentService->capture($payment, $payload);
        } elseif (in_array($status, ['failed', 'canceled'])) {
            $payment->update(['status' => $status, 'payload' => $payload]);
            $payment->order->update(['status' => 'pending']);
        }

        return response()->json(['message' => 'OK']);
    }
}
