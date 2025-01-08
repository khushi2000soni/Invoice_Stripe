
@extends('layouts.app')
@section('title')@lang('quickadmin.order-management.title')@endsection
@section('customCss')
<meta name="csrf-token" content="{{ csrf_token() }}" >
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
<link rel="stylesheet" href="{{ asset('admintheme/assets/css/printView-datatable.css')}}">
<style>
    .dropdown-toggle::after {
    display: none;
    }

    .custom-select2 select{
        width: 200px;
        z-index: 1;
        position: relative;
    }
    .custom-select2 .form-control-inner{
        position: relative;
    }
    .custom-select2 .form-control-inner label{
        position: absolute;
        left: 10px;
        top: -10px;
        background-color: #fff;
        padding: 0 5px;
        z-index: 1;
        font-weight: 600;
        font-size: 14px;
    }
    .select2-results{
        padding-top: 48px;
        position: relative;
    }
    .select2-link2{
        position: absolute;
        top: 6px;
        left: 5px;
        width: 100%;
    }
    .select2-container--default .select2-selection--single,
    .select2-container--default .select2-selection--single .select2-selection__arrow{
        height: 40px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered{
        line-height: 41px;
    }
    .select2-search--dropdown .select2-search__field{
        padding: 10px;
        font-size: 15px;
    }
    .select2-search--dropdown .select2-search__field:focus{
        outline: none;
    }
    .select2-link2 .btns {
        color: #3584a5;
        background-color: transparent;
        border: none;
        font-size: 14px;
        padding: 7px 15px;
        cursor: pointer;
        border: 1px solid #3584a5;
        border-radius: 60px;
    }
    #centerModal, #editModal{
        z-index: 99999;
    }
    #centerModal::before, #editModal::before{
        display: none;
    }

    .modal-open .modal-backdrop.show{
        display: block !important;
        z-index: 9999;
    }
    .datapikergroup .lhs .form-control  {
        border-top-right-radius: 0px;
        border-bottom-right-radius: 0px;
    }
    .datapikergroup .rhs .form-control  {
        border-top-left-radius: 0px;
        border-bottom-left-radius: 0px;
    }
     .cart_filter_box {
        border-bottom: 1px solid #e5e9f2;
        padding-bottom: 4px;
    }
</style>
@endsection

@section('main-content')

<section class="section roles" style="z-index: unset">
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card pt-2">
                    <div class="card-body">
                        @can('invoice_filter')
                        <form id="invoice-filter-form">
                            <div class="row align-items-center pb-3 mb-4 cart_filter_box">
                                <div class="col-md-6 col-lg-6 col-xl-3 pr-xl-0">
                                    <div class="custom-select2 fullselect2">
                                        <div class="form-control-inner">
                                            <label for="customer_id">@lang('quickadmin.order.fields.customer_name')</label>
                                            <select class="form-control filter-customer-select @error('customer_id') is-invalid @enderror" name="customer_id" id="customer_id" tabindex="0">
                                                <option value="">@lang('quickadmin.order.fields.select_customer')</option>
                                                @foreach($customers as $customer)
                                                <option value="{{ $customer->id }}">{{ $customer->full_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                @can('invoice_date_filter')
                                <div class="col-md-6 col-lg-6 col-xl-3 pr-xl-0">
                                    <div class="mx-0 datapikergroup custom-select2">
                                        <div class="form-control-inner">
                                            <label for="reportrange">Select Date</label>
                                            <div id="reportrange" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                                                <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
                                                <span></span> <b class="caret"></b>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endcan
                                <div class="col-sm-6 col-md-6 col-lg-6 col-xl-2">
                                    <div class="form-group mb-0 d-flex">
                                        <button type="submit" class="btn btn-primary mr-1 col" id="apply-filter">@lang('quickadmin.qa_submit')</button>
                                        <button type="reset" class="btn btn-primary mr-1 col" id="reset-filter">@lang('quickadmin.qa_reset')</button>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-6 col-lg-6 col-xl-4 text-end">
                                    <div class="form-group mb-0 d-flex justify-content-md-end filegroip">
                                        @if ($type != 'deleted')
                                            @can('invoice_create')
                                            <div class="col-auto px-md-1 pr-1">
                                                <a href="{{ route('orders.create')}}" class="btn btn-outline-dark invoiceicon add_invoice_btn circlebtn" title="@lang('quickadmin.dashboard.add_invoice')"><x-svg-icon icon="add-order" /> </a>
                                            </div>
                                            @endcan

                                            @can('invoice_print')
                                            <div class="col-auto px-md-1 pr-1">
                                                <a href="{{ route('orders.allprint') }}" class="btn printbtn h-10 col circlebtn"  id="invoice-print" title="@lang('quickadmin.qa_print')"> <x-svg-icon icon="print" /></a>
                                            </div>
                                            @endcan

                                            @can('invoice_export')
                                            <div class="col-auto px-md-1 pr-1">
                                                <a href="{{ route('orders.allexport') }}" class="btn excelbtn h-10 col circlebtn"  id="invoice-excel" title="@lang('quickadmin.qa_excel')"><x-svg-icon icon="excel"/></a>
                                            </div>
                                            @endcan

                                            @can('invoice_recycle_access')
                                            <div class="col-auto px-md-1 pr-1">
                                                <a class="btn btn-outline-danger recycleicon col circlebtn" href="{{ route('orders.getTypeOrder',['type'=>'deleted'])}}" id="trashed-data" title="@lang('quickadmin.order.recycle')"><x-svg-icon icon="recycle" /></a>
                                            </div>
                                            @endcan
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </form>
                        @endcan


                        <div class="table-responsive fixed_Search">
                            {{$dataTable->table(['class' => 'table dt-responsive invoicdatatable dropdownBtnTable order_table_custom', 'style' => 'width:100%;','id'=>'dataaTable'])}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="popup_render_div"></div>
</section>
@endsection


@section('customJS')
{!! $dataTable->scripts() !!}
    <script src="{{ asset('admintheme/assets/bundles/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('admintheme/assets/bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('admintheme/assets/bundles/jquery-ui/jquery-ui.min.js') }}"></script>
    <!-- Page Specific JS File -->
    <script src="{{ asset('admintheme/assets/js/page/datatables.js') }}"></script>
    <script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>
        // Enable pusher logging - don't include this in production
        Pusher.logToConsole = true;
        var pusherAppKey = "{{ env('PUSHER_APP_KEY') }}";
        var pusher = new Pusher(pusherAppKey, {
        cluster: 'ap2'
        });
        var channel = pusher.subscribe('invoices');
        channel.bind('invoice-updated', function(data) {
        //alert(JSON.stringify(data));
        $('#dataaTable').DataTable().ajax.reload();
            var alertType = 'success';
            var message = 'New invoice added or updated!'
            var title = "{{ trans('quickadmin.order.invoice') }}";
            // Show a notification
            showToaster(title,alertType,message);
        });
        console.log();
    </script>
    <script type="text/javascript">
        $(function() {
            var date = '{{ config("app.start_date")}}';
            var start = moment(date, 'YYYY-MM-DD'); /*.moment().startOf('month')*/
            var end = moment();
            function cb(start, end) {
                $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            }

            $('#reportrange').daterangepicker({
                startDate: start,
                endDate: end,
                ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            }, cb);

            cb(start, end);

        });
        var defaultStartDate = moment('{{ config("app.start_date") }}', 'YYYY-MM-DD');
        var defaultEndDate = moment();

    </script>

<script>
    $(document).ready(function () {
        var dataTable = $('#dataaTable').DataTable();
        $('#invoice-print').printPage();

        $(document).on('click','.print-invoice-btn', function(e){
            e.preventDefault();
            var actionurl = $(this).data('href');
            $.ajax({
                url: actionurl,
                method: 'GET',
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json', // Specify JSON as the expected data type
                success: function (response) {
                    if (response.status === true && response.pdf) {
                             // Create the iframe dynamically
                            var printFrame = $('<iframe>', {
                                name: 'myiframe',
                                class: 'printFrame',
                                width: '100%',
                                height: '100%',
                            });
                            // Append the iframe to the body
                            $('body').append(printFrame);
                            $('body .printFrame').css('display', 'none');
                            printFrame.contents().find('body')
                            .append(response.pdf);
                            printFrame.focus();
                            printFrame.get(0).contentWindow.print();
                            // Remove the iframe after a delay
                            // setTimeout(() => {
                            // printFrame.remove();
                            // }, 1000);
                    } else {
                        console.error('Error fetching PDF:', response.error);
                    }
                },
                error: function (xhr, status, error) {
                console.error('Error fetching PDF:', error);
                }
            });
        });

        // Page show from top when page changes
        $(document).on('draw.dt','#dataaTable', function (e) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: 0
            }, 'fast');
        });

        $(document).on('shown.bs.modal','.share-modal', function (e) {
            e.preventDefault();
            // Remove the modal backdrop
            $('.modal-backdrop').remove();
        });

        $(".filter-customer-select").select2({
        }).on('select2:open', function () {
            let a = $(this).data('select2');
            if (!$('.select2-link').length) {
                a.$results.parents('.select2-results')
                    .append('<div class="select2-link2"><button class="btns addNewCustomerBtn get-customer"><i class="fa fa-plus-circle"></i> Add New</button></div>');
            }
        });

        $(document).on('click', '.select2-container .get-customer', function (e) {
            e.preventDefault();
            var gethis = $(this);
            var hrefUrl = "{{ route('customers.create') }}";
            $('.modal-backdrop').remove();
            $.ajax({
                type: 'get',
                url: hrefUrl,
                dataType: 'json',
                success: function (response) {
                    if(response.success) {
                        //$("body").addClass("modal-open");
                        $('.popup_render_div').html(response.htmlView);
                        // Show the first modal
                        $('.popup_render_div #centerModal').modal('show');
                        // Initialize select2 for the first modal
                        $(".js-example-basic-single").select2({
                        dropdownParent: $('.popup_render_div #centerModal') // Set the dropdown parent to the modal
                        }).on('select2:open', function () {
                            let a = $(this).data('select2');
                            if (!$('.select2-link').length) {
                                a.$results.parents('.select2-results')
                                .append('<div class="select2-link2"><button class="btns get-city close-select2"><i class="fa fa-plus-circle"></i> Add New</button></div>');
                            }
                        });
                    }
                }
            });

            $('#invoice-filter-form #customer_id').select2('close');
        });

        $(document).on('click', '.select2-container .get-city', function (e) {
            e.preventDefault();
            var gethis = $(this);
            var hrefUrl = "{{ route('address.create') }}";
            $('.modal-backdrop').remove();
            // Fetch data and populate the second modal
            $.ajax({
                type: 'get',
                url: hrefUrl,
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        $('.addressmodalbody').remove();
                        $('.popup_render_div #address_id').select2('close');
                        $('.popup_render_div').after('<div class="addressmodalbody" style="display: block;"></div>');
                        $('.addressmodalbody').html(response.htmlView);
                        $('.addressmodalbody #centerModal').modal('show');
                        $('.addressmodalbody #centerModal').attr('style', 'z-index: 100000');
                    }
                }
            });
        });

        // Code Add New Customer

        $(document).on('submit', '#AddForm', function (e) {
            e.preventDefault();
            $("#AddForm button[type=submit]").prop('disabled',true);
            $(".error").remove();
            $(".is-invalid").removeClass('is-invalid');
            var formData = $(this).serialize();
            var formAction = $(this).attr('action');
            $.ajax({
                url: formAction,
                type: 'POST',
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: formData,
                success: function (response) {
                        $('#centerModal').modal('hide');
                        var alertType = response['alert-type'];
                        var message = response['message'];
                        var title = response['title'];
                        var newOption = new Option(response.selectdata.name, response.selectdata.id, true, true);
                        $('#customer_id').append(newOption).trigger('change');
                        showToaster(title,alertType,message);
                        $('#AddForm')[0].reset();
                    $("#AddForm button[type=submit]").prop('disabled',false);
                },
                error: function (xhr) {
                    var errors= xhr.responseJSON.errors;
                    for (const elementId in errors) {
                        $("#"+elementId).addClass('is-invalid');
                        var errorHtml = '<div><span class="error text-danger">'+errors[elementId]+'</span></div>';
                        $(errorHtml).insertAfter($("#"+elementId).parent());
                    }
                    $("#AddForm button[type=submit]").prop('disabled',false);
                }
            });
        });

        // Add Address Instatntly

        $(document).on('submit', '#AddaddressForm', function (e) {
            e.preventDefault();
            $("#AddaddressForm button[type=submit]").prop('disabled',true);
            $(".error").remove();
            $(".is-invalid").removeClass('is-invalid');
            var form = $(this);
            var formData = $(this).serialize();
            var formAction = $(this).attr('action');
            $.ajax({
                url: formAction,
                type: 'POST',
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: formData,
                success: function (response) {

                        form.closest('#centerModal').modal('hide');
                        var newOption = new Option(response.address.address, response.address.id, true, true);
                        $('.popup_render_div #centerModal #address_id').append(newOption).trigger('change');
                        var alertType = response['alert-type'];
                        var message = response['message'];
                        var title = "{{ trans('quickadmin.address.address') }}";
                        showToaster(title,alertType,message);
                        $('#AddaddressForm')[0].reset();
                        //location.reload();
                        //DataaTable.ajax.reload();
                        $("#AddaddressForm button[type=submit]").prop('disabled',false);
                },
                error: function (xhr) {
                    var errors= xhr.responseJSON.errors;
                    //console.log(xhr.responseJSON);
                    for (const elementId in errors) {
                        $("#"+elementId).addClass('is-invalid');
                        var errorHtml = '<div><span class="error text-danger">'+errors[elementId]+'</span></';
                        $(errorHtml).insertAfter($("#"+elementId).parent());
                    }
                    $("#AddaddressForm button[type=submit]").prop('disabled',false);
                }
            });
        });


        $("body").on("click", ".edit-invoice-customer-btn", function () {
                var hrefUrl = $(this).attr('data-href');
                $('.modal-backdrop').remove();
                $.ajax({
                    type: 'get',
                    url: hrefUrl,
                    dataType: 'json',
                    success: function (response) {
                        //$('#preloader').css('display', 'none');
                        if(response.success) {
                            $('.popup_render_div').html(response.htmlView);
                            $('#editPhoneModal').modal('show');
                            setTimeout(() => {
                                $('.modal-backdrop').not(':first').remove();
                            }, 300);
                        }
                    }
                });
        });


        $(document).on('click', '.print-button', function(e) {
            e.preventDefault();
            var actionurl = $(this).data('href');
            $.ajax({
                url: actionurl,
                method: 'GET',
                success: function(response) {
                    var basestringpdf = response.pdf;
                    var objbuilder= '';
                    objbuilder += ('<object width = "100%" height="100%" data="data:application/pdf;base64,');
                    objbuilder += (basestringpdf);
                    objbuilder += ('" type="application/pdf" class="internal">');
                    objbuilder += ('<embed type="application/pdf" src="data:application/pdf;base64,' + basestringpdf + '" />');
                    objbuilder += ('</object>');

                    var mywindow = window.open('', 'PRINT', 'height=600,width=800');
                    mywindow.document.write('<html><head><title>PRINT PREVIEW</title>');
                    mywindow.document.write('</head><body >');
                    // mywindow.document.write('<embed type="application/pdf" width="100%" height="100%" src="data:application/pdf;base64,' + decodedPdfContent + '" />');
                    mywindow.document.write(objbuilder);
                    mywindow.document.write('</body></html>');
                    layer = $(mywindow.document);
                    // mywindow.print();
                    // mywindow.close();
                    },
                    error: function(error) {
                        console.error('Error fetching invoice:', error);
                    }
            });
        });

        $(document).on('click','.share-whatsapp-btn',function(e){
            e.preventDefault();
            var recipientNumber = $(this).data('recipient-number');
            var recipientName = $(this).data('recipient-name');
            var orderId = $(this).data('order-id');
            var pdfDownloadUrl = $(this).data('href');
            var pdfLink = document.createElement('a');
            var whatsappMessage="{{ getSetting('share_invoice_whatsapp_message')}}";
            pdfLink.href = pdfDownloadUrl;
            pdfLink.download = 'invoice.pdf';
            pdfLink.style.display = 'none';
            document.body.appendChild(pdfLink);
            pdfLink.click();
            document.body.removeChild(pdfLink);
            var whatsappUrl = 'https://api.whatsapp.com/send?phone=' + recipientNumber + '&text='+whatsappMessage;
            window.open(whatsappUrl, '_blank');
        });

        $(document).on('click','.share-email-btn',function(e){
            e.preventDefault();
            var orderId = $(this).data('order-id');
            var hrefurl = $(this).data('href');
            $('.modal-backdrop').remove();
            $.ajax({
                type: 'GET',
                url: hrefurl,
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    $('.popup_render_div').html(response.htmlView);
                    $('.popup_render_div #shareEmailModal').modal('show');
                    $('.popup_render_div #shareEmailModal').attr('style', 'z-index: 100000');

                }
            });
        });

        $(document).on('submit','#shareEmailForm',function(e){
            e.preventDefault();
            $("#shareEmailForm button[type=submit]").prop('disabled',true);
            $(".error").remove();
            $(".is-invalid").removeClass('is-invalid');
            var formData = $(this).serialize();
            var formAction = $(this).attr('action');
            $.ajax({
                url: formAction,
                type: 'POST',
                data: formData,
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                        var alertType = response['alert-type'];
                        var message = response['message'];
                        var title = "{{ trans('quickadmin.order.invoice') }}";
                        showToaster(title,alertType,message);
                        $('#shareEmailModal').modal('hide');
                },
                error: function (xhr) {
                    var errors= xhr.responseJSON.errors;
                    console.log(xhr.responseJSON);

                    for (const elementId in errors) {
                        $("#shareEmailForm #"+elementId).addClass('is-invalid');
                        var errorHtml = '<div><span class="error text-danger">'+errors[elementId]+'</span></';
                        $(errorHtml).insertAfter($("#shareEmailForm #"+elementId).parent());
                    }
                    $("#shareEmailForm button[type=submit]").prop('disabled',false);
                    // var errorMessage = xhr.responseJSON.message;
                    // swal("{{ trans('quickadmin.order.invoice') }}", errorMessage, 'error');
                }
            });
        });

        $(document).on('submit', '.deleteForm', function(e) {
            e.preventDefault();
            var formAction = $(this).attr('action');
            swal({
                title: "{{ trans('messages.deletetitle') }}",
                text: "{{ trans('messages.areYouSure') }}",
                icon: 'warning',
                buttons: {
                confirm: 'Yes, delete it',
                cancel: 'No, cancel',
                },
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                    url: formAction,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        var alertType = response['alert-type'];
                            var message = response['message'];
                            var title = "{{ trans('quickadmin.order.invoice') }}";
                            showToaster(title,alertType,message);
                            dataTable.ajax.reload();
                    },
                    error: function (xhr) {
                        swal("{{ trans('quickadmin.order.invoice') }}", 'Something went wrong', 'error');
                    }
                    });
                }
            });
        });


        $(document).on('submit', '.restoreForm', function(e) {
            e.preventDefault();
            var formAction = $(this).attr('action');
            swal({
            title: "{{ trans('messages.restoretitle') }}",
            text: "{{ trans('messages.areYouSureRestore') }}",
            icon: 'warning',
            buttons: {
            confirm: 'Yes, Restore it',
            cancel: 'No, cancel',
            },
            dangerMode: true,
            }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                url: formAction,
                type: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    var alertType = response['alert-type'];
                        var message = response['message'];
                        var title = "{{ trans('quickadmin.order.invoice') }}";
                        showToaster(title,alertType,message);
                        dataTable.ajax.reload();
                },
                error: function (xhr) {
                    swal("{{ trans('quickadmin.order.invoice') }}", 'Some mistake is there.', 'error');
                }
                });
            }
            });
        });

        $('#reset-filter').on('click', function(e) {
            e.preventDefault();
            $('#invoice-filter-form')[0].reset();

            var select2Element = $('#customer_id');
            select2Element.val(null).trigger('change');

            //Reset the Daterangepicker
            if ($('#reportrange').data('daterangepicker')) {
                var start = defaultStartDate ?? null;
                var end = defaultEndDate ?? null;

                $('#reportrange').data('daterangepicker').setStartDate(start);
                $('#reportrange').data('daterangepicker').setEndDate(end);
                $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            }

            var type = "{{$type}}";

            if(type == 'deleted'){
                var url = "{{ route('orders.getTypeOrder') }}/" + type;
                dataTable.ajax.url(url).load();
            }else{
                dataTable.ajax.url("{{ route('orders.index') }}").load();
            }

            originalExportUrl = "{{ route('orders.allexport') }}";
            originalPrintUrl = "{{ route('orders.allprint') }}";
            $('#invoice-excel').attr('href', originalExportUrl);
            $('#invoice-print').attr('href', originalPrintUrl);
        });

        $('#invoice-filter-form').on('submit', function(e) {

            e.preventDefault();
            // Get the date range picker instance
            var picker = $('#reportrange').data('daterangepicker');
            // Retrieve the selected start and end dates
            if (picker && picker.startDate && picker.endDate) {
                var from_date = picker.startDate.format('YYYY-MM-DD');
                var to_date = picker.endDate.format('YYYY-MM-DD');
            } else {
                // Handle the case where picker or its properties are undefined
                var from_date = null;
                var to_date = null;
            }

            var type = "{{$type}}";
            // Collect filter values (customer, from_date, to_date) from the form
            var customer_id = $('#customer_id').val();
            if(customer_id == undefined){
                customer_id = '';
            }

            if(from_date == undefined || from_date == 'Invalid date'){
                from_date = '';
            }

            if(to_date == undefined || to_date == 'Invalid date'){
                to_date = '';
            }

            var exportUrl = "{{ route('orders.allexport') }}"
            + '?customer_id=' + encodeURIComponent(customer_id)
            + '&from_date=' + encodeURIComponent(from_date)
            + '&to_date=' + encodeURIComponent(to_date);

            var printUrl = "{{ route('orders.allprint') }}"
            + '?customer_id=' + encodeURIComponent(customer_id)
            + '&from_date=' + encodeURIComponent(from_date)
            + '&to_date=' + encodeURIComponent(to_date);

            var params = {
                    customer_id      : customer_id,
                    from_date        : from_date,
                    to_date          : to_date,
            };
            // Apply filters to the DataTable
            if(type == 'deleted'){
                dataTable.ajax.url("{{ route('orders.getTypeOrder') }}/" + type + "?" +$.param(params)).load();
            }else{
                dataTable.ajax.url("{{ route('orders.index') }}?"+$.param(params)).load();
            }

            $('#invoice-excel').attr('href', exportUrl);
            $('#invoice-print').attr('href', printUrl);


        });

        $(document).on('click','.view-delete-orders', function(){
        // $('#preloader').css('display', 'flex');
            var hrefUrl = $(this).attr('data-href');
            $.ajax({
                type: 'get',
                url: hrefUrl,
                dataType: 'json',
                success: function (response) {
                    //$('#preloader').css('display', 'none');
                    if(response.success) {
                        $('.popup_render_div').html(response.htmlView);
                        $('#OrderModal').modal('show');
                    }
                }
            });
        });




    });
</script>

@endsection
