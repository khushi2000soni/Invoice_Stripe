<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class InvoiceExport implements FromCollection , WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $customer_id;
    protected $from_date;
    protected $to_date;

    public function __construct($customer_id,$from_date,$to_date)
    {
        $this->customer_id = $customer_id;
        $this->from_date = $from_date;
        $this->to_date = $to_date;
    }


    public function collection()
    {
            $query = Order::query();
            if ($this->customer_id !== null && $this->customer_id != 'null') {
                $query->where('customer_id', $this->customer_id);
            }
            if ($this->from_date !== null && $this->from_date != 'null') {
                $query->whereDate('invoice_date','>=', $this->from_date);
            }
            if ($this->to_date !== null && $this->to_date != 'null') {
                $query->whereDate('invoice_date','<=', $this->to_date);
            }

            if (!(auth()->user()->hasRole(config('app.roleid.super_admin')))) {
                $days = getSetting('invoice_allow_day_admin_accountant');
                $query = $query->whereDate('invoice_date', '>=', now()->subDays($days));
            }

            $allorders = $query->orderBy('id','desc')->get();
            return $allorders->map(function ($order, $key) {
            return [
                trans('quickadmin.qa_sn')  => $key + 1,
                trans('quickadmin.order.fields.invoice_number') => $order->invoice_number ?? '' ,
                trans('quickadmin.order.fields.customer_name') => $order->customer ? $order->customer->full_name : '',
                trans('quickadmin.order.fields.total_price') => $order->grand_total ?? '',
                trans('quickadmin.order.fields.invoice_date') =>$order->created_at->format('d-m-Y') ?? '',
            ];
        });
    }

    public function headings(): array
    {
        return [trans('quickadmin.qa_sn'),trans('quickadmin.order.fields.invoice_number') , trans('quickadmin.order.fields.customer_name'), trans('quickadmin.order.fields.total_price'), trans('quickadmin.order.fields.invoice_date')];
    }
}
