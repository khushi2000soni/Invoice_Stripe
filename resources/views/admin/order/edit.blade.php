
@extends('layouts.app')
@section('title')@lang('quickadmin.order.fields.edit') @endsection
@section('customCss')
<meta name="csrf-token" content="{{ csrf_token() }}" >
<style>
    .buttonGroup{
        gap: 8px
    }
    .invoice hr {
    border-top-color: #ededed;
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
    #centerModal{
        z-index: 99999;
    }
    #centerModal::before{
        display: none;
    }
    .modal-open .modal-backdrop.show{
        display: block !important;
        z-index: 9999;
    }
     .cart_filter_box{
        border-bottom: 1px solid #e5e9f2;
    }
    .alertMessage {
        display: inline-block;
    }
</style>


@endsection

@section('main-content')

<section class="section">
    <div class="section-body">
        <div class="text-center">
            <div class="bg-danger alertMessage d-none my-1 text-center text-light py-1 px-3 rounded" id="internetlostMessage">
                <x-svg-icon icon="internet-disconnected" />
            </div>
            <div class="bg-success alertMessage d-none my-1 text-center text-light py-1 px-3 rounded" id="OnlineComeBack">
                <x-svg-icon icon="internet-connected" />
            </div>
        </div>
        <form method="post" id="SaveEditInvoiceForm" action="{{route('orders.update', $order->id)}}">
            <div class="card pt-2">
                <div class="invoice-print card-body">
                   <div class="row align-items-center pb-3 mb-4 cart_filter_box">
                        <div class="col-lg-12">
                            <div class="row">
                                @can('order_product_create')
                                <div class="col-md-12">
                                    <div class="row align-items-center">
                                        <div class="col-xl-4 col-md-5 col-sm-6 col-12 order-sm-1 order-2">
                                            <div class="custom-select2 fullselect2">
                                                <div class="form-control-inner">
                                                    <label>@lang('quickadmin.order.fields.customer_name')</label>
                                                    <select class="js-customer-list @error('customer_id') is-invalid @enderror" name="customer_id" id="customer_id" >
                                                        <option value="{{ isset($order) ? $order->customer->id : old('customer_id') }}">
                                                            {{ isset($order) ? $order->customer->full_name : trans('quickadmin.order.fields.select_customer') }}
                                                        </option>
                                                        @foreach($customers as $customer)
                                                        <option value="{{ $customer->id }}">{{ $customer->full_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-8 col-md-7 col-sm-6 order-sm-2 order-1 mb-sm-0 mb-4">
                                            <h6 class="text-sm-right text-left m-0">@lang('quickadmin.order.fields.invoice_number') : #{{ $order->invoice_number}}</h6>
                                            <h6 class="text-sm-right text-left m-0">@lang('quickadmin.order.fields.invoice_date') : {{ $invoice_date }}</h6>
                                        </div>
                                    </div>
                                </div>
                                @endcan
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table dt-responsive tablestriped tablehover ordertable">
                                <thead>
                                    <tr>
                                        <th class="text-left">@lang('quickadmin.qa_sn')</th>
                                        <th class="text-left d-none">@lang('quickadmin.order.fields.product_id')</th>
                                        <th class="text-left">@lang('quickadmin.order.fields.product_name')</th>
                                        <th class="text-left">@lang('quickadmin.order.fields.quantity')</th>
                                        <th class="text-left">@lang('quickadmin.order.fields.price')</th>
                                        <th class="text-left">@lang('quickadmin.order.fields.sub_total')</th>
                                        <th class="text-right">@lang('quickadmin.qa_action')</th>
                                        {{-- <th class="text-center">
                                            <div class="form-group m-0">
                                            <div class="input-group">
                                                <button type="button" class="btn btn-success" id="addNewBlankRow"><i class="fas fa-plus"></i></button>
                                            </div>
                                        </div>
                                    </th> --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    @include('admin.order.form')
                                </tbody>
                            </table>
                        </div>
                        <div class="row mt-4">
                            <div class="col">
                                <div class="custom-control custom-checkbox pt-4">
                                    <input type="checkbox" class="custom-control-input" id="is_round_off" @if($order->is_round_off === 1) checked @endif>
                                    <label class="custom-control-label" for="is_round_off">@lang('quickadmin.order.fields.round_off')</label>
                                </div>
                            </div>
                            <div class="col-lg-auto text-right">
                            <div class="invoice-detail-item mb-2">
                                <div class="d-flex align-items-center justify-content-between"><span class="invoice-detail-value">@lang('quickadmin.order.fields.thaila') : </span> <span class="px-2">
                                <input type="numeric" class="form-control form-control-sm col-md-3 float-right" id="thaila_price" name="thaila_price" value="0.00" min="0" step=".001">
                                </span></div>
                            </div>
                            <div class="invoice-detail-item mb-2">
                                <div class="d-flex align-items-center justify-content-between"><span class="invoice-detail-value">@lang('quickadmin.order.fields.sub_total_amount') : </span> <span class="px-2" id="sub_total_amount">0</span></div>
                            </div>
                            <div class="invoice-detail-item mb-2">
                                <div class="d-flex align-items-center justify-content-between"><span class="invoice-detail-value">@lang('quickadmin.order.fields.round_off') : </span> <span class="px-2" id="round_off_amount">0</span></div>
                            </div>
                            <hr class="mt-2 mb-2">
                            <div class="invoice-detail-item mb-2">
                                <div class="d-flex align-items-center justify-content-between"><span class="invoice-detail-value">@lang('quickadmin.order.fields.grand_total') : </span> <span class="px-2" id="grand_total_amount">0</span></div>
                            </div>
                            </div>
                        </div>
                        </div>
                    </div><hr>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-md-right">
                                <div class="float-lg-left">
                                    <button type="submit" class="btn btn-success btn-icon icon-left saveInvoicebtn" id="saveInvoicebtn"><i class="fas fa-credit-card"></i>@lang('quickadmin.qa_save_invoice')</button>
                                    {{-- <button class="btn btn-light btn-icon icon-left"><i class="fas fa-print"></i>@lang('quickadmin.qa_print_invoice')</button> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
  </section>

  <div class="popup_render_div"></div>
@endsection

@section('customJS')
  <script>

    var order = {
        customer_id:0,
        products: [],
        deleted_products: [],
        thailaPrice: 0.00,
        is_round_off: 0,
        sub_total: 0.00,
        round_off_amount: 0,
        grand_total: 0.00
    };
    var rowIndex;

    $(document).ready(function() {
        localStorage.removeItem('order');
        order = @json($orderData);
        order.customer_id = parseInt(order.customer_id);
        order.sub_total = parseFloat(order.sub_total);
        order.grand_total = parseFloat(order.grand_total);
        order.thailaPrice = parseFloat(order.thailaPrice);
        order.round_off_amount = parseInt(order.round_off_amount);
        /// load existing data into localstorage
        order.products.forEach(function (product) {
            product.price = parseFloat(product.price);
            product.order_product_id = parseInt(product.order_product_id);
            product.total_price = parseFloat(product.total_price);
        });
        order.deleted_products=[];
        updateLocalStorage(order);
        var storedOrder = JSON.parse(localStorage.getItem("order"));
        if (storedOrder && storedOrder.products) {
            order = storedOrder;
            order.products.forEach(function (product, index) {
                product.rowIndex = index;
            });
            loadExistingProducts();
        }
        updateLocalStorage(order);
        // Update the subtotal in the UI
        $('#sub_total_amount').text(order.sub_total);
        $('#round_off_amount').text(order.round_off_amount);
        $('#grand_total_amount').text(order.grand_total);

        $("#customer_id").change(function (e) {
            e.preventDefault();
            $(".error.text-danger").remove();
            $(".is-invalid").removeClass("is-invalid");
            order.customer_id = parseInt($(this).val());
        });

        $("#product_id").change(function (e) {
            e.preventDefault();
            order.product_id = parseInt($(this).val());
            order.product_name = $(this).find("option:selected").text();
            $("#selectedProductName").text(order.product_name);
        });

        $("#is_round_off").change(function (e) {
            e.preventDefault();
            order.is_round_off = this.checked ? 1 : 0;
            calculateGrandTotal();
        });

        // Event listener for the thaila_price input field change
        $("#thaila_price").on("input", function (e) {
            e.preventDefault();
            updateThailaPrice();
            calculateGrandTotal();
        });

        // Load Existing Products into Table
        function loadExistingProducts() {
            // Assuming 'your-table-id' is the ID of your table
            var tableBody = $('.ordertable tbody tr:not(.template-row)');
            tableBody.empty(); // Clear existing rows in the table
            // Check if there are existing products in localStorage
            var storedOrder = JSON.parse(localStorage.getItem("order"));
            if (storedOrder && storedOrder.products) {
                    // Iterate over existing products in the order and add rows to the table
                    storedOrder.products.forEach(function(product, rowIndex) {
                    var newRow = $('.template-row').clone().removeClass('template-row');
                    newRow.attr('id', 'row_' + rowIndex);
                    newRow.data('rowIndex', rowIndex);
                    newRow.data('order-id', storedOrder.orderid); // Set data-order-id attribute
                    newRow.data('order-product-id', product.order_product_id); // Set order_product_id attribute
                    // Update values in the new row
                    newRow.find('.js-product-basic-single').val(product.product_id).trigger('change');
                    newRow.find('input[name="quantity"]').val(product.quantity);
                    newRow.find('input[name="price"]').val(product.price);
                    newRow.find('input[name="total_price"]').val(product.total_price);
                    newRow.css('display', '');
                    $('.addNewBlankRow').hide();
                    if (rowIndex === storedOrder.products.length - 1) {
                        // Show the "Add New Blank Row" button only for the last row
                        newRow.find('.addNewBlankRow').show();
                    }
                    $('.ordertable tbody').append(newRow);
                    newRow.show();
                    var selectBox = newRow.find(".js-product-basic-single");
                    var select2Container = selectBox.next('.select2-container');
                    if (select2Container.length > 0) {
                        select2Container.remove();
                    }
                    // Initialize Select2 for the new row's select box
                    selectBox.select2({
                        width: '100%', // Set the desired width
                        dropdownAutoWidth: true // Enable auto-width for the dropdown
                    }).on('select2:open', function () {
                        let a = $(this).data('select2');
                        if (!$('.select2-link').length) {
                            a.$results.parents('.select2-results')
                                .append('<div class="select2-link2"><button class="btns addNewBtn get-product"><i class="fa fa-plus-circle"></i> Add New</button></div>');
                        }
                    });
                    newRow.find('.sr_no').html(rowIndex+1);
                    newRow.find('.copy-product').attr('data-row-index', rowIndex);
                    newRow.find('.delete-product').attr('data-row-index', rowIndex);
                });

                // Update other order-related details if needed
                $('#order_id').val(storedOrder.orderid);
                $('#invoice_number').val(storedOrder.invoice_number);
                $('#customer_id').val(storedOrder.customer_id);
                $('#thaila_price').val(storedOrder.thailaPrice);
            }
        }


         // Add New Blank Row
        function addBlankRow() {
            var lastRow = $('.ordertable tbody tr:not(.template-row):last');
            var isValid = validateRow(lastRow);
            if (lastRow.length > 0) {
                var isValid = validateRow(lastRow);
                if (!isValid) {
                    // Display error messages or take appropriate action
                    return;
                }
            }
            removerror();

            var newRow = $('.template-row').clone().removeClass('template-row');
            var rowIndex = $('.ordertable tbody tr:not(.template-row)').length;
            newRow.attr('id', 'row_' + rowIndex);
           // newRow.find('.js-product-basic-single').attr('id', 'product_id_' + rowIndex);
            newRow.data('rowIndex', rowIndex);
            newRow.find('input').val('');
            newRow.css('display', '');
            var selectBox = newRow.find(".js-product-basic-single");
            var select2Container = selectBox.next('.select2-container');
            if (select2Container.length > 0) {
                select2Container.remove();
            }

            $('.ordertable tbody').append(newRow);
            newRow.show();
            // Initialize Select2 for the new row's select box
            selectBox.select2({
                width: '100%', // Set the desired width
                dropdownAutoWidth: true // Enable auto-width for the dropdown
            }).on('select2:open', function () {
                let a = $(this).data('select2');
                if (!$('.select2-link').length) {
                    a.$results.parents('.select2-results')
                        .append('<div class="select2-link2"><button class="btns addNewBtn get-product"><i class="fa fa-plus-circle"></i> Add New</button></div>');
                }
            });
            newRow.find('.sr_no').html(rowIndex+1);
            newRow.find('.copy-product').attr('data-row-index', rowIndex);
            newRow.find('.delete-product').attr('data-row-index', rowIndex);

            var productRecord = {
                rowIndex: rowIndex,
                product_id: parseInt(selectBox.val()) || '',
                product_name: order.product_name || '', // Adjust this based on your data structure
                quantity: parseInt(newRow.find('input[name="quantity"]').val()) || 0,
                price: parseFloat(newRow.find('input[name="price"]').val()) || 0,
                total_price: parseFloat(newRow.find('input[name="total_price"]').val()) || 0
            };

            lastRow.find('.addNewBlankRow').hide();
            newRow.find('.addNewBlankRow').show();
            order.products.push(productRecord);
            updateLocalStorage(order);
            updateSubtotal();
            calculateGrandTotal();
        }

        $(document).on('click','.addNewBlankRow', function (e) {
            e.preventDefault();
            addBlankRow();
        });

        //// Validated last row of table

        function validateRow(row) {
            removerror();
            var customer_id = parseInt($('select[name="customer_id"]').val());
            var product_id = parseInt(row.find('select[name="product_id"]').val());
            var quantity = parseFloat(row.find('input[name="quantity"]').val());
            var price = parseFloat(row.find('input[name="price"]').val());
            var errors = {
                customer_id: "The Customer Name is required.",
                product_id: "The Product Name is required.",
                quantity: "The Quantity is required.",
                price: "The Price is required.",
                total_price: "Please fill quantity & price."
            };

            var hasErrors = false;
            // Check if customer_id is filled
            if (!customer_id) {
                $('select[name="customer_id"]').addClass('is-invalid');
                var errorHtml = '<div><span class="error text-danger">' + errors.customer_id + '</span></div>';
                $(errorHtml).insertAfter($('select[name="customer_id"]').parent());
                hasErrors = true;
            }

            // Check if product_id is filled
            if (!product_id) {
                row.find('select[name="product_id"]').addClass('is-invalid');
                var errorHtml = '<div><span class="error text-danger">' + errors.product_id + '</span></div>';
                $(errorHtml).insertAfter(row.find('select[name="product_id"]').parent());
                hasErrors = true;
            }

            // Check if quantity is filled
            if (!quantity) {
                row.find('input[name="quantity"]').addClass('is-invalid');
                var errorHtml = '<div><span class="error text-danger">' + errors.quantity + '</span></div>';
                $(errorHtml).insertAfter(row.find('input[name="quantity"]').parent());
                hasErrors = true;
            }

            // Check if price is filled
            if (!price) {
                row.find('input[name="price"]').addClass('is-invalid');
                var errorHtml = '<div><span class="error text-danger">' + errors.price + '</span></div>';
                $(errorHtml).insertAfter(row.find('input[name="price"]').parent());
                hasErrors = true;
            }

            // Display total_price error if both quantity and price are not filled
            if (!quantity && !price) {
                row.find('input[name="total_price"]').addClass('is-invalid');
                var errorHtml = '<div><span class="error text-danger">' + errors.total_price + '</span></div>';
                $(errorHtml).insertAfter(row.find('input[name="total_price"]').parent());
            }

            return !hasErrors;
        }

        // Initialize the thailaPrice from local storage if available
        if (localStorage.getItem("order")) {
            order = JSON.parse(localStorage.getItem("order"));
            $("#thaila_price").val(order.thailaPrice || 0.00);
        }

        function updateThailaPrice() {
            var thailaPrice = parseFloat($("#thaila_price").val()) || 0.00;
            order.thailaPrice = thailaPrice;
            localStorage.setItem("order", JSON.stringify(order));
        }

        /// Total of qty x price
        function calculateAmount(row) {
            var rowIndex = parseInt(row.data('rowIndex'));
            var quantity = parseFloat(row.find('#quantity').val()) || 0;
            var price = parseFloat(row.find('#price').val()) || 0;
            var total_price = (quantity * price).toFixed(2);
            quantity = parseFloat(quantity.toFixed(3));
            price = parseFloat(price.toFixed(3));

            row.find('#total_price').val(total_price);
            var productRecord = order.products.find(record => record.rowIndex === rowIndex);
            if (productRecord) {
                productRecord.quantity = quantity;
                productRecord.price = price;
                productRecord.total_price = parseFloat(total_price);
            }

            updateLocalStorage(order);
            updateSubtotal();
            calculateGrandTotal();
        }

        // When qty or price change total price calculated

        $('.ordertable tbody').on('input', '#quantity, #price', function () {
            removerror();
            var row = $(this).closest('tr');
            calculateAmount(row);
        });

        function updateProductDetails(row, selectedProductId, selectedProductName) {
            var rowIndex = parseInt(row.data('rowIndex'));

            // Update the corresponding product record in order.products
            var productRecord = order.products.find(record => record.rowIndex === rowIndex);
            if (productRecord) {
                productRecord.product_id = selectedProductId;
                productRecord.product_name = selectedProductName;
            }

            // Save the updated order object back to localStorage
            updateLocalStorage(order);
        }

        // Event listener for product selection change
        $('.ordertable tbody').on('change', '.js-product-basic-single', function (e) {
            e.preventDefault();
            removerror();
            var row = $(this).closest('tr');

            var selectedProductId = $(this).val();
            selectedProductId = parseInt(selectedProductId);
            var selectedProductName = $(this).find('option:selected').text();
            // Update product details in the specific row
            updateProductDetails(row, selectedProductId, selectedProductName);
        });


        $(document).on('select2:open','.js-product-basic-single', function (e) {
            e.preventDefault();
            $(this).siblings('.select2-container').addClass('active');
            activeSelect2 = $(this);
        });

        function updateLocalStorage(order) {
            localStorage.setItem('order', JSON.stringify(order));
        }

        // Function to clear the form fields
        function clearForm() {
            $("#product_id, #quantity, #price, #total_price").val("");
            $(".is-invalid").removeClass("is-invalid");
            $(".error").remove();
        }

        function removerror(){
            $(".error.text-danger").remove();
            $(".is-invalid").removeClass("is-invalid");
        }

        function calculateSubtotal() {
            let subtotal = 0;
            for (const product of order.products) {
                totalPrice=parseFloat(product.total_price);
                subtotal += totalPrice || 0;
            }
            return parseFloat(subtotal.toFixed(2));
        }

        function updateSubtotal() {
            var subtotal = calculateSubtotal();
            $('#sub_total_amount').text(subtotal.toFixed(2));
            order.sub_total = subtotal;
            updateLocalStorage(order);
        }

        function calculateGrandTotal() {
            var subTotal = calculateSubtotal();
            var thailaPrice = parseFloat(order.thailaPrice);
            var isRoundOff = parseInt(order.is_round_off);
            if (isRoundOff) {
                // Calculate the Rounded Subtotal
                var roundedSubtotal = Math.floor(subTotal);
                // Calculate the Round Off amount
                // var roundOffAmount = roundedSubtotal - subTotal;
                var roundOffAmount = Math.abs(roundedSubtotal - subTotal);  // it will get positive value
                // Update and display Round Off amount and Rounded Subtotal
                $("#round_off_amount").text(roundOffAmount.toFixed(2));
                order.round_off_amount = parseFloat(roundOffAmount.toFixed(2));
            } else {
                $("#round_off_amount").text("0");
                order.round_off_amount = 0;
            }

            var grandTotal = thailaPrice + (isRoundOff ? roundedSubtotal : subTotal);
            $("#sub_total_amount").text(subTotal.toFixed(2));
            $("#grand_total_amount").text(grandTotal.toFixed(2));
            // Update the grand total in the order object
            order.grand_total = grandTotal;
            updateLocalStorage(order);
        }

        // copy row

        $(document).on('click', '.copy-product', function (e) {
            e.preventDefault();
            var rowIndex = $(this).data('row-index');
            var lastRow = $('.ordertable tbody tr:not(.template-row):last');
            var productToCopy = order.products[rowIndex];    // Find the product to copy based on the row index
            var copiedRow = $('#row_' + rowIndex).clone();  // Clone the row to copy
            var newRowindex = $('.ordertable tbody tr:not(.template-row)').length;  // Increment the row index for the copied row
            copiedRow.attr('id', 'row_' + newRowindex);
            copiedRow.data('rowIndex', newRowindex);
            var isValid = validateRow($('#row_' + rowIndex));      /// validate blank values
            if (!isValid) {
                // Display error messages or take appropriate action
                return;
            }
            // check current row has addblankblank btn or not
            var hasAddNewButton = $(".ordertable tbody tr:not(.template-row)").eq(rowIndex).find('.addNewBlankRow:visible').length > 0;
            // Update IDs and attributes for select and input elements in the copied row
            copiedRow.find('.js-product-basic-single').attr('id', 'product_id_' + newRowindex);
            copiedRow.find('.copy-product').attr('data-row-index', newRowindex);
            copiedRow.find('.delete-product').attr('data-row-index', newRowindex);
            // Update input values in the copied row
            // copiedRow.find('input').each(function () {
            //     var originalValue = $(this).val();
            //     $(this).val(originalValue);
            // });
            copiedRow.find('input[name="quantity"]').val('');
            copiedRow.find('input[name="price"]').val('');
            copiedRow.find('input[name="total_price"]').val('');
            copiedRow.find('.sr_no').html(newRowindex+1);
            // Set the selected product in the copied row's Select2 dropdown
            var selectedProductId = productToCopy.product_id;
            var selectBox = copiedRow.find(".js-product-basic-single");
            selectBox.val(selectedProductId).trigger('change');
            // Append the copied row to the table body
            $('.ordertable tbody').append(copiedRow);
            // Initialize Select2 for the new row's select box
            var select2Container = selectBox.next('.select2-container');
            if (select2Container.length > 0) {
                select2Container.remove();
            }
            selectBox.select2({
                width: '100%', // Set the desired width
                dropdownAutoWidth: true // Enable auto-width for the dropdown
            }).on('select2:open', function () {
                let a = $(this).data('select2');
                if (!$('.select2-link').length) {
                    a.$results.parents('.select2-results')
                        .append('<div class="select2-link2"><button class="btns addNewBtn get-product"><i class="fa fa-plus-circle"></i> Add New</button></div>');
                }
            });
            // Update the data-row-index attribute for the copy button in the new row
            copiedRow.find('.copy-product').attr('data-row-index', newRowindex);
            // Add the copied product to the order array
            var copiedProduct = {
                rowIndex: newRowindex,
                product_id: productToCopy.product_id,
                product_name: productToCopy.product_name,
                quantity: 0,
                price: 0,
                total_price: 0
            };
            order.products.push(copiedProduct);
            if (hasAddNewButton) {
                lastRow.find('.addNewBlankRow').hide();
            }
            else{
                lastRow.find('.addNewBlankRow').hide();
                copiedRow.find('.addNewBlankRow').show();
            }
            // Update localStorage, UI, or perform any other necessary actions
            updateLocalStorage(order);
            updateSubtotal();
            calculateGrandTotal();
        });

        // delete order

        $(document).on('click', '.delete-product', function (e) {
            e.preventDefault();
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
                    // Get the row index from the data attribute
                    var rowIndex = $(this).data('row-index');
                    // Check if the row to delete contains the addNewBlankRow button
                    var hasAddNewButton = $(".ordertable tbody tr:not(.template-row)").eq(rowIndex).find('.addNewBlankRow').length > 0;
                    var productToDelete = order.products[rowIndex];
                    if ('order_product_id' in productToDelete) {
                        // The record has an order_product_id, so it's marked for deletion
                        // Push the order_product_id to the deleted_products array
                        order.deleted_products.push(productToDelete.order_product_id);
                    }
                    // Remove the row from the table
                    var rowToDelete = $(".ordertable tbody tr:not(.template-row)").eq(rowIndex);
                    rowToDelete.remove();
                    // Remove the product from the order.products array
                    order.products.splice(rowIndex, 1);
                    // Update row indexes in the remaining rows
                    $(".ordertable tbody tr:not(.template-row)").each(function (index) {
                        $(this).find('.copy-product').attr('data-row-index', index);
                        $(this).find('.delete-product').attr('data-row-index', index);
                        $(this).data('rowIndex', index);
                        $(this).attr('id', 'row_' + index);
                        $(this).find('.sr_no').html(index + 1);
                    });

                    order.products.forEach((product, index) => {
                        product.rowIndex = index;
                    });

                    // If the deleted row had the addNewBlankRow button, add it to the previous row
                    if (hasAddNewButton) {
                        var lastRow = $('.ordertable tbody tr:not(.template-row):last');
                        lastRow.find('.addNewBlankRow').show();
                    }

                    updateLocalStorage(order);
                    updateSubtotal();
                    calculateGrandTotal();
                }
            });
        });


        // ***********Code Starts for select box modal of party and item ***********

        $(".js-customer-list").select2({
        }).on('select2:open', function () {
            let a = $(this).data('select2');
            if (!$('.select2-link').length) {
                a.$results.parents('.select2-results')
                    .append('<div class="select2-link2"><button class="btns addNewCustomerBtn get-customer" ><i class="fa fa-plus-circle"></i> Add New</button></div>');
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

        $(document).on('click', '.select2-container .get-city', function (e) {
            e.preventDefault();
            var gethis = $(this);
            var hrefUrl = "{{ route('address.create') }}";
            $('.modal-backdrop').remove();
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

        $(document).on('click', '.select2-container .get-product', function (e) {
            e.preventDefault();
            var gethis = $(this);
            var hrefUrl = "{{ route('products.create') }}";
            $('.modal-backdrop').remove();
            $.ajax({
                type: 'get',
                url: hrefUrl,
                dataType: 'json',
                success: function (response) {
                    if(response.success) {
                        $('.popup_render_div').html(response.htmlView);
                        $('.popup_render_div #centerModal').modal('show');
                        $(".js-example-basic-single").select2({
                            dropdownParent: $('.popup_render_div #centerModal') // Set the dropdown parent to the modal
                        }).on('select2:open', function () {
                            let a = $(this).data('select2');
                            if (!$('.select2-link').length) {
                                a.$results.parents('.select2-results')
                                .append('<div class="select2-link2"><button class="btns AddCatBtn get-category close-select2" ><i class="fa fa-plus-circle"></i> Add New</button></div>');
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
            $('.modal-backdrop').remove();
            // Fetch data and populate the second modal
            $.ajax({
                type: 'get',
                url: hrefUrl,
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        $('.categorymodalbody').remove();
                        $('.popup_render_div #category_id').select2('close');
                        $('.popup_render_div').after('<div class="categorymodalbody" style="display: block;"></div>');
                        $('.categorymodalbody').html(response.htmlView);
                        $('.categorymodalbody #centerModal').modal('show');
                        $('.categorymodalbody #centerModal').attr('style', 'z-index: 100000');
                    // $('.popup_render_div #centerModal').modal('show');
                        //$('.popup_render_div #centerModal').attr('style', 'z-index: 9999');
                        // $('.popup_render_div #centerModal').on('shown.bs.modal', function () {
                        //     $('.categorymodalbody #centerModal').modal('show');
                        //     $('.categorymodalbody #centerModal').attr('style', 'z-index: 100000');
                        // });
                    }
                }
            });
        });

        $(document).on('hidden.bs.modal','.categorymodalbody .modal', function (e) {
            e.preventDefault();
            $('.categorymodalbody').remove();
        });


        $(document).on('hidden.bs.modal','.centerModal .modal', function (e) {
            e.preventDefault();
            $('.select2-container').removeClass('active');
            activeSelect2 = null;
        });

        // Code Add New Customer or Product Both

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
                        if (response.selectdata.formtype == 'customer') {
                            $('#customer_id').append(newOption).trigger('change');
                        } else if (response.selectdata.formtype == 'product') {
                          //  $('#product_id').append(newOption).trigger('change');
                         // $('.js-product-basic-single .active').append(newOption).trigger('change');
                         if (activeSelect2) {
                            activeSelect2.append(newOption).trigger('change');
                            $('.select2-container').removeClass('active');
                            activeSelect2 = null;
                        }
                        }

                        showToaster(title,alertType,message);
                        $('#AddForm')[0].reset();
                        //location.reload();
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
                        // $('.addressmodalbody #centerModal').modal('hide');
                        // $('.popup_render_div #centerModal').modal('hide');
                        form.closest('#centerModal').modal('hide');
                        var newOption = new Option(response.address.address, response.address.id, true, true);
                        $('.popup_render_div #centerModal #address_id').append(newOption).trigger('change');
                        var alertType = response['alert-type'];
                        var message = response['message'];
                        var title = "{{ trans('quickadmin.address.address') }}";
                        showToaster(title,alertType,message);
                        $('#AddaddressForm')[0].reset();
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
                        var newOption = new Option(response.category.name, response.category.id, true, true);
                        $('.popup_render_div #category_id').append(newOption).trigger('change');
                        showToaster(title,alertType,message);
                        $('#AddCategoryForm')[0].reset();
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

        // Initialize the thailaPrice from local storage if available
        if (localStorage.getItem("order")) {
            order = JSON.parse(localStorage.getItem("order"));
            $("#thaila_price").val(order.thailaPrice || 0.00);
        }

        $(document).on('submit', '#SaveEditInvoiceForm', function (e) {
            e.preventDefault();
            showLoader();
            var isValid = true;
            $(".ordertable tbody tr:not(.template-row)").each(function () {
                if (!validateRow($(this))) {
                    isValid = false;
                }
            });

            if (!isValid) {
                // Display an error message or take appropriate action
                return;
            }

            //$("#SaveEditInvoiceForm button[type=submit]").prop('disabled',true);
            var formAction = $(this).attr('action');

            if(networkstatus === true){
                var invoiceData = {
                    customer_id: order.customer_id,
                    thaila_price: order.thailaPrice,
                    is_round_off: order.is_round_off,
                    round_off: order.round_off_amount,
                    sub_total: order.sub_total,
                    grand_total: order.grand_total,
                    products: order.products, // The products array as-is
                    deleted_products: order.deleted_products,
                };
                // Send data to the server using AJAX
                console.log("Sending data to the server");
                $.ajax({
                    url: formAction,
                    type: 'PUT',
                    data: invoiceData,
                    headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        hideLoader();
                        console.log('Data stored in the database:', response);
                        // Remove data from localStorage after successful insertion
                        var alertType = response['alert-type'];
                        var message = response['message'];
                        var title = "{{ trans('quickadmin.order.order') }}";
                        swal({
                        title: title,
                        text: message,
                        icon: alertType,
                        buttons: {
                        confirm: 'OK',
                        },
                        }).then((confirm) => {
                        if (confirm) {
                            localStorage.removeItem('order');
                            window.location.href = "{{ route('orders.index') }}";
                        }
                        });

                        $('#SaveEditInvoiceForm')[0].reset();
                        //location.reload();
                        $("#SaveEditInvoiceForm button[type=submit]").prop('disabled',false);
                        console.log('Data removed from localStorage.');
                    },
                    error: function (xhr) {
                        hideLoader();
                        var errors= xhr.responseJSON.errors;
                        console.log(xhr.responseJSON);
                        swal("{{ trans('quickadmin.product.product') }}", 'Some mistake is there.', 'error');
                        // for (const elementId in errors) {
                        //     $("#"+elementId).addClass('is-invalid');
                        //     var errorHtml = '<div><span class="error text-danger">'+errors[elementId]+'</span></';
                        //     $(errorHtml).insertAfter($("#"+elementId).parent());
                        // }
                        // $("#AddForm button[type=submit]").prop('disabled',false);
                        var alertType = errors['alert-type'];
                        var message = errors['message'];
                        var title = "{{ trans('quickadmin.order.order') }}";
                        //showToaster(title,alertType,message);
                    }
                });
            }else{
                hideLoader();
                console.log('networkstatus',networkstatus);
                alert("You have lost internet connection, Please connect with the internet to save your Temorary data");
                $("#saveInvoicebtn").replaceWith('<button type="submit" class="btn btn-success btn-icon icon-left saveTempInvoiceDatabtn" id="saveTempInvoiceDatabtn"><i class="fas fa-credit-card"></i>@lang("quickadmin.qa_temp_save_invoice")</button>');
            }
        });
    });

  </script>

@endsection
