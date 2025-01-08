Here's a detailed and structured **README** file for your Laravel project with Stripe integration, including webhook handling and payment processing:

---

# **Laravel Stripe Integration with Webhooks**

This project implements Stripe payment processing in a Laravel application, including webhook handling for different payment events. The system supports creating Stripe customers, handling payments, and responding to various webhook events such as successful or failed payments.

---

## **Features**

- Create Stripe customers and store their Stripe customer IDs in the database.
- Handle payment intents and process transactions upon successful payments.
- Manage webhook events for:
  - `payment_intent.succeeded`
  - `payment_intent.payment_failed`
  - `invoice.payment_succeeded`
  - `invoice.payment_failed`
  - `checkout.session.completed`
- Store transaction details in the database.
- Handle payment failures gracefully.
- Use Laravel’s logging system for debugging and error reporting.

---

## **Tech Stack**

- **Backend Framework**: Laravel 10
- **Payment Gateway**: Stripe
- **Database**: MySQL
- **Webhooks**: Stripe webhook events

---

## **Installation**

1. **Clone the Repository**

   ```bash
   git clone https://github.com/your-username/your-repo-name.git
   cd your-repo-name
   ```

2. **Install Dependencies**

   ```bash
   composer install
   ```

3. **Set Up Environment Variables**

   Create a `.env` file in the root directory and set the following environment variables:

   ```env
   STRIPE_SECRET_KEY=your-stripe-secret-key
   STRIPE_WEBHOOK_SECRET_KEY=your-stripe-webhook-signing-secret
   APP_URL=http://localhost:8000
   ```

4. **Run Migrations**

   ```bash
   php artisan migrate
   ```

5. **Run the Application**

   ```bash
   php artisan serve
   ```

---

## **Stripe Webhook Setup**

To handle Stripe webhooks:

1. Set up a webhook endpoint in the Stripe dashboard pointing to:  
   `http://your-domain.com/stripe/webhook`

2. Add your webhook signing secret to the `.env` file:

   ```env
   STRIPE_WEBHOOK_SECRET_KEY=your-webhook-signing-secret
   ```

3. Use the following Stripe CLI command to test webhooks locally:

   ```bash
   stripe listen --forward-to http://localhost:8000/stripe/webhook
   ```

---

## **Database Schema**

### **Transactions Table**

The `transactions` table stores the payment details:

| Column             | Type       | Description                                   |
|--------------------|------------|-----------------------------------------------|
| `id`               | `bigint`   | Primary key                                   |
| `order_id`         | `foreign`  | References the `orders` table                 |
| `customer_id`      | `foreign`  | References the `customers` table              |
| `order_json`       | `json`     | Stores the order data                        |
| `payment_intent_id`| `string`   | Unique Stripe payment intent ID              |
| `amount`           | `double`   | Payment amount                               |
| `currency`         | `string`   | Currency code (e.g., USD, INR)               |
| `payment_method`   | `string`   | Payment method used (e.g., card, bank)       |
| `payment_type`     | `enum`     | Type of payment (`debit`, `credit`)          |
| `payment_json`     | `json`     | Stores the complete payment data             |
| `status`           | `string`   | Payment status (`1` for success, `2` for fail)|
| `receipt_url`      | `string`   | Stripe receipt URL                           |
| `description`      | `text`     | Description of the transaction               |
| `created_at`       | `timestamp`| Timestamp when the record was created        |
| `updated_at`       | `timestamp`| Timestamp when the record was last updated   |
| `deleted_at`       | `timestamp`| Soft delete column                           |

---

## **Webhook Event Handling**

The following Stripe webhook events are handled in `StripeWebhookController`:

| Event Type                    | Handler Method                    | Description                                   |
|-------------------------------|-----------------------------------|---------------------------------------------|
| `payment_intent.succeeded`    | `handlePaymentIntentSucceeded()`  | Handles successful payment intent events     |
| `payment_intent.payment_failed`| `handlePaymentIntentFailed()`     | Handles failed payment intents               |
| `invoice.payment_succeeded`   | `handleInvoicePaymentSucceeded()` | Handles successful invoice payments          |
| `invoice.payment_failed`      | `handleInvoicePaymentFailed()`    | Handles failed invoice payments              |
| `checkout.session.completed`  | `handleCheckoutSessionCompleted()`| Handles successful checkout sessions         |

---

## **Example Webhook Handler**

Here’s an example of how `handlePaymentIntentSucceeded` works:

---

## **Error Handling**

- CSRF token errors (`419`) are prevented by excluding the webhook route from CSRF protection in `VerifyCsrfToken` middleware.
- Stripe signature verification errors are logged, and appropriate error responses are returned to Stripe.

---

## **Logging**

All significant events and errors are logged using Laravel’s logging system. You can check the logs in the `storage/logs/laravel.log` file.

---

## **Testing**

You can test the webhook events using the Stripe CLI or manually trigger events from the Stripe dashboard. Ensure that the correct webhook secret is used for verification.

