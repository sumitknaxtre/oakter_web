<?php

namespace App\Http\Controllers;

use App\Services\Checkout\CheckoutPaymentCompletionService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

class RazorpayWebhookController extends Controller
{
    public function __construct(
        private readonly CheckoutPaymentCompletionService $paymentCompletionService,
    ) {}

    public function __invoke(Request $request): Response
    {
        $webhookSecret = config('razorpay.webhook_secret');

        if (! is_string($webhookSecret) || $webhookSecret === '') {
            abort(503, 'Razorpay webhook is not configured.');
        }

        $payload = $request->getContent();
        $signature = $request->header('X-Razorpay-Signature', '');

        $api = new Api(config('razorpay.key_id'), config('razorpay.key_secret'));

        try {
            $api->utility->verifyWebhookSignature($payload, $signature, $webhookSecret);
        } catch (SignatureVerificationError) {
            return response('Invalid signature.', 400);
        }

        $event = json_decode($payload, true);

        if (! is_array($event)) {
            return response('Invalid payload.', 400);
        }

        $eventName = $event['event'] ?? '';

        if (! in_array($eventName, ['payment.captured', 'order.paid'], true)) {
            return response('Ignored.', 200);
        }

        [$razorpayOrderId, $razorpayPaymentId] = $this->extractPaymentDetails($event);

        if ($razorpayOrderId === null || $razorpayPaymentId === null) {
            return response('Missing payment details.', 422);
        }

        $result = $this->paymentCompletionService->completeFromRazorpayPayment(
            $razorpayOrderId,
            $razorpayPaymentId,
            $request->ip(),
            $request->userAgent(),
        );

        if (! $result->success && ! $result->alreadyPaid) {
            report(new \RuntimeException('Razorpay webhook payment completion failed: '.$result->message));
        }

        return response('OK', 200);
    }

    /**
     * @param  array<string, mixed>  $event
     * @return array{0: ?string, 1: ?string}
     */
    private function extractPaymentDetails(array $event): array
    {
        $payment = $event['payload']['payment']['entity'] ?? null;

        if (is_array($payment)) {
            return [
                $payment['order_id'] ?? null,
                $payment['id'] ?? null,
            ];
        }

        $order = $event['payload']['order']['entity'] ?? null;

        if (is_array($order)) {
            return [
                $order['id'] ?? null,
                $event['payload']['payment']['entity']['id'] ?? null,
            ];
        }

        return [null, null];
    }
}
