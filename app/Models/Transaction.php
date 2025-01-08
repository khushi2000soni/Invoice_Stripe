<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes;

    public $table = 'transactions';
  
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'customer_id',
        'order_id',
        'order_json',
        'payment_intent_id',
        'payment_method',
        'amount',
        'currency',
        'status',
        'payment_type',
        'payment_method',
        'payment_json',
        'receipt_url',
        'description',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

}
