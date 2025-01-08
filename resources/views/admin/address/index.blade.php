@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')
@section('title')@lang('quickadmin.address.title') @endsection
@section('customCss')
<meta name="csrf-token" content="{{ csrf_token() }}" >
<link rel="stylesheet" href="{{ asset('admintheme/assets/css/printView-datatable.css')}}">

<style>
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
        top: -8px;
        background-color: #fff;
        padding: 0 5px;
        z-index: 1;
        font-size: 12px;
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
    #centerModal, #editAddressModal{
        z-index: 99999;
    }
    #centerModal::before, #editAddressModal::before{
        display: none;
    }
    .modal-open .modal-backdrop.show{
        display: block !important;
        z-index: 9999;
    }
    .cart_filter_box{
        border-bottom: 1px solid #e5e9f2;
        padding-bottom: 4px;
    }
    .select2-dropdown{
        z-index: 99999;
    }
</style>

@endsection

@section('main-content')

<section class="section roles" style="z-index: unset">

    <div class="section-body">
          <div class="row">
            <div class="col-12">
              <div class="card pt-2">
                {{-- <div class="card-header d-flex justify-content-between align-items-center">
                  <h4>@lang('quickadmin.address.title')</h4>
                </div> --}}
                <div class="card-body">
                    <div class="row align-items-center pb-3 mb-4 cart_filter_box">
                        <div class="col">
                            <form id="citiwise-filter-form">
                                <div class="row align-items-center">
                                    <div class="col-xl-3 col-lg-4 col-md-5 col-sm-6 pr-0">
                                        <div class="custom-select2 fullselect2">
                                            <div class="form-control-inner">
                                                <label for="address_id">@lang('quickadmin.customers.fields.select_address')</label>
                                                <select class="js-example-basic-single @error('address_id') is-invalid @enderror" name="address_id" id="address_id" >
                                                    <option value="">@lang('quickadmin.customers.fields.select_address')</option>
                                                    @foreach($addresses as $address)
                                                    <option value="{{ $address->id }}">{{ $address->address }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-lg-4 col-sm-6 text-end">
                                        <div class="form-group d-flex  m-0">
                                            <button type="submit" class="btn btn-primary mr-1 col" id="apply-filter">@lang('quickadmin.qa_submit')</button>
                                            <button type="reset" class="btn btn-primary mr-1 col" id="reset-filter">@lang('quickadmin.qa_reset')</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-auto col-12 mt-md-0 mt-3">
                            <div class="row align-items-center">
                                <div class="col-auto px-md-1 pr-1">
                                    @can('address_create')
                                    <button type="button" class="add_address addRecordBtn sm_btn circlebtn" data-toggle="modal" data-target="#centerModal" data-href="{{ route('address.create')}}" title="@lang('quickadmin.qa_add_new')"><x-svg-icon icon="add-address" /></button>
                                    @endcan
                                </div>
                                <div class="col-auto px-1">
                                    @can('address_print')
                                    <a href="{{ route('address.print') }}" class="btn h-10 printbtn col circlebtn"  id="print-button" title="@lang('quickadmin.qa_print')"><x-svg-icon icon="print" /></a>
                                    @endcan
                                </div>
                                <div class="col-auto pl-1">
                                    @can('address_export')
                                    <a href="{{ route('address.export') }}" class="btn h-10 excelbtn col circlebtn"  id="excel-button" title="@lang('quickadmin.qa_excel')"><x-svg-icon icon="excel" /></a>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </div>
                  <div class="table-responsive fixed_Search">
                    {{$dataTable->table(['class' => 'table dt-responsive subBodyTable address_Table', 'style' => 'width:100%;','id'=>'addressTable'])}}
                  </div>
                </div>
              </div>
            </div>
          </div>
    </div>
</section>
  <div class="popup_render_div"></div>
@endsection

@section('customJS')
{!! $dataTable->scripts() !!}
  <script src="{{ asset('admintheme/assets/bundles/datatables/datatables.min.js') }}"></script>
  <script src="{{ asset('admintheme/assets/bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}"></script>
  <!-- Page Specific JS File -->
  <script src="{{ asset('admintheme/assets/js/page/datatables.js') }}"></script>
<script>

$(document).ready(function () {
    var addressDataTable = $('#addressTable').DataTable();


    $('#print-button').printPage();

    // Page show from top when page changes
    $(document).on('draw.dt','#addressTable', function (e) {
        e.preventDefault();
        $('html, body').animate({
            scrollTop: 0
        }, 'fast');
    });

    $(document).on('click' , 'excel-button' , function(e){
        e.preventDefault();
        var iframe = document.createElement('iframe');
        iframe.style.display = 'none';
        iframe.src = '/address-export'; // Replace with the actual URL for your export route
        document.body.appendChild(iframe);
    });


    $(".js-example-basic-single").select2({
    }).on('select2:open', function () {
        let a = $(this).data('select2');
        if (!$('.select2-link').length) {
            a.$results.parents('.select2-results')
                .append('<div class="select2-link2"><button class="btns addNewBtn"><i class="fa fa-plus-circle"></i> Add New</button></div>');
        }
    });

    $(document).on('click','.addNewBtn',function(){
        $(".addRecordBtn").trigger('click');
        $('#address_id').select2('close');
    })

    $(document).on('click', '.select2-container .get-city', function (e) {
        e.preventDefault();
        var gethis = $(this);
        var hrefUrl = "{{ route('address.create') }}";
        $.ajax({
            type: 'get',
            url: hrefUrl,
            dataType: 'json',
            success: function (response) {
                if(response.success) {
                    $('.popup_render_div').html(response.htmlView);
                    $('#centerModal').modal('show');
                }
            }
        });
    });


    $(document).on('click','.addRecordBtn', function(){
       // $('#preloader').css('display', 'flex');
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
                    $('#centerModal').modal('show');
                }
            }
        });
    });

    $("body").on("click", ".edit-address-btn", function () {
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
                        $('#editAddressModal').modal('show');
                    }
                }
            });
    });

    $(document).on('submit', '#AddaddressForm', function (e) {
        e.preventDefault();
        $("#AddaddressForm button[type=submit]").prop('disabled',true);
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
                    if (!$('.js-example-basic-single').data('select2')) {
                        $('.js-example-basic-single').select2();
                    }
                    var newOption = new Option(response.address.address, response.address.id, true, true);
                    $('#address_id').append(newOption).trigger('change');
                    var alertType = response['alert-type'];
                    var message = response['message'];
                    var title = "{{ trans('quickadmin.address.address') }}";
                    showToaster(title,alertType,message);
                    $('#AddaddressForm')[0].reset();
                    //location.reload();
                    addressDataTable.ajax.reload();
                    $("#AddaddressForm button[type=submit]").prop('disabled',false);
            },
            error: function (xhr) {
                var errors= xhr.responseJSON.errors;
                for (const elementId in errors) {
                    $("#"+elementId).addClass('is-invalid');
                    var errorHtml = '<div><span class="error text-danger">'+errors[elementId]+'</span></';
                    $(errorHtml).insertAfter($("#"+elementId).parent());
                }
                $("#AddaddressForm button[type=submit]").prop('disabled',false);
            }
        });
    });


    $(document).on('submit', '#EditaddressForm', function (e) {
        e.preventDefault();

        $("#EditaddressForm button[type=submit]").prop('disabled',true);
        $(".error").remove();
        $(".is-invalid").removeClass('is-invalid');
        var formData = $(this).serialize();
        var formAction = $(this).attr('action');
        $.ajax({
            url: formAction,
            type: 'PUT',
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
            data: formData,
            success: function (response) {
                    $('#editAddressModal').modal('hide');
                    var alertType = response['alert-type'];
                    var message = response['message'];
                    var title = "{{ trans('quickadmin.address.address') }}";
                    showToaster(title,alertType,message);
                    $('#EditaddressForm')[0].reset();
                   // location.reload();
                   addressDataTable.ajax.reload();
                   $("#EditaddressForm button[type=submit]").prop('disabled',false);
            },
            error: function (xhr) {
                var errors= xhr.responseJSON.errors;
                for (const elementId in errors) {
                    $("#EditaddressForm #"+elementId).addClass('is-invalid');
                    var errorHtml = '<div><span class="error text-danger">'+errors[elementId]+'</span></';
                    $(errorHtml).insertAfter($("#EditaddressForm #"+elementId).parent());
                }
                $("#EditaddressForm button[type=submit]").prop('disabled',false);
            }
        });
    });


    $(document).on('submit', '.deleteAddressForm', function(e) {
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
            // If the user confirms, send the DELETE request
            $.ajax({
            url: formAction,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                var alertType = response['alert-type'];
                    var message = response['message'];
                    var title = "{{ trans('quickadmin.address.address') }}";
                    showToaster(title,alertType,message);
                    addressDataTable.ajax.reload();

            },
            error: function (xhr) {
                // Handle error response
                swal('Address', 'Some Went Wrong.', 'error');
            }
            });
        }
        });
    });

    $('#reset-filter').on('click', function(e) {
        e.preventDefault();
        $('#citiwise-filter-form')[0].reset();
        var select2Element = $('#address_id');
        select2Element.val(null).trigger('change');
        addressDataTable.ajax.url("{{ route('address.index') }}").load();
        originalExportUrl = "{{ route('address.export') }}";
        originalPrintUrl = "{{ route('address.print') }}";
        $('#excel-button').attr('href', originalExportUrl);
        $('#print-button').attr('href', originalPrintUrl);
    });

    $('#citiwise-filter-form').on('submit', function(e) {
        e.preventDefault();

        var address_id = $('#address_id').val();

        if(address_id == undefined){
            address_id = '';
        }
        var params = {
            address_id      : address_id,
        };
        exportUrl = "{{ route('address.export') }}" + '/' + address_id;
        printUrl = "{{ route('address.print') }}" + '/' + address_id;
        // Apply filters to the DataTable
        addressDataTable.ajax.url("{{ route('address.index') }}?"+$.param(params)).load();
        $('#excel-button').attr('href', exportUrl);
        $('#print-button').attr('href', printUrl);
    });


});



</script>
@endsection
