<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;

class StripeWebhookController extends Controller
{
    public function handleStripeWebhook(Request $request)
    {
        Log::info('Start stripe webhook');

        Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
        $payload = $request->getContent();
        $stripeSignatureHeader = $request->header('Stripe-Signature');

        $endpointSecret = env('STRIPE_WEBHOOK_SECRET_KEY'); // Replace with the actual signing secret

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $stripeSignatureHeader,
                $endpointSecret
            );
        } catch (\UnexpectedValueException $e) {
            Log::info('Invalid payload!');
            // Invalid payload
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            Log::info('Invalid signature!');
            $data = [
                'error_message' => $e->getMessage() . '->' . $e->getLine()
            ];
            return response()->json(['error' => 'Invalid signature', 'data' => $data], 400);
        }

        try {
            // Handle the event based on its type
            switch ($event->type) {
                case 'invoice.payment_succeeded':
                    $this->handleInvoicePaymentSucceeded($event->data->object);
                break;

                case 'invoice.payment_failed':
                    $this->handleInvoicePaymentFailed($event->data->object);
                break;

                case 'payment_intent.succeeded':
                    $this->handlePaymentIntentSucceeded($event->data->object);
                break;
                
                case 'payment_intent.payment_failed':
                    $this->handlePaymentIntentFailed($event->data->object);
                break;

                case 'checkout.session.completed':
                    $this->handleCheckoutSessionCompleted($event->data->object);
                break;

                default:
                    Log::info('Invalid Event fired!');
            }
        } catch (\Exception $e) {
            dd($e->getMessage().'->'.$e->getLine());
            return response()->json(['error' => $e->getMessage() . '->' . $e->getLine()], 400);
            // return response()->json(['error' => 'Something went wrong!'], 400);
        }

        Log::info('End stripe webhook');

        return response()->json(['success' => true]);
    }

    public function handleInvoicePaymentSucceeded($invoice)
    {
        try {
            // Retrieve the payment intent ID and related order/customer details
            $paymentIntentId = $invoice->payment_intent;
            $orderId = $invoice->metadata->order_id; // Assuming you pass order_id as metadata during payment
            $customerId = $invoice->customer;        // Stripe customer ID

            // Retrieve the corresponding order and customer from your database
            $order = Order::with('orderProduct.product')->first($orderId);
            $customer = Customer::where('stripe_customer_id', $customerId)->first();

            if (!$order || !$customer) {
                Log::error('Order or customer not found for payment intent: ' . $paymentIntentId);
                return;
            }

            // Insert a new transaction record
            Transaction::create([
                'order_id' => $order->id,
                'customer_id' => $customer->id,
                'order_json' => json_encode($order->toArray()),
                'payment_intent_id' => $paymentIntentId,
                'amount' => $invoice->amount_total / 100, // Stripe amounts are in cents
                'currency' => $invoice->currency,
                'payment_method' => $invoice->payment_method_types[0], // Example: 'card'
                'payment_type' => 'debit',
                'payment_json' => json_encode($invoice),
                'status' => '1', // 1 => success
                'receipt_url' => $invoice->url,
                'description' => $invoice->description,
            ]);

            Log::info('Transaction successfully recorded for payment intent: ' . $paymentIntentId);
        } catch (\Exception $e) {
            Log::error('Failed to record transaction: ' . $e->getMessage());
        }
    }

    private function handleInvoicePaymentFailed($invoice)
    {
        try {
            // Retrieve payment intent ID and related metadata
            $paymentIntentId = $invoice->payment_intent;
            $orderId = $invoice->metadata->order_id ?? null; // Ensure metadata contains order_id
            $customerId = $invoice->customer;                // Stripe customer ID

            // Retrieve the corresponding order and customer from your database
            $order = Order::with('orderProduct.product')->first($orderId);
            $customer = Customer::where('stripe_customer_id', $customerId)->first();

            if (!$order || !$customer) {
                Log::error('Order or customer not found for failed payment intent: ' . $paymentIntentId);
                return;
            }

            // Insert a new transaction record for failed payment
            Transaction::create([
                'order_id' => $order->id,
                'customer_id' => $customer->id,
                'order_json' => json_encode($order->toArray()),
                'payment_intent_id' => $paymentIntentId,
                'amount' => $invoice->amount / 100, // Stripe amounts are in cents
                'currency' => $invoice->currency,
                'payment_method' => $invoice->payment_method_types[0], // Example: 'card'
                'payment_type' => 'debit',
                'payment_json' => json_encode($invoice),
                'status' => '2', // 2 => failed
                'receipt_url' => null,
                'description' => 'Payment failed for order #' . $order->id,
            ]);

            Log::info('Transaction recorded for failed payment intent: ' . $paymentIntentId);
        } catch (\Exception $e) {
            Log::error('Failed to record failed transaction: ' . $e->getMessage());
        }
    }

    private function handlePaymentIntentSucceeded($paymentIntent)
    {
        try {
            $orderId = $paymentIntent->metadata->order_id ?? null;
            $order = Order::find($orderId);
            $customer = Customer::where('stripe_customer_id', $paymentIntent->customer)->first();

            if (!$order || !$customer) {
                Log::error('Order or customer not found for payment intent succeeded: ' . $paymentIntent->id);
                return;
            }

            Transaction::create([
                'order_id' => $order->id,
                'customer_id' => $customer->id,
                'order_json' => json_encode($order->toArray()),
                'payment_intent_id' => $paymentIntent->id,
                'amount' => $paymentIntent->amount / 100,
                'currency' => $paymentIntent->currency,
                'payment_method' => $paymentIntent->payment_method,
                'payment_type' => 'debit',
                'payment_json' => json_encode($paymentIntent),
                'status' => '1', // 1 => success
                'receipt_url' => $paymentIntent->charges->data[0]->receipt_url ?? null,
                'description' => 'Payment succeeded for order #' . $order->id,
            ]);

            Log::info('Transaction recorded for succeeded payment intent: ' . $paymentIntent->id);
        } catch (\Exception $e) {
            Log::error('Failed to record payment intent succeeded transaction: ' . $e->getMessage());
        }
    }

    private function handlePaymentIntentFailed($paymentIntent)
    {
        try {
            $orderId = $paymentIntent->metadata->order_id ?? null;
            $order = Order::find($orderId);
            $customer = Customer::where('stripe_customer_id', $paymentIntent->customer)->first();

            if (!$order || !$customer) {
                Log::error('Order or customer not found for payment intent failed: ' . $paymentIntent->id);
                return;
            }

            Transaction::create([
                'order_id' => $order->id,
                'customer_id' => $customer->id,
                'order_json' => json_encode($order->toArray()),
                'payment_intent_id' => $paymentIntent->id,
                'amount' => $paymentIntent->amount / 100,
                'currency' => $paymentIntent->currency,
                'payment_method' => $paymentIntent->payment_method,
                'payment_type' => 'debit',
                'payment_json' => json_encode($paymentIntent),
                'status' => '2', // 2 => failed
                'receipt_url' => null,
                'description' => 'Payment failed for order #' . $order->id,
            ]);

            Log::info('Transaction recorded for failed payment intent: ' . $paymentIntent->id);
        } catch (\Exception $e) {
            Log::error('Failed to record payment intent failed transaction: ' . $e->getMessage());
        }
    }

    private function handleCheckoutSessionCompleted($session)
    {
        try {
            $orderId = $session->metadata->order_id ?? null;
            $paymentIntentId = $session->payment_intent;
            $order = Order::find($orderId);
            $customer = Customer::where('stripe_customer_id', $session->customer)->first();

            if (!$order || !$customer) {
                Log::error('Order or customer not found for checkout session completed: ' . $paymentIntentId);
                return;
            }

            Transaction::create([
                'order_id' => $order->id,
                'customer_id' => $customer->id,
                'order_json' => json_encode($order->toArray()),
                'payment_intent_id' => $paymentIntentId,
                'amount' => $session->amount_total / 100,
                'currency' => $session->currency,
                'payment_method' => $session->payment_method_types[0],
                'payment_type' => 'debit',
                'payment_json' => json_encode($session),
                'status' => '1', // 1 => success
                'receipt_url' => $session->receipt_url ?? null,
                'description' => 'Checkout session completed for order #' . $order->id,
            ]);

            Log::info('Transaction recorded for checkout session completed: ' . $paymentIntentId);
        } catch (\Exception $e) {
            Log::error('Failed to record checkout session transaction: ' . $e->getMessage());
        }
    }




}
