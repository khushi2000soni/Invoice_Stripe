
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
        <div class="card pt-2">
            <div class="invoice-print card-body">
                <div class="row align-items-center pb-3 mb-4 cart_filter_box">
                    <div class="col-lg-12">
                        <div class="row">
                            @can('order_product_create')
                            <div class="col-md-12">
                                <div class="row align-items-center">                                    
                                    <div class="col-xl-8 col-md-7 col-sm-6 order-sm-2 order-1 mb-sm-0 mb-4">
                                        <h6 class="text-sm-right text-left m-0">@lang('quickadmin.order.fields.invoice_number') : #{{ $order->invoice_number}}</h6>
                                        <h6 class="text-sm-right text-left m-0">@lang('quickadmin.order.fields.invoice_date') : {{ $order->invoice_date }}</h6>
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
                            @include('admin.order.pdf.main-pdf-table')
                    </div>
                    
                    </div>
                </div><hr>
                <div class="row">
                    <div class="col-md-4">
                        <div class="text-md-right">
                            <div class="float-lg-left">
                                {{-- <a href="{{ route('payment.checkout', ['id' => $order->id]) }}" class="btn btn-success">Proceed to Payment</a> --}}
                                <button 
            id="pay-btn"
            class="btn btn-success mt-3"
            style="width: 100%; padding: 7px;"
            onclick="createCheckoutSession({{ $order->id }})">Pay {{ $order->grand_total }} INR
        </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
  </section>

@endsection

@section('customJS')
<script src="https://js.stripe.com/v3/"></script>
<script>
    const stripe = Stripe("{{ env('STRIPE_PUBLISH_KEY') }}");

    function createCheckoutSession(orderId) {
        // Disable button during the process to prevent multiple clicks
        document.getElementById("pay-btn").disabled = true;

        // Send AJAX request to create the Stripe checkout session
        $.ajax({
            url: "{{ route('payment.create-session', ['id' => ':orderId']) }}".replace(':orderId', orderId),
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
            },
            success: function(response) {
                if (response.id) {
                    // Redirect the user to Stripe's Checkout page
                    stripe.redirectToCheckout({ sessionId: response.id })
                        .then(function(result) {
                            if (result.error) {
                                alert(result.error.message);
                            }
                        });
                } else {
                    alert('Error: ' + response.error);
                }
            },
            error: function() {
                alert('An error occurred while creating the checkout session.');
                document.getElementById("pay-btn").disabled = false;
            }
        });
    }
</script>
@endsection
