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
            <h2 style="margin: 0;color: #2a2a33;font-size: 20px;font-weight: bold; text-align:center;"><strong>@lang('quickadmin.report-management.fields.category_report')</strong></h2>

        </header>
    </div>
    <main class="main" style="max-width: 100%;margin: 0 auto;padding: 40px 0;padding-top: 20px;">
        <table cellpadding="0" cellspacing="0" width="100%" style="color: #000;font-size: 16px;">
            <thead>
                <tr>
                    <th style="padding: 10px;border: 1px solid #000;border-right: none;" align="left">@lang('quickadmin.qa_sn')</th>
                    <th style="padding: 10px;border: 1px solid #000;border-right: none;" align="center">@lang('quickadmin.qa_category_name')</th>
                    <th style="padding: 10px;border: 1px solid #000;border-right: none;" align="center">@lang('quickadmin.reports.total_product')</th>
                    <th style="padding: 10px;border: 1px solid #000;border-right: none;" align="center">@lang('quickadmin.reports.sale_amount')</th>
                    <th style="padding: 10px;border: 1px solid #000;" align="center">@lang('quickadmin.reports.sale_percent')</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($catdata as $key => $data)
                <tr>
                    <td style="padding: 10px;border: 1px solid #000;border-right: none;border-top: none;" align="left">{{ $key + 1 }}</td>
                    <td style="padding: 10px;border: 1px solid #000;border-right: none;border-top: none;" align="center">{{  $data->name ?? '' }}</td>
                    <td style="padding: 10px;border: 1px solid #000;border-right: none;border-top: none;" align="center">{{  $data->totalsoldproduct ?? '' }}</td>
                    <td style="padding: 10px;border: 1px solid #000;border-right: none;border-top: none;" align="center">{{ $data->amount ?? ''}}</td>
                    <td style="padding: 10px;border: 1px solid #000;border-top: none;" align="center">{{ CategoryAmountPercent($data->amount ,$totalAmount) ?? '0'}}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="padding: 10px;border: 1px solid #000;border-top: none;" align="center">No Record Found!</td>
                </tr>
                @endforelse
                <tr>
                    <th colspan="4" style="padding: 10px;border: 1px solid #000;border-right: none;border-top: none;" align="right">@lang('quickadmin.order.fields.grand_total')</th>
                    <th style="padding: 10px;border: 1px solid #000;border-top: none;" align="center">{{ $totalAmount }}</th>
                </tr>
            </tbody>
        </table>
    </main>
@endsection
