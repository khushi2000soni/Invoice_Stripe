<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Category extends Model
{
    use HasFactory;
    public $table = 'categories';
    protected $fillable = ['name'];
    public $timestamps = true;
    protected $appends = [
        'total_product'
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function getTotalProductAttribute()
    {
        return $this->products()->count();
    }

    public static function getFilteredCategories($request)
    {
        // $address_id = $request->address_id ?? null;
        $from_date = $request->from_date ?? null;
        $to_date = $request->to_date ?? null;

        $query = self::query();
        $query->select([
            'categories.id as category_id',
            'categories.name as name',
            DB::raw('SUM(order_products.total_price) as amount'),
            DB::raw('SUM(order_products.quantity) as totalsoldproduct'),
        ])
        ->leftJoin('products', 'categories.id', '=', 'products.category_id')
        ->leftJoin('order_products', 'products.id', '=', 'order_products.product_id')
        ->leftJoin('orders', 'order_products.order_id', '=', 'orders.id')
        ->leftJoin('customers', 'orders.customer_id', '=', 'customers.id')
        ->whereNull('orders.deleted_at')
        ->whereNull('order_products.deleted_at')
        ->groupBy('categories.id')
        ->havingRaw('amount > 0')
        ->orderByDesc(DB::raw('SUM(order_products.total_price)'));

        // if ($address_id) {
        //     $query->where('customers.address_id', $address_id);
        // }

        if ($from_date !== null && $from_date != 'null') {
            $fromDate = Carbon::parse($from_date)->startOfDay();
            $query->where('order_products.created_at', '>=', $fromDate);
        }
        if ($to_date !== null && $to_date != 'null') {
            $toDate = Carbon::parse($to_date)->endOfDay();
            $query->where('order_products.created_at', '<=', $toDate);
        }

        //dd($query->toSql());

        return $query;
    }
}
