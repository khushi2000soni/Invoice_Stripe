<?php

namespace App\Models;

use App\Traits\InvoiceNumberGenerator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class Order extends Model
{
    use HasFactory,HasApiTokens,Notifiable,SoftDeletes , InvoiceNumberGenerator;

    public $table = 'orders';

    protected $fillable = [
        'customer_id',
        'thaila_price',
        'is_round_off',
        'round_off',
        'sub_total',
        'grand_total',
        'invoice_number',
        'invoice_date',
        'remark',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at',
        'updated_at',
        'deleted_at',
        'is_payment_done',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function(Order $model) {
            $model->created_by = auth()->user()->id;            
        });

        static::deleting(function (Order $model) {
            $model->deleted_by = auth()->user()->id;
            $model->save();

            // $model->orderProduct->each(function ($orderProduct) {
            //     $orderProduct->deleted_by = auth()->user()->id;
            //     $orderProduct->save();
            // });

            // $model->orderProduct()->delete();
        });

        static::updating(function(Order $model) {
            $model->updated_by = auth()->user()->id;
        });
    }

    public function customer(){
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function createdBy(){
        return $this->belongsTo(User::class, 'created_by');
    }

    public function deletedBy(){
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function orderProduct(){
        return $this->hasMany(OrderProduct::class, 'order_id');
    }
}
