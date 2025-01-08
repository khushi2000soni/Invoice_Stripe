
@extends('layouts.app')
@section('title')@lang('quickadmin.phone-book.title')@endsection
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
    #centerModal, #editModal{
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
                  <h4>@lang('quickadmin.phone-book.fields.list')</h4>
                </div> --}}
                <div class="card-body">
                    <div class="row align-items-center pb-3 mb-4 cart_filter_box">
                        <div class="col">
                            <form id="citiwise-filter-form">
                                <div class="row">
                                    <div class="col-md-3 pr-0">
                                        <div class="custom-select2 fullselect2">
                                            <div class="form-control-inner">
                                                <label>@lang('quickadmin.customers.fields.select_address')</label>
                                                <select class="form-control filter-address-select @error('address_id') is-invalid @enderror" name="address_id" id="address_id" tabindex="0">
                                                    <option value="">@lang('quickadmin.customers.fields.select_address')</option>
                                                    @foreach($addresses as $address)
                                                    <option value="{{ $address->id }}">{{ $address->address }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 text-end">
                                        <div class="form-group d-flex mb-0">
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
                                    @can('customer_create')
                                    <button type="button" class="addnew-btn addRecordBtn sm_btn circlebtn"  data-href="{{ route('customers.create')}}"><x-svg-icon icon="add" /></button>
                                    @endcan
                                </div>
                                <div class="col-auto px-1">
                                    @can('phone_book_print')
                                    <a href="{{ route('PhoneBook.print') }}" class="btn printbtn h-10 col circlebtn"  id="print-button"> <x-svg-icon icon="print" /></a>
                                    @endcan
                                </div>
                                <div class="col-auto pl-1">
                                    @can('phone_book_export')
                                    <a href="{{ route('PhoneBook.export') }}" class="btn excelbtn h-10 col circlebtn"  id="excel-button"><x-svg-icon icon="excel" /></a>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive fixed_Search">
                        {{$dataTable->table(['class' => 'table dt-responsive customerTable', 'style' => 'width:100%;','id'=>'dataaTable'])}}
                    </div>
                </div>
              </div>

              <div class="popup_render_div"></div>
            </div>
          </div>
        </div>
  </section>
@endsection


@section('customJS')
{!! $dataTable->scripts() !!}
  <script src="{{ asset('admintheme/assets/bundles/datatables/datatables.min.js') }}"></script>
  <script src="{{ asset('admintheme/assets/bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}"></script>
  <script src="{{ asset('admintheme/assets/bundles/jquery-ui/jquery-ui.min.js') }}"></script>
  <!-- Page Specific JS File -->
  <script src="{{ asset('admintheme/assets/js/page/datatables.js') }}"></script>

<script>
$(document).ready(function () {

    $('#print-button').printPage();

    var DataaTable = $('#dataaTable').DataTable();

    // Page show from top when page changes
    $(document).on('draw.dt','#dataaTable', function (e) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: 0
            }, 'fast');
        });

    $(".filter-address-select").select2({
    }).on('select2:open', function () {
        let a = $(this).data('select2');
        if (!$('.select2-link').length) {
            a.$results.parents('.select2-results')
                .append('<div class="select2-link2"><button class="btns addNewAddressBtn"><i class="fa fa-plus-circle"></i> Add New</button></div>');
        }
    });

    $(document).on('click','.addNewAddressBtn',function(e){
        e.preventDefault();
        var hrefUrl = '{{ route('address.create') }}';
        $.ajax({
            type: 'get',
            url: hrefUrl,
            dataType: 'json',
            success: function (response) {
                //$('#preloader').css('display', 'none');
                if(response.success) {
                    $('.popup_render_div').html(response.htmlView);
                    $('#centerModal').modal('show');
                    $(".js-example-basic-single").select2({
                        dropdownParent: $('.popup_render_div #centerModal') // Set the dropdown parent to the modal
                    });
                }
            }
        });

        $('#citiwise-filter-form #address_id').select2('close');
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

    $(document).on('hidden.bs.modal','.addressmodalbody .modal', function (e) {
        e.preventDefault();
        $('.addressmodalbody').remove();
    });

    $(document).on('click', '.addRecordBtn', function (e) {
        e.preventDefault();
        var hrefUrl = $(this).attr('data-href');
        $.ajax({
            type: 'get',
            url: hrefUrl,
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    // Render the modal content for the first modal
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
    });

    $("body").on("click", ".edit-customers-btn", function () {
        var hrefUrl = $(this).attr('data-href');
        $.ajax({
            type: 'get',
            url: hrefUrl,
            dataType: 'json',
            success: function (response) {
                //$('#preloader').css('display', 'none');
                if(response.success) {
                    $('.popup_render_div').html(response.htmlView);
                    $('#editModal').modal('show');
                        // Initialize select2 for the first modal
                    $(".js-example-basic-single").select2({
                        dropdownParent: $('.popup_render_div #editModal') // Set the dropdown parent to the modal
                    });
                    setTimeout(() => {
                        $('.modal-backdrop').not(':first').remove();
                    }, 300);
                }
            }
        });
    });

    $("body").on("click", ".edit-password-btn", function () {
            var hrefUrl = $(this).attr('data-href');
            $.ajax({
                type: 'get',
                url: hrefUrl,
                dataType: 'json',
                success: function (response) {
                    //$('#preloader').css('display', 'none');
                    if(response.success) {
                        $('.popup_render_div').html(response.htmlView);
                        $('#passwordModal').modal('show');
                    }
                }
            });
    });

    /// Add Party
    $(document).on('submit', '#centerModal #AddForm', function (e) {
        e.preventDefault();

        $("#centerModal #AddForm button[type=submit]").prop('disabled',true);
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
                    var title = "{{ trans('quickadmin.customers.customer') }}";
                    showToaster(title,alertType,message);
                    $('#centerModal #AddForm')[0].reset();
                   // location.reload();
                   DataaTable.ajax.reload();
                   $("#centerModal #AddForm button[type=submit]").prop('disabled',false);
            },
            error: function (xhr) {
                var errors= xhr.responseJSON.errors;
                console.log(xhr.responseJSON);

                for (const elementId in errors) {
                    $("#centerModal #"+elementId).addClass('is-invalid');
                    var errorHtml = '<div><span class="error text-danger">'+errors[elementId]+'</span></';
                    $(errorHtml).insertAfter($("#centerModal #"+elementId).parent());
                }
                $("#centerModal #AddForm button[type=submit]").prop('disabled',false);
            }
        });
    });


    $(document).on('submit', '#editModal #EditForm', function (e) {
        e.preventDefault();
        $("#editModal #EditForm button[type=submit]").prop('disabled',true);
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
                    $('#editModal').modal('hide');
                    var alertType = response['alert-type'];
                    var message = response['message'];
                    var title = "{{ trans('quickadmin.customers.customer') }}";
                    showToaster(title,alertType,message);
                    $('#editModal #EditForm')[0].reset();
                    //location.reload();
                    DataaTable.ajax.reload();
                    $("#editModal #EditForm button[type=submit]").prop('disabled',false);
            },
            error: function (xhr) {
                var errors= xhr.responseJSON.errors;
                console.log(xhr.responseJSON);

                for (const elementId in errors) {
                    $("#EditForm #"+elementId).addClass('is-invalid');
                    var errorHtml = '<div><span class="error text-danger">'+errors[elementId]+'</span></';
                    $(errorHtml).insertAfter($("#editModal #EditForm #"+elementId).parent());
                }
                $("#editModal #EditForm button[type=submit]").prop('disabled',false);
            }
        });
    });

    $(document).on('submit', '#EditPasswordForm', function (e) {
        e.preventDefault();
        $("#EditPasswordForm button[type=submit]").prop('disabled',true);
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
                    $('#passwordModal').modal('hide');
                    var alertType = response['alert-type'];
                    var message = response['message'];
                    var title = "{{ trans('quickadmin.customers.customer') }}";
                    showToaster(title,alertType,message);
                    $('#EditPasswordForm')[0].reset();
                    //location.reload();
                    DataaTable.ajax.reload();
                    $("#EditPasswordForm button[type=submit]").prop('disabled',false);
            },
            error: function (xhr) {
                var errors= xhr.responseJSON.errors;
                console.log(xhr.responseJSON);

                for (const elementId in errors) {
                    $("#EditPasswordForm #"+elementId).addClass('is-invalid');
                    var errorHtml = '<div><span class="error text-danger">'+errors[elementId]+'</span></';
                    $(errorHtml).insertAfter($("#EditPasswordForm #"+elementId).parent());
                }
                $("#EditPasswordForm button[type=submit]").prop('disabled',false);
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
                    var title = "{{ trans('quickadmin.customers.customer') }}";
                    showToaster(title,alertType,message);
                    DataaTable.ajax.reload();
                    // location.reload();

            },
            error: function (xhr) {
                // Handle error response
                swal("{{ trans('quickadmin.customers.customer') }}", 'some mistake is there.', 'error');
            }
            });
        }
        });
    });


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
                    // Create a new option element for the first select
                    //var newOption1 = new Option(response.address.address, response.address.id, true, true);
                    // Append the new option to the first select and trigger change event
                    //$('#citiwise-filter-form #address_id').append(newOption1).trigger('change');
                    var addressSelect = $('#citiwise-filter-form #address_id');
                    var newOption = $('<option>', { value: response.address.id, text: response.address.address });
                    addressSelect.append(newOption).html(
                        $('option', addressSelect).sort((x, y) => $(x).text().localeCompare($(y).text()))
                    ).prepend(addressSelect.find('option[value=""]').detach()).trigger('change');


                    // Create a new option element for the second select
                    var newOption2 = new Option(response.address.address, response.address.id, true, true);
                    // Append the new option to the second select and trigger change event
                    $('#AddForm #address_id').append(newOption2).trigger('change');
                    var alertType = response['alert-type'];
                    var message = response['message'];
                    var title = "{{ trans('quickadmin.address.address') }}";
                    showToaster(title,alertType,message);
                    $('#AddaddressForm')[0].reset();
                    //DataaTable.ajax.reload();
                    $("#AddaddressForm button[type=submit]").prop('disabled',false);
            },
            error: function (xhr) {
                var errors= xhr.responseJSON.errors;
                console.log(xhr.responseJSON);

                for (const elementId in errors) {
                    $("#"+elementId).addClass('is-invalid');
                    var errorHtml = '<div><span class="error text-danger">'+errors[elementId]+'</span></';
                    $(errorHtml).insertAfter($("#"+elementId).parent());
                }
                $("#AddaddressForm button[type=submit]").prop('disabled',false);
            }
        });
    });



    $('#reset-filter').on('click', function(e) {
        e.preventDefault();
        $('#citiwise-filter-form')[0].reset();

        var select2Element = $('#address_id');
        select2Element.val(null).trigger('change');

        DataaTable.ajax.url("{{ route('showPhoneBook') }}").load();
        originalExportUrl = "{{ route('PhoneBook.export') }}";
        originalPrintUrl = "{{ route('PhoneBook.print') }}";
        $('#excel-button').attr('href', originalExportUrl);
        $('#print-button').attr('href', originalPrintUrl);
    });

    $('#citiwise-filter-form').on('submit', function(e) {
        e.preventDefault();

        // Collect filter values (customer, from_date, to_date) from the form
        var address_id = $('#address_id').val();
        if(address_id == undefined){
            address_id = '';
        }
        var params = {
            address_id      : address_id,
        };

        exportUrl = "{{ route('PhoneBook.export') }}" + '/' + address_id;
        printUrl = "{{ route('PhoneBook.print') }}" + '/' + address_id;
        // Apply filters to the DataTable
        DataaTable.ajax.url("{{ route('showPhoneBook') }}?"+$.param(params)).load();
        $('#excel-button').attr('href', exportUrl);
        $('#print-button').attr('href', printUrl);

    });

});
</script>

@endsection
