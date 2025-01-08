<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class OrderProduct extends Model
{
    use HasFactory,HasApiTokens,Notifiable,SoftDeletes;
    public $table = 'order_products';

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
        'total_price',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at',
        'updated_at',
        'deleted_at',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function(OrderProduct $model) {
            $model->created_by = auth()->user()->id;
        });

        static::deleting(function(OrderProduct $model) {
            $model->deleted_by = auth()->user()->id;
            $model->save();
        });

        static::updating(function(OrderProduct $model) {
            $model->updated_by = auth()->user()->id;
        });
    }

    public function order(){
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function product(){
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function deletedBy() {
        return $this->belongsTo(User::class, 'deleted_by');
    }



}
