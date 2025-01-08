<?php

namespace App\Exports;

use App\Models\Address;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AddressExport implements FromCollection, WithHeadings
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
        $query = Address::query();

        // If $address_id is not null, filter by city
        if ($this->address_id !== null) {
            $query->where('id', $this->address_id);
        }

        $addresses = $query->orderBy('address','asc')->get();

        return $addresses->map(function ($address, $key) {
            return [
                'Sn.' => $key + 1,
                'City' => $address->address,
                'No. of Party' => $address->customers->count() ?? 0,
                // 'Created At' => $address->created_at->format('d-m-Y'),
            ];
        });
    }

    public function headings(): array
    {
        return ["Sn.", "City" , "No. of Party"];
    }

}
