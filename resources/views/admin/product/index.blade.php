
@extends('layouts.app')
@section('title')@lang('quickadmin.product-management.title')@endsection
@section('customCss')
<meta name="csrf-token" content="{{ csrf_token() }}" >
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
    #centerModal::before, #editModal::before{
        display: none;
    }

    .modal-open .modal-backdrop.show{
        display: block !important;
        z-index: 9999;
    }

    .select2-dropdown{
        z-index: 99999;
    }
    .cart_filter_box{
        border-bottom: 1px solid #e5e9f2;
        padding-bottom: 4px;
    }
    /* #editModal .select2-results, #centerModal .select2-results{
        padding-top: 0px !important;
    } */
    #editModal .select2-results{
        padding-top: 0px !important;
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
                                    <h4>@lang('quickadmin.product-management.fields.list')</h4>
                                </div> -->
                                <div class="col">
                                    <form id="custom-filter-form">
                                        <div class="row align-items-center">
                                            <div class="col-xl-3 col-lg-4 col-md-5 col-sm-6 pr-0">
                                                <div class="custom-select2 fullselect2">
                                                    <div class="form-control-inner">
                                                        <label for="category_id">@lang('quickadmin.product.select_category')</label>
                                                        <select class="form-control filter-category-select @error('category_id') is-invalid @enderror" name="category_id" id="category_id" value="">
                                                            <option value="">@lang('quickadmin.product.select_category')</option>
                                                            @foreach($categories as $category)
                                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-xl-3 col-lg-4 col-sm-6 text-end">
                                                <div class="custom-select2 fullselect2">
                                                    <div class="form-control-inner">
                                                        <label for="product_id">Select Item</label>
                                                        <select class="form-control filter-product-select @error('product_id') is-invalid @enderror" name="product_id" id="product_id" value="">
                                                            <option value="">Select Item</option>
                                                            @foreach($products as $product)
                                                                <option value="{{ $product->id }}">{{ $product->full_name }}</option>
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
                                            @can('product_create')
                                            <button type="button" class="addnew-btn addRecordBtn sm_btn circlebtn"  data-href="{{ route('products.create')}}" title="@lang('quickadmin.qa_add_new')"><x-svg-icon icon="add-item" /></button>
                                            @endcan
                                        </div>

                                        <div class="col-auto px-1">
                                            @can('product_print')
                                            <a href="{{ route('products.print') }}" class="btn h-10 printbtn col circlebtn"  id="print-button" title="@lang('quickadmin.qa_print')"><x-svg-icon icon="print" /></a>
                                            @endcan
                                        </div>
                                        <div class="col-auto pl-1">
                                            @can('product_export')
                                            <a href="{{ route('products.export')}}" class="btn h-10 excelbtn col circlebtn"  id="excel-button" title="@lang('quickadmin.qa_excel')"><x-svg-icon icon="excel" /></a>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive fixed_Search">
                                {{$dataTable->table(['class' => 'table dt-responsive', 'style' => 'width:100%;','id'=>'dataaTable'])}}
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

    $(".filter-category-select").select2({
    }).on('select2:open', function () {
        let a = $(this).data('select2');
        if (!$('.select2-link').length) {
            a.$results.parents('.select2-results')
                .append('<div class="select2-link2"><button class="btns addNewCatBtn"><i class="fa fa-plus-circle"></i> Add New</button></div>');
        }
    });

    $(document).on('click','.addNewCatBtn',function(e){
        e.preventDefault();

        var hrefUrl = '{{ route('categories.create') }}';
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

        $('#custom-filter-form #category_id').select2('close');
    });

    $(".filter-product-select").select2({
    }).on('select2:open', function () {
        let a = $(this).data('select2');
        if (!$('.select2-link').length) {
            a.$results.parents('.select2-results')
                .append('<div class="select2-link2"><button class="btns addNewProductBtn"><i class="fa fa-plus-circle"></i> Add New</button></div>');
        }
    });

    $(document).on('click','.addNewProductBtn',function(e){
        e.preventDefault();
        $(".addRecordBtn").trigger('click');
        $('#custom-filter-form #product_id').select2('close');
    });

    $(document).on('click' , 'excel-button' , function(e){
        e.preventDefault();
        var iframe = document.createElement('iframe');
        iframe.style.display = 'none';
        iframe.src = '/products-export'; // Replace with the actual URL for your export route
        document.body.appendChild(iframe);
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
                    $('.popup_render_div #centerModal').modal('show');
                    $(".js-example-basic-single").select2({
                        dropdownParent: $('.popup_render_div #centerModal') // Set the dropdown parent to the modal
                    }).on('select2:open', function () {
                        let a = $(this).data('select2');
                        if (!$('.select2-link').length) {
                            a.$results.parents('.select2-results')
                            .append('<div class="select2-link2"><button class="btns get-category close-select2" data-toggle="modal" data-target="#centerModal"><i class="fa fa-plus-circle"></i> Add New</button></div>');
                        }
                    });

                }
            }
        });
    });

    $(document).on('click', '.select2-container .get-category', function (e) {
        e.preventDefault();
        var gethis = $(this);
        var hrefUrl = "{{ route('categories.create') }}";
        // Fetch data and populate the second modal
        $.ajax({
            type: 'get',
            url: hrefUrl,
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    $('.popup_render_div #category_id').select2('close');
                    $('.popup_render_div').after('<div class="categorymodalbody" style="display: block;"></div>');
                    $('.categorymodalbody').html(response.htmlView);
                    $('.popup_render_div #centerModal').modal('show');
                    $('.popup_render_div #centerModal').attr('style', 'z-index: 9999');
                    $('.popup_render_div #centerModal').on('shown.bs.modal', function () {
                        $('.categorymodalbody #centerModal').modal('show');
                        $('.categorymodalbody #centerModal').attr('style', 'z-index: 100000');
                    });
                }
            }
        });
    });

    $(document).on('hidden.bs.modal','.categorymodalbody .modal', function (e) {
        e.preventDefault();
        $('.categorymodalbody').remove();
    });

    $("body").on("click", ".edit-products-btn", function (e) {
        e.preventDefault();
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
                        $(".js-example-basic-single").select2({
                             dropdownParent: $('.popup_render_div #editModal') // Set the dropdown parent to the modal
                        });
                    }
                }
            });
    });

    $("body").on("click", ".merge-button", function (e) {
            e.preventDefault();
            var hrefUrl = $(this).attr('data-href');
            $.ajax({
                type: 'get',
                url: hrefUrl,
                dataType: 'json',
                success: function (response) {
                    //$('#preloader').css('display', 'none');
                    if(response.success) {
                        $('.popup_render_div').html(response.htmlView);
                        $('.popup_render_div #MergeProductModal').modal('show');
                        $('.popup_render_div #MergeProductModal').css('z-index', '99999');
                        $(document).find('.popup_render_div #MergeProductModal select').select2({
                            dropdownParent: $('.popup_render_div #MergeProductModal')
                        });
                    }
                }
            });
    });

    // Add Item
    $(document).on('submit', '#centerModal #AddForm', function (e) {
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
                    var title = "{{ trans('quickadmin.product.product') }}";

                    if (!$('.js-example-basic-single').data('select2')) {
                        $('.js-example-basic-single').select2();
                    }
                    var newOption = new Option(response.product.name, response.product.id, true, true);
                    $('#product_id').append(newOption).trigger('change');
                    showToaster(title,alertType,message);
                    $('#AddForm')[0].reset();
                    DataaTable.ajax.reload();
                    $("#AddForm button[type=submit]").prop('disabled',false);
            },
            error: function (xhr) {
                var errors= xhr.responseJSON.errors;
                for (const elementId in errors) {
                    $("#"+elementId).addClass('is-invalid');
                    var errorHtml = '<div><span class="error text-danger">'+errors[elementId]+'</span></';
                    $(errorHtml).insertAfter($("#centerModal #"+elementId).parent());
                }
                $("#AddForm button[type=submit]").prop('disabled',false);
            }
        });
    });

    // edit item
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
                    var title = "{{ trans('quickadmin.product.product') }}";
                    showToaster(title,alertType,message);
                    $('#EditForm')[0].reset();
                    //location.reload();
                    DataaTable.ajax.reload();
                    $("#EditForm button[type=submit]").prop('disabled',false);
            },
            error: function (xhr) {
                var errors= xhr.responseJSON.errors;
                console.log(xhr.responseJSON);

                for (const elementId in errors) {
                    $("#EditForm #"+elementId).addClass('is-invalid');
                    var errorHtml = '<div><span class="error text-danger">'+errors[elementId]+'</span></';
                    $(errorHtml).insertAfter($("#EditForm #"+elementId).parent());
                }
                $("#EditForm button[type=submit]").prop('disabled',false);
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
                    var title = "{{ trans('quickadmin.product.product') }}";
                    showToaster(title,alertType,message);
                    DataaTable.ajax.reload();
                    // location.reload();

            },
            error: function (xhr) {
                // Handle error response
                swal("{{ trans('quickadmin.product.product') }}", 'some mistake is there.', 'error');
            }
            });
        }
        });
    });

    // add new category
    $(document).on('submit', '#AddCategoryForm', function (e) {
        e.preventDefault();

        $("#AddCategoryForm button[type=submit]").prop('disabled',true);
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

                    // $('.categorymodalbody #centerModal').modal('hide');
                    // $('.popup_render_div #centerModal').modal('hide');

                    form.closest('#centerModal').modal('hide');

                    var alertType = response['alert-type'];
                    var message = response['message'];
                    var title = "{{ trans('quickadmin.category.category') }}";

                    if (!$('.filter-category-select').data('select2')) {
                        $('.filter-category-select').select2();
                    }

                    if (!$('.js-example-basic-single').data('select2')) {
                        $('.js-example-basic-single').select2();
                    }

                    var newOption = new Option(response.category.name, response.category.id, true, true);
                    $('#category_id').append(newOption).trigger('change');
                    $('.popup_render_div #category_id').append(newOption).trigger('change');

                    showToaster(title,alertType,message);
                    $('#AddCategoryForm')[0].reset();
                    DataaTable.ajax.reload();
                    $("#AddCategoryForm button[type=submit]").prop('disabled',false);
            },
            error: function (xhr) {
                var errors= xhr.responseJSON.errors;
                console.log(xhr.responseJSON);

                for (const elementId in errors) {
                    $("#"+elementId).addClass('is-invalid');
                    var errorHtml = '<div><span class="error text-danger">'+errors[elementId]+'</span></';
                    $(errorHtml).insertAfter($("#"+elementId).parent());
                }
                $("#AddCategoryForm button[type=submit]").prop('disabled',false);
            }
        });
    });

    $('#reset-filter').on('click', function(e) {
        e.preventDefault();
        $('#custom-filter-form')[0].reset();
        var select2Element = $('#product_id');
        select2Element.val(null).trigger('change');
        var select2ElementCategory = $('#category_id');
        select2ElementCategory.val(null).trigger('change');

        DataaTable.ajax.url("{{ route('products.index') }}").load();

        originalExportUrl = "{{ route('products.export') }}";
        originalPrintUrl = "{{ route('products.print') }}";
        $('#excel-button').attr('href', originalExportUrl);
        $('#print-button').attr('href', originalPrintUrl);
    });


    $('#custom-filter-form').on('submit', function(e) {
        e.preventDefault();

        // Collect filter values (customer, from_date, to_date) from the form
        var product_id = $('#product_id').val();
        if(product_id == undefined){
            product_id = '';
        }
        var category_id = $('#category_id').val();
        if(category_id == undefined || category_id == ""){
            category_id = null;
        }
        var params = {
            category_id      : category_id,
            product_id      : product_id,
        };

        exportUrl = "{{ route('products.export') }}" + '/' + category_id+ '/' + product_id;
        printUrl = "{{ route('products.print') }}" + '/' + category_id+ '/' + product_id;
        // Apply filters to the DataTable
        DataaTable.ajax.url("{{ route('products.index') }}?"+$.param(params)).load();
        $('#excel-button').attr('href', exportUrl);
        $('#print-button').attr('href', printUrl);
    });

    //// Merge Items Functionality

    $(document).on('submit', '#submitMergeForm', function (e) {
        e.preventDefault();
        $("#submitMergeForm button[type=submit]").prop('disabled',true);
        $(".error").remove();
        var form = $(this);
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
                    form.closest('#MergeProductModal').modal('hide');
                    var alertType = response['alert-type'];
                    var message = response['message'];
                    var title = "{{ trans('quickadmin.product.product') }}";
                    showToaster(title,alertType,message);
                    $('#submitMergeForm')[0].reset();
                    //location.reload();
                    DataaTable.ajax.reload();
                    $("#submitMergeForm button[type=submit]").prop('disabled',false);
            },
            error: function (xhr) {
                var errors= xhr.responseJSON.errors;
                console.log(xhr.responseJSON);
                for (const elementId in errors) {
                    $("#"+elementId).addClass('is-invalid');
                    var errorHtml = '<div><span class="error text-danger">'+errors[elementId]+'</span></';
                    $(errorHtml).insertAfter($("#"+elementId).parent());
                }
                $("#submitMergeForm button[type=submit]").prop('disabled',false);
            }
        });
    });


});



</script>
@endsection
