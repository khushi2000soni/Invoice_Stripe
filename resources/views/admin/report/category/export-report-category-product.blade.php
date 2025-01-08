<table cellpadding="0" cellspacing="0" width="100%" style="color: #000; font-size: 16px;border: 2px solid #000;" >
    <tbody style="color: #000; font-size: 16px;border: 2px solid #000;">
        <tr>
            <td colspan="5" style="padding-bottom: 5px; text-align: center;">
                <div style="color: #2a2a33; font-size: 20px; vertical-align: middle;"><strong>{{ $category_name}} @lang('quickadmin.report-management.fields.category_report')</strong></div>
            </td>
        </tr>
        <tr>
            <td colspan="5" style="padding-bottom: 5px; text-align: center;">
                <div style="color: #2a2a33; font-size: 16px; vertical-align: middle;"><strong>@lang('quickadmin.reports.total_amount') : {{ $totalAmount }} </strong></div>
            </td>
        </tr>
        @if ($duration)
            <tr>
                <td colspan="5" style="padding-bottom: 5px; text-align: center;">
                    <div style="color: #2a2a33; font-size: 16px; vertical-align: middle;"><strong>@lang('quickadmin.order.fields.duration') : {{ $duration }} </strong></div>
                </td>
            </tr>
        @endif
        <tr>
            <td colspan="5" style="padding-bottom: 10px; text-align: center;">
                <div style="color: #2a2a33; font-size: 16px; vertical-align: middle;"><strong>@lang('quickadmin.reports.sale_percent') : {{ $category_percent }} </strong></div>
            </td>
        </tr>
        <tr>
            <th style="padding: 10px; border: 1px solid #000; border-right: none; text-align: center; vertical-align: middle;">@lang('quickadmin.qa_sn')</th>
            <th style="padding: 10px; border: 1px solid #000; border-right: none; text-align: center; vertical-align: middle;padding-left: 20px;">@lang('quickadmin.qa_product_name')</th>
            <th style="padding: 10px; border: 1px solid #000; border-right: none; text-align: center; vertical-align: middle;padding-left: 20px;">@lang('quickadmin.reports.total_quantity')</th>
            <th style="padding: 10px; border: 1px solid #000; border-right: none; text-align: center; vertical-align: middle;padding-left: 20px;">@lang('quickadmin.reports.sale_amount')</th>
            <th style="padding: 10px; border: 1px solid #000; text-align: center; vertical-align: middle;padding-left: 20px;">@lang('quickadmin.reports.sale_percent')</th>
        </tr>

        @forelse ($alldata as $key => $data)
            <tr>
                <td style="padding: 10px; border: 1px solid #000; border-right: none; border-top: none; text-align: center; vertical-align: middle;">{{ $key + 1 }}</td>
                <td style="padding: 10px; border: 1px solid #000; border-right: none; border-top: none; text-align: center; vertical-align: middle;">{{  $data->product_name ?? '' }}</td>
                <td style="padding: 10px; border: 1px solid #000; border-right: none; border-top: none; text-align: center; vertical-align: middle;">{{ $data->total_quantity ?? ''}}</td>
                <td style="padding: 10px; border: 1px solid #000; border-right: none; border-top: none; text-align: center; vertical-align: middle;">{{ $data->amount ?? ''}}</td>
                <td style="padding: 10px; border: 1px solid #000; border-top: none; text-align: center; vertical-align: middle;">{{ CategoryAmountPercent($data->amount, $totalAmount) ?? '0'}}</td>
            </tr>
        @empty
            <tr>
                <td colspan="3" style="padding: 10px; border: 1px solid #000; border-top: none; text-align: center; vertical-align: middle;">No Record Found!</td>
            </tr>
        @endforelse
    </tbody>
</table>
