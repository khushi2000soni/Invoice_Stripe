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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->integer('customer_id')->unsigned()->index();
            $table->decimal('thaila_price', 15, 2)->default(null)->nullable();
            $table->tinyInteger('is_round_off')->default(0)->comment('1=> Yes, 0=>No');
            $table->decimal('round_off', 15, 2)->nullable();
            $table->decimal('sub_total', 15, 2);
            $table->decimal('grand_total', 15, 2);
			$table->string('invoice_number')->nullable();
			$table->date('invoice_date')->nullable();
			$table->string('remark')->nullable();
            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->integer('deleted_by')->unsigned()->nullable();
            $table->timestamps();
			$table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
