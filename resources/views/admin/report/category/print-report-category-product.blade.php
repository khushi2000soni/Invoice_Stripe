@extends('layouts.print-view.print-layout')
@section('title')@lang('quickadmin.order-management.fields.list')@endsection

@section('custom_css')
<!-- <style>
    @media print {
    @page {
        size: A5;
        margin:0 auto;
    }

    html, body {
        margin: 0;
        padding: 0;
    }

    body{
        display: flex;
        align-items: center;
        justify-content: start;
        flex-direction:column;
    }

    html
    {
        zoom:75%;
    }

    table{
        width: 100%;
    }
}
</style> -->
@endsection

@section('content')
    <div class="page-header">
        <header style="padding: 1px 0; max-width: 100%; margin: 0 auto;">
            <h2 style="margin: 0;color: #2a2a33;font-size: 20px;font-weight: bold; text-align:center;"><strong>{{ $category_name}} @lang('quickadmin.report-management.fields.category_report')</strong></h2>

        </header>
    </div>
    <main class="main" style="max-width: 100%;margin: 0 auto;padding: 40px 0;padding-top: 10px;">
        <table cellpadding="0" cellspacing="0" width="100%" style="color: #000;font-size: 16px;">
            <tbody>
                <tr>
                    <td colspan="4" style="padding-bottom: 5px"><div class="" style="color: #2a2a33;font-size: 16px; text-align:left;"><strong> @lang('quickadmin.reports.total_amount') : {{ $totalAmount }} </strong></div></td>
                </tr>
                @if ($duration)
                <tr>
                    <td colspan="4" style="padding-bottom: 5px"><div class="" style="color: #2a2a33;font-size: 16px; text-align:left;"><strong> @lang('quickadmin.order.fields.duration') : {{ $duration }} </strong></div></td>
                </tr>
                @endif
                <tr>
                    <td colspan="4" style="padding-bottom: 10px"><div class="" style="color: #2a2a33;font-size: 16px; text-align:left;"><strong>@lang('quickadmin.reports.sale_percent') : {{ $category_percent }} </strong></div></td>
                </tr>
                <tr>
                    <th style="padding: 10px;border: 1px solid #000;border-right: none;" align="center">@lang('quickadmin.qa_sn')</th>
                    <th style="padding: 10px;border: 1px solid #000;border-right: none;" align="center">@lang('quickadmin.qa_product_name')</th>
                    <th style="padding: 10px;border: 1px solid #000;border-right: none;" align="center">@lang('quickadmin.reports.total_quantity')</th>
                    <th style="padding: 10px;border: 1px solid #000;border-right: none;" align="center">@lang('quickadmin.reports.sale_amount')</th>
                    <th style="padding: 10px;border: 1px solid #000;" align="center">@lang('quickadmin.reports.sale_percent')</th>
                </tr>

                @forelse ($alldata as $key => $data)
                <tr>
                    <td style="padding: 10px;border: 1px solid #000;border-right: none;border-top: none;" align="left">{{ $key + 1 }}</td>
                    <td style="padding: 10px;border: 1px solid #000;border-right: none;border-top: none;" align="center">{{  $data->product_name ?? '' }}</td>
                    <td style="padding: 10px;border: 1px solid #000;border-right: none;border-top: none;" align="center">{{ $data->total_quantity ?? ''}}</td>
                    <td style="padding: 10px;border: 1px solid #000;border-right: none;border-top: none;" align="center">{{ $data->amount ?? ''}}</td>
                    <td style="padding: 10px;border: 1px solid #000;border-top: none;" align="center">{{ CategoryAmountPercent($data->amount ,$totalAmount) ?? '0'}}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" style="padding: 10px;border: 1px solid #000;border-top: none;" align="center">No Record Found!</td>
                </tr>
                @endforelse

            </tbody>
        </table>
    </main>
@endsection
