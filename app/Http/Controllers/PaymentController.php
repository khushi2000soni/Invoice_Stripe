<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    
    public function createCheckoutSession($id)
    {
        try {
            $order = Order::findOrFail($id);
            Stripe::setApiKey(env('STRIPE_SECRET_KEY'));            

            $lineItems = [
                [
                    'price_data' => [
                        'currency' => 'INR',
                        'unit_amount' => ($order->grand_total) * 100, 
                        'product_data' => [
                            'name' => 'Order Total', 
                        ],
                    ],
                    'quantity' => 1,
                ],
            ];            

            $stripeCustomer = null;
            // Retrieve or create a Stripe Customer
            try {
                if ($order->customer && $order->customer->stripe_customer_id) {
                    $stripeCustomer = \Stripe\Customer::retrieve($order->customer->stripe_customer_id);
                } else {
                    $token = Str::random(32);
                    // If customer doesn't exist or hasn't been created in Stripe yet, create a new one
                    $stripeCustomer = \Stripe\Customer::create([
                        'name' => 'Test', // $order->customer->name, // Replace with actual customer's name
                        'email' => 'test@gmail.com', //$order->customer->email, // Replace with actual customer's email
                        "address" => [
                            "city" => 'Jaipur', 
                            "country" => 'India', 
                            "line1" => "VT Road", 
                            "line2" => "Mansovar", 
                            "postal_code" => 302012, 
                            "state" => "Rajasthan"
                        ]
                    ]);
    
                    // Store the Stripe customer ID in the database
                    $order->customer->stripe_customer_id = $stripeCustomer->id;
                    $order->customer->save();
                }
            } catch (\Stripe\Exception\InvalidRequestException $e) {
                return response()->json(['error' => 'Failed to retrieve or create Stripe customer: ' . $e->getMessage()], 500);
            }

            
            // Create the Stripe Checkout session
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'customer' => $stripeCustomer->id,
                'metadata' => ['order_id' => $order->id],
                'success_url' => route('payment.success', ['id' => $order->id]),
                'cancel_url' => route('payment.cancel', ['id' => $order->id]),
            ]);
            // Return the session ID to frontend
            return response()->json(['id' => $session->id]);

        } catch (\Exception $e) {
            dd($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    


    public function paymentSuccess($id)
    {
        // Handle successful payment
        $order = Order::findOrFail($id);
        // Update order status or do further processing
        return view('admin.payment.success', compact('order'));
    }

    public function paymentCancel($id)
    {
        // Handle payment cancellation
        $order = Order::findOrFail($id);
        return view('admin.payment.cancel', compact('order'));
    }

}
