<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th colspan="4" class="text-center">{{ $address->address }} @lang('quickadmin.customer-management.fields.list') <span class="float-right"><button class="btn btn-link close-accordion" data-address-id="{{ $address->id }}"><i class="fas fa-times"></i></button></span></th>
            </tr>
            <tr>
                <th>@lang('quickadmin.qa_sn')</th>
                <th class="text-center">@lang('quickadmin.customers.fields.name')</th>
                <th class="text-center">@lang('quickadmin.customers.fields.guardian_name')</th>
                <th class="text-center">@lang('quickadmin.customers.fields.ph_num')</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($customers as $key => $customer)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td class="text-center">{{ $customer->name }}</td>
                    <td class="text-center">{{ $customer->guardian_name }}</td>
                    <td class="text-center">{{ $customer->phone }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">No Record Found!</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
