<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

class Product extends Model
{
    use HasFactory,HasApiTokens,Notifiable;
    public $table = 'products';

    protected $fillable = [
        'name',
        'category_id',
        'created_by',
        'updated_at',
        'deleted_at',
        'is_active',
        'is_verified',
    ];

    protected $appends = [
        'order_count','full_name'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function(Product $model) {
            $model->created_by = auth()->user()->id;
        });
    }


    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderProduct()
    {
        return $this->hasMany(OrderProduct::class);
    }

    /**
     * Accessor to get the count of orders for this product.
     *
     * @return int
     */

    public function getOrderCountAttribute()
    {
        return Order::whereHas('orderProduct', function ($query) {
            $query->where('product_id', $this->id);
        })->count();
    }

    public function getFullNameAttribute()
    {
        return $this->name . ' - ' .$this->category->name;
    }

    public static function getFilteredProducts($request)
    {
        $category_id = $request->category_id ?? null;
        $address_id = $request->address_id ?? null;
        $from_date = $request->from_date ?? null;
        $to_date = $request->to_date ?? null;
        $query = self::query();
        $query->select([
            'categories.id as category_id',
            'categories.name as category_name',
            'products.name as product_name',
            DB::raw('SUM(order_products.total_price) as amount'),
            DB::raw('SUM(order_products.quantity) as total_quantity'),
        ])
        ->from('categories')
        ->leftJoin('products', 'categories.id', '=', 'products.category_id')
        ->leftJoin('order_products', 'products.id', '=', 'order_products.product_id')
        ->leftJoin('orders', 'order_products.order_id', '=', 'orders.id')
        ->leftJoin('customers', 'orders.customer_id', '=', 'customers.id')
        ->where('categories.id', $category_id)
        ->whereNull('orders.deleted_at')
        ->whereNull('order_products.deleted_at');

        if ($address_id) {
            $query->where('customers.address_id', $address_id);
        }

        if ($from_date !== null && $from_date != 'null') {
            $fromDate = Carbon::parse($from_date)->startOfDay();
            $query->where('order_products.created_at', '>=', $fromDate);
        }

        if ($to_date !== null && $to_date != 'null') {
            $toDate = Carbon::parse($to_date)->endOfDay();
            $query->where('order_products.created_at', '<=', $toDate);
        }

        $query->groupBy('categories.id', 'products.id')
            ->havingRaw('amount > 0')
            ->orderByDesc('order_products.id');

        //dd($query->toSql());

        return $query;
    }
}
