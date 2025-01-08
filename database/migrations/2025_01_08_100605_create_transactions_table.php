<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->json('order_json')->nullable();
            $table->string('payment_intent_id')->unique();
            $table->double('amount', 11, 2)->nullable();
            $table->string('currency', 3);
            $table->string('payment_method')->nullable(); // Credit card, bank transfer, etc.
            $table->enum('payment_type',['debit','credit'])->nullable();
            $table->json('payment_json')->nullable();
            $table->string('status')->comment('1=>success ,2=>failed');
            $table->string('receipt_url')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
