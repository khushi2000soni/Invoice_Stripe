
@extends('layouts.app')
@section('title')@lang('quickadmin.user-management.title')@endsection
@section('customCss')
<meta name="csrf-token" content="{{ csrf_token() }}" >
<link rel="stylesheet" href="{{ asset('admintheme/assets/css/printView-datatable.css')}}">
@endsection

@section('main-content')
<style type="text/css">
    .cart_filter_box {
        border-bottom: 1px solid #e5e9f2;
        padding-bottom: 4px;
    }
</style>
<section class="section roles" style="z-index: unset">
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center pb-3 mb-3 cart_filter_box">
                                <div class="col">
                                    <h4>@lang('quickadmin.user-management.title')</h4>
                                </div>
                                <div class="col-auto  mt-md-0 mt-3 ml-auto">
                                    @if ($type != 'deleted')
                                    <div class="row align-items-center">
                                        <div class="col-auto px-1">
                                            @can('staff_create')
                                            <button type="button" class="addnew-btn addRecordBtn sm_btn circlebtn" data-toggle="modal" data-target="#centerModal" data-href="{{ route('staff.create')}}" title="@lang('quickadmin.qa_add_new')"><x-svg-icon icon="add" /></button>
                                            @endcan
                                        </div>
                                        <div class="col-auto px-1">
                                            @can('staff_print')
                                            <a href="{{ route('staff.print') }}" class="printbtn btn h-10 col circlebtn"  id="print-button"  title="@lang('quickadmin.qa_print')"><x-svg-icon icon="print" /></a>
                                            @endcan
                                        </div>
                                        <div class="col-auto pl-1">
                                            @can('staff_export')
                                            <a href="{{ route('staff.export')}}" class="excelbtn btn h-10 col circlebtn"  id="excel-button" title="@lang('quickadmin.qa_excel')"><x-svg-icon icon="excel" /></a>
                                            @endcan
                                        </div>
                                        <div class="col-auto pl-1">
                                            @can('staff_rejoin')
                                            <a href="{{ route('staff.typeindex',['type'=> 'deleted'])}}" class="recycleicon btn h-10 col circlebtn" title="@lang('quickadmin.qa_rejoin_staff')"><x-svg-icon icon="rejoin-btn" /></a>
                                            @endcan
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <div class=" fixed_Search">
                                {{$dataTable->table(['class' => 'table dt-responsive', 'style' => 'width:100%;','id'=>'dataaTable'])}}
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
    var DataaTable = $('#dataaTable').DataTable();
    $('#print-button').printPage();
    // Page show from top when page changes
    $(document).on('draw.dt','#dataaTable', function (e) {
        e.preventDefault();
        $('html, body').animate({
            scrollTop: 0
        }, 'fast');
    });

    $(document).on('click','.addRecordBtn', function(){
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
                    $('#centerModal').modal('show');
                }
            }
        });
    });

    $("body").on("click", ".edit-users-btn", function () {
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


    $(document).on('submit', '#AddForm', function (e) {
        e.preventDefault();

        $("#AddForm button[type=submit]").prop('disabled',true);
        $(".error").remove();
        $(".is-invalid").removeClass('is-invalid');
        var formData = $(this).serialize();
        var formAction = $(this).attr('action');
        $.ajax({
            url: '{{ route('staff.store') }}',
            type: 'POST',
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: formData,
            success: function (response) {
                    $('#centerModal').modal('hide');
                    var alertType = response['alert-type'];
                    var message = response['message'];
                    var title = "{{ trans('quickadmin.users.users') }}";
                    showToaster(title,alertType,message);
                    $('#AddForm')[0].reset();
                   // location.reload();
                   DataaTable.ajax.reload();
                   $("#AddForm button[type=submit]").prop('disabled',false);
            },
            error: function (xhr) {
                var errors= xhr.responseJSON.errors;
                console.log(xhr.responseJSON);

                for (const elementId in errors) {
                    $("#"+elementId).addClass('is-invalid');
                    var errorHtml = '<div><span class="error text-danger">'+errors[elementId]+'</span></';
                    $(errorHtml).insertAfter($("#"+elementId).parent());
                }
                $("#AddForm button[type=submit]").prop('disabled',false);
            }
        });
    });


    $(document).on('submit', '#EditForm', function (e) {
        e.preventDefault();
        $("#EditForm button[type=submit]").prop('disabled',true);
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
                    var title = "{{ trans('quickadmin.users.users') }}";
                    showToaster(title,alertType,message);
                    $('#EditForm')[0].reset();
                    DataaTable.ajax.reload();
                    $("#EditForm button[type=submit]").prop('disabled',false);
            },
            error: function (xhr) {
                var errors= xhr.responseJSON.errors;
                for (const elementId in errors) {
                    $("#EditForm #"+elementId).addClass('is-invalid');
                    var errorHtml = '<div><span class="error text-danger">'+errors[elementId]+'</span></';
                    $(errorHtml).insertAfter($("#EditForm #"+elementId).parent());
                }
                $("#EditForm button[type=submit]").prop('disabled',false);
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
                    var title = "{{ trans('quickadmin.users.users') }}";
                    showToaster(title,alertType,message);
                    $('#EditPasswordForm')[0].reset();
                    DataaTable.ajax.reload();
                    $("#EditPasswordForm button[type=submit]").prop('disabled',false);
            },
            error: function (xhr) {
                var errors= xhr.responseJSON.errors;
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
                    var title = "{{ trans('quickadmin.users.users') }}";
                    showToaster(title,alertType,message);
                    DataaTable.ajax.reload();
                    // location.reload();

            },
            error: function (xhr) {
                // Handle error response
                swal("{{ trans('quickadmin.users.users') }}", 'some mistake is there.', 'error');
            }
            });
        }
        });
    });

    // rejoin or restore
    $(document).on('click', '.rejoin-users-btn', function(e) {
        e.preventDefault();
        var formAction = $(this).data('href');
        swal({
        title: "{{ trans('messages.rejointitle') }}",
        text: "{{ trans('messages.areYouSurerejoin') }}",
        icon: 'warning',
        buttons: {
        confirm: 'Yes, Rejoin Staff',
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
                    var title = "{{ trans('quickadmin.users.users') }}";
                    showToaster(title,alertType,message);
                    DataaTable.ajax.reload();
                    // location.reload();
            },
            error: function (xhr) {
                // Handle error response
                swal("{{ trans('quickadmin.order.invoice') }}", 'Some mistake is there.', 'error');
            }
            });
        }
        });
    });




});



</script>
@endsection
