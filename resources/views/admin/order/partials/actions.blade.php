@can('invoice_print')
{{-- <button class="btn btn-icon btn-info print-order-btn p-1 px-2 print-button" data-href="{{route('orders.print', $order->id)}}" title="@lang('quickadmin.qa_print')" ><x-svg-icon icon="invoice-print" /></button> --}}
<a class="btn btn-icon btn-info print-invoice-btn p-1 px-2" data-href="{{route('orders.orderdetailprint', $order->id)}}" title="@lang('quickadmin.qa_print')" ><x-svg-icon icon="invoice-print" /></a>
@endcan

@can('invoice_share')
<a role="button" class="btn btn-icon share-order-btn p-1 px-2 mx-1" data-toggle="modal" data-target="#ShareInvoiceModal{{$order->id}}" title="@lang('quickadmin.qa_share')"><x-svg-icon icon="share" /> </a>
<div class="modal fade px-3 share-modal" id="ShareInvoiceModal{{$order->id}}" tabindex="-1" role="dialog" aria-labelledby="ShareInvoiceModaltitlte" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ShareInvoiceModaltitlte">@lang('quickadmin.order.share_invoice')</h5>
                <button class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body ">
                    <div class="shareblock">
                        <div class="btnbk">
                            @php
                                $recipientMail = $order->customer->email ?? "";
                            @endphp

                            <a href="javascript:void(0);" data-order-id="{{ $order->id }}" data-href="{{route('orders.share-email', $order->id)}}" class="btn btn-danger dangerBtn text-white btn-block share-email-btn">
                                <x-svg-icon icon="mail" />
                            </a>

                        </div>
                        <div class="btnbk">
                            @php
                               $recipientNumber = $order->customer->phone ?? ""; // Replace with the actual recipient's phone number
                            @endphp
                            <a href="javascript:void(0);" class="btn btn-success btn-block share-whatsapp-btn" data-order-id="{{ $order->id }}" data-recipient-number="{{ $recipientNumber }}" data-recipient-name="{{ $order->customer->name ?? "" }}" data-href="{{route('orders.generate-pdf', $order->id)}}">
                                <x-svg-icon icon="whatsapp" />
                            </a>
                        </div>
                    </div>
            </div>
        </div>
    </div>
</div>
@endcan


@can('invoice_access')
<div class="dropdown d-inline">
    <button class="btn dropdown-toggle px-2"
        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="@lang('quickadmin.qa_view')">
        <i class="fas fa-align-justify"></i>
    </button>
    <div class="dropdown-menu droplist">
        @can('invoice_download')
        <a class="dropdown-item has-icon" href="{{route('orders.generate-pdf', $order->id)}}" title="@lang('quickadmin.qa_download')"><x-svg-icon icon="download" /> @lang('quickadmin.qa_download')</a>
        @endcan
        @can('invoice_edit')
        <a class="dropdown-item has-icon" href="{{route('orders.edit', $order->id)}}" title="@lang('quickadmin.qa_edit')"><x-svg-icon icon="edit" />  @lang('quickadmin.qa_edit')</a>
        @endcan
        @can('invoice_show')
        <a class="dropdown-item has-icon" href="{{route('orders.print-pdf', $order->id)}}" title="@lang('quickadmin.qa_view')" target="_blank"><x-svg-icon icon="view" /> @lang('quickadmin.qa_view')</a>
        @endcan
        @can('invoice_delete')
        <form action="{{route('orders.destroy', $order->id)}}" method="POST" class="deleteForm m-1">
            <button class="dropdown-item has-icon record_delete_btn deleteBtn  d-inline-flex align-items-center" type="submit"><x-svg-icon icon="delete" /> @lang('quickadmin.qa_delete')</button>
        </form>
        @endcan
    </div>
</div>
@endcan

