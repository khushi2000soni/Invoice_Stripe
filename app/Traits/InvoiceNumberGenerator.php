<?php

namespace App\Traits;

use App\Models\Order; // Make sure to import your Order model
use Illuminate\Support\Facades\Log;

trait InvoiceNumberGenerator
{
    /**
     * Generate a unique invoice number.
     *
     * @param int $orderId
     * @return string
     */
    public function generateUniqueInvoiceNumber($orderId)
    {
        $maxRetries = 5;
        $retryCount = 0;
        $invoiceNumber = generateInvoiceNumber($orderId); // Call your existing helper function

        do {
            // Check if the generated invoice number already exists
            $exists = Order::where('invoice_number', $invoiceNumber)->exists();
            if (!$exists) {
                return $invoiceNumber; // Return the unique invoice number
            }
            // If it exists, generate a new invoice number
            $invoiceNumber = generateInvoiceNumber($orderId);
            $retryCount++;
        } while ($retryCount < $maxRetries);

        Log::info("Unable to generate a unique invoice number after multiple attempts.");
        throw new \Exception('Unable to generate a unique invoice number after multiple attempts.');
    }
}
