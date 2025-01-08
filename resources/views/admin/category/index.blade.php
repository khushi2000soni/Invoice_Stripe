@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')
@section('title')@lang('quickadmin.category.title')@endsection
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
    #centerModal, #editCategoryModal{
        z-index: 99999;
    }
    #centerModal::before, #editCategoryModal::before{
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
                    <div class="card-body">
                        <div class="row align-items-center pb-3 mb-4 cart_filter_box">
                                <!-- <div class="col pl-0">
                                    <h4>@lang('quickadmin.category.list-title')</h4>
                                </div> -->
                                <div class="col">
                                    <form id="citiwise-filter-form">
                                        <div class="row align-items-center">
                                            <div class="col-xl-3 col-lg-4 col-md-5 col-sm-6 pr-0">
                                                <div class="custom-select2 fullselect2">
                                                    <div class="form-control-inner">
                                                        <label for="category_id">Select Category</label>
                                                        <select class="js-example-basic-single form-control @error('category_id') is-invalid @enderror" name="category_id" id="category_id" value="">
                                                            <option value="">Select Category</option>
                                                            @foreach($categories as $category)
                                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xl-3 col-lg-4 col-sm-6 text-end">
                                                <div class="form-group d-flex m-0">
                                                    <button type="submit" class="btn btn-primary mr-1 col" id="apply-filter">@lang('quickadmin.qa_submit')</button>
                                                    <button type="reset" class="btn btn-primary mr-1 col" id="reset-filter">@lang('quickadmin.qa_reset')</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <div class="col-md-auto col-12 mt-md-0 mt-3">
                                    <div class="row align-items-center">
                                        <div class="col-auto px-1">
                                            @can('category_create')
                                            <button type="button" class="addnew-btn addRecordBtn sm_btn circlebtn"  data-href="{{ route('categories.create')}}" title="@lang('quickadmin.qa_add_new')"><x-svg-icon icon="add-category" /></button>
                                        @endcan
                                        </div>
                                        <div class="col-auto px-1">
                                            @can('category_print')
                                            <a href="{{ route('categories.print') }}" class="btn h-10 printbtn col circlebtn"  id="print-button" title="@lang('quickadmin.qa_print')"><x-svg-icon icon="print" /></a>
                                            @endcan
                                        </div>
                                        <div class="col-auto pl-1">
                                            @can('category_export')
                                            <a href="{{ route('categories.export')}}" class="btn h-10 excelbtn col circlebtn"  id="excel-button" title="@lang('quickadmin.qa_excel')"><x-svg-icon icon="excel" /></a>
                                            @endcan
                                        </div>
                                    </div>
                                </div>

                        </div>
                        <div class="table-responsive fixed_Search">
                            {{$dataTable->table(['class' => 'table dt-responsive subBodyTable', 'style' => 'width:100%;','id'=>'categoryTable'])}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
          {{-- @dd(session()->all()) --}}
        </div>
  </section>
  <div class="popup_render_div"></div>
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
    var categoryDataTable = $('#categoryTable').DataTable();

    $('#print-button').printPage();

    // Page show from top when page changes
    $(document).on('draw.dt','#categoryTable', function (e) {
        e.preventDefault();
        $('html, body').animate({
            scrollTop: 0
        }, 'fast');
    });

    $(document).on('click' , 'excel-button' , function(e){
        e.preventDefault();
        var iframe = document.createElement('iframe');
        iframe.style.display = 'none';
        iframe.src = '/categories-export'; // Replace with the actual URL for your export route
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
        $('#category_id').select2('close');
    })

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
                    setTimeout(() => {
                        $('.modal-backdrop').not(':first').remove();
                    }, 300);
                }
            }
        });
    });

    $("body").on("click", ".edit-category-btn", function () {
            var hrefUrl = $(this).attr('data-href');
            $.ajax({
                type: 'get',
                url: hrefUrl,
                dataType: 'json',
                success: function (response) {
                    //$('#preloader').css('display', 'none');
                    if(response.success) {
                        $('.popup_render_div').html(response.htmlView);
                        $('#editCategoryModal').modal('show');
                        setTimeout(() => {
                        $('.modal-backdrop').not(':first').remove();
                    }, 300);
                    }
                }
            });
    });


    $(document).on('submit', '#AddCategoryForm', function (e) {
        e.preventDefault();

        $("#AddCategoryForm button[type=submit]").prop('disabled',true);
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
                    var title = "{{ trans('quickadmin.category.category') }}";
                    showToaster(title,alertType,message);
                    $('#AddCategoryForm')[0].reset();
                   // location.reload();
                   categoryDataTable.ajax.reload();
                   $("#AddCategoryForm button[type=submit]").prop('disabled',false);
            },
            error: function (xhr) {
                var errors= xhr.responseJSON.errors;
                for (const elementId in errors) {
                    $("#"+elementId).addClass('is-invalid');
                    var errorHtml = '<div><span class="error text-danger">'+errors[elementId]+'</span></';
                    $(errorHtml).insertAfter($("#"+elementId).parent());
                }
                $("#AddCategoryForm button[type=submit]").prop('disabled',false);
            }
        });
    });


    $(document).on('submit', '#EditCategoryForm', function (e) {
        e.preventDefault();

        $("#EditCategoryForm button[type=submit]").prop('disabled',true);
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
                    $('#editCategoryModal').modal('hide');
                    var alertType = response['alert-type'];
                    var message = response['message'];
                    var title = "{{ trans('quickadmin.category.category') }}";
                    showToaster(title,alertType,message);
                    $('#EditCategoryForm')[0].reset();
                    //location.reload();
                    categoryDataTable.ajax.reload();
                    $("#EditCategoryForm button[type=submit]").prop('disabled',false);
            },
            error: function (xhr) {
                var errors= xhr.responseJSON.errors;
                console.log(xhr.responseJSON);

                for (const elementId in errors) {
                    $("#EditCategoryForm #"+elementId).addClass('is-invalid');
                    var errorHtml = '<div><span class="error text-danger">'+errors[elementId]+'</span></';
                    $(errorHtml).insertAfter($("#EditCategoryForm #"+elementId).parent());
                }
                $("#EditCategoryForm button[type=submit]").prop('disabled',false);
            }
        });
    });


    $(document).on('submit', '.deleteCategoryForm', function(e) {
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
                    var title = "{{ trans('quickadmin.category.category') }}";
                    showToaster(title,alertType,message);
                    categoryDataTable.ajax.reload();
                    // location.reload();

            },
            error: function (xhr) {
                // Handle error response
                swal('Category', 'some mistake is there.', 'error');
            }
            });
        }
        });
    });



    $('#reset-filter').on('click', function(e) {
        e.preventDefault();
        $('#citiwise-filter-form')[0].reset();
        var select2Element = $('#category_id');

        select2Element.val(null).trigger('change');

        categoryDataTable.ajax.url("{{ route('categories.index') }}").load();

        originalExportUrl = "{{ route('categories.export') }}";
        originalPrintUrl = "{{ route('categories.print') }}";
        $('#excel-button').attr('href', originalExportUrl);
        $('#print-button').attr('href', originalPrintUrl);
    });


    $('#citiwise-filter-form').on('submit', function(e) {
        e.preventDefault();

        // Collect filter values (customer, from_date, to_date) from the form
        var category_id = $('#category_id').val();
        if(category_id == undefined){
            category_id = '';
        }
        var params = {
            category_id      : category_id,
        };

        exportUrl = "{{ route('categories.export') }}" + '/' + category_id;
        printUrl = "{{ route('categories.print') }}" + '/' + category_id;
        // Apply filters to the DataTable
        categoryDataTable.ajax.url("{{ route('categories.index') }}?"+$.param(params)).load();
        $('#excel-button').attr('href', exportUrl);
        $('#print-button').attr('href', printUrl);
    });


});



</script>
@endsection
