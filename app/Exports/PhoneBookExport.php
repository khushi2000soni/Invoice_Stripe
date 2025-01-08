<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PhoneBookExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $address_id;

    public function __construct($address_id)
    {
        $this->address_id = $address_id;
    }

    public function collection()
    {

        $query = Customer::query();
        if ($this->address_id !== null) {
            $query->where('address_id', $this->address_id);
        }

        $customers = $query->orderBy('name','asc')->get();
        return $customers->map(function ($customer, $key) {
            return [
                trans('quickadmin.qa_sn') => $key + 1,
                trans('quickadmin.customers.fields.name') => $customer->name ?? '',
                trans('quickadmin.customers.fields.guardian_name') => $customer->guardian_name ?? '',
                trans('quickadmin.customers.fields.ph_num') => $customer->phone ?? '',
                trans('quickadmin.customers.fields.phone2') => $customer->phone2 ?? '',
                trans('quickadmin.customers.fields.address') => $customer->address->address ?? '',
            ];
        });
    }

    public function headings(): array
    {
        return [trans('quickadmin.qa_sn'),trans('quickadmin.customers.fields.name') , trans('quickadmin.customers.fields.guardian_name') , trans('quickadmin.customers.fields.ph_num'), trans('quickadmin.customers.fields.phone2'), trans('quickadmin.customers.fields.address')];
    }


}

