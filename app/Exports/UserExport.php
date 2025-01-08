<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UserExport implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {

        $query = User::query();
        $staff = $query->orderBy('name','asc')->get();

        return $staff->map(function ($staff, $key) {
            $role = $staff->roles->first();
            return [
                trans('quickadmin.qa_sn') => $key + 1,
                trans('quickadmin.users.fields.name') => $staff->name ?? '',
                trans('quickadmin.users.fields.role') => $role ? $role->name : '',
                trans('quickadmin.users.fields.usernameid') => $staff->username  ?? "",
                trans('quickadmin.users.fields.email') => $staff->email ?? '',
                trans('quickadmin.users.fields.phone') => $staff->phone ?? '',
            ];
        });

    }

    public function headings(): array
    {
        return [trans('quickadmin.qa_sn'),trans('quickadmin.users.fields.name') , trans('quickadmin.users.fields.role') , trans('quickadmin.users.fields.usernameid'), trans('quickadmin.users.fields.email'), trans('quickadmin.users.fields.phone')];
    }
}
