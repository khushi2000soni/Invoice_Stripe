@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')
@section('title')@lang('quickadmin.qa_dashboard')@endsection
@section('customCss')
<meta name="csrf-token" content="{{ csrf_token() }}" >
@endsection

@section('main-content')

<section class="section roles" style="z-index: unset">

    <div class="section-body dashboard-card">
        @can('dashboard_widget_access')
        <div class="row">
            <div class="col-lg-6 col-md-12 col-sm-12">
                <div class="row diffrent-cards">
                    <div class="col-12 col-md-6 col-lg-6">
                        <div class="card card-info">
                        <div class="card-header">
                           <!--  <div class="card-square l-bg-cyan text-white rounded">
                                <i class="fas fa-rupee-sign p-2"></i>
                            </div> -->
                            <h4 class="">@lang('quickadmin.dashboard.todaySaleAmount')</h4>
                        </div>
                        <div class="card-body">
                            <h4>{{ $todaySaleAmount }}</h4>
                        </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6">
                        <div class="card card-info" >
                        <div class="card-header">
                            <!-- <div class="card-square l-bg-cyan text-white rounded">
                                <i class="fas fa-rupee-sign p-2"></i>
                            </div> -->
                            <h4 class="">@lang('quickadmin.dashboard.last7DaysSaleAmount')</h4>
                        </div>
                        <div class="card-body">
                            <h4>{{ $last7DaysSaleAmount }}</h4>
                        </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6">
                        <div class="card card-info">
                        <div class="card-header">
                            <!-- <div class="card-square l-bg-cyan text-white rounded">
                                <i class="fas fa-rupee-sign p-2"></i>
                            </div> -->
                            <h4 class="">@lang('quickadmin.dashboard.last30DaysSaleAmount')</h4>
                        </div>
                        <div class="card-body">
                            <h4>{{ $currentMonthSaleAmount }}</h4>
                        </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6">
                        <div class="card card-info">
                        <div class="card-header">
                            <!-- <div class="card-square l-bg-cyan text-white rounded">
                                <i class="fas fa-rupee-sign p-2"></i>
                            </div> -->
                            <h4 class=" me-0">@lang('quickadmin.dashboard.allSaleAmount')</h4>
                        </div>
                        <div class="card-body">
                            <h4>{{ $allSaleAmount }}</h4>
                        </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6">
                        <div class="card card-info">
                        <div class="card-header">
                            <!-- <div class="card-square l-bg-cyan text-white rounded">
                                <i class="fas fa-shopping-cart p-2"></i>
                            </div> -->
                            <h4 class=" me-0">@lang('quickadmin.dashboard.todayTotalOrder')</h4>
                        </div>
                        <div class="card-body">
                            <h4>{{ $todayTotalOrder }}</h4>
                        </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6">
                        <div class="card card-info">
                        <div class="card-header">
                            <!-- <div class="card-square l-bg-cyan text-white rounded">
                                <i class="far fa-credit-card p-2"></i>
                            </div> -->
                            <h4 class=" me-0">@lang('quickadmin.dashboard.totalProductInStock')</h4>
                        </div>
                        <div class="card-body">
                            <h4>{{ $totalProductInStock }}</h4>
                        </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6">
                        <div class="card card-info">
                        <div class="card-header">
                            <!-- <div class="card-square l-bg-cyan text-white rounded">
                                <i class="far fa-credit-card p-2"></i>
                            </div> -->
                            <h4 class=" me-0">@lang('quickadmin.dashboard.totalCategory')</h4>
                        </div>
                        <div class="card-body">
                            <h4>{{ $totalCategory }}</h4>
                        </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6">
                        <div class="card card-info">
                        <div class="card-header">
                            <!-- <div class="card-square l-bg-cyan text-white rounded">
                                <i class="fas fa-user-plus p-2"></i>
                            </div> -->
                            <h4 class=" me-0">@lang('quickadmin.dashboard.totalCustomer')</h4>
                        </div>
                        <div class="card-body">
                            <h4>{{ $totalCustomer }}</h4>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-12 col-md-12">
                <div class="card overflow-hidden h-100 border-0 ordertble-block">
                  <div class="card-header">
                    <div class="row">
                        <div class="col-12">
                            <h5 class="mt-1 mb-1">@lang('quickadmin.dashboard.order')</h5>
                        </div>
                        <!-- <div class="col-6">
                            <div class="form-group mb-1">
                                <select id="timeFrameOrderSelectTable" class="form-select">
                                    <option value="today">@lang('quickadmin.dashboard.today')</option>
                                    <option value="7days">@lang('quickadmin.dashboard.7days')</option>
                                    <option value="30days">@lang('quickadmin.dashboard.30days')</option>
                                </select>
                            </div>
                        </div> -->
                    </div>
                  </div>
                  <div class="card-body" style="max-height: 430px;  overflow-y: auto;">
                    <div class="table-responsive">
                      <table class="table" id="order-table-body">
                        <thead>
                        <tr>
                          <th class="py-0">@lang('quickadmin.dashboard.customer')</th>
                          <th class="py-0">@lang('quickadmin.dashboard.amount')</th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
            </div>
            <div class="col-lg-12 col-sm-12">
                <div class="card mt-5">
                <div class="card-body card-type-3">
                    <div class="row">
                        <div class="col-12 col-lg-6">
                            <h5 class="mt-1 mb-1">@lang('quickadmin.reports.total_order_amount')</h5>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="form-group mb-1 text-right">
                                <select id="timeFrameOrderChartSelect" class="form-select" >
                                    <option value="yearly" {{ $timeFrame === 'yearly' ? 'selected' : '' }}>@lang('quickadmin.reports.yearly')</option>
                                    <option value="monthly" {{ $timeFrame === 'monthly' ? 'selected' : '' }}>@lang('quickadmin.reports.monthly')</option>
                                    <option value="daily" {{ $timeFrame === 'daily' ? 'selected' : '' }}>@lang('quickadmin.reports.daily')</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-chart mb-2 ml-4 mr-4">
                    <canvas id="orderChart"></canvas>
                </div>
                </div>
            </div>
        </div>
        @endcan

    </div>
  </section>
@endsection


@section('customJS')
<script src="{{ asset('admintheme/assets/bundles/jquery-ui/jquery-ui.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
$(document).ready(function() {

    var orderData = @json($data);

    var ctx = document.getElementById('orderChart').getContext('2d');
    var orderChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: orderData.labels,
            datasets: [{
                label: "@lang('quickadmin.reports.total_order_amount')",
                data: orderData.values,
                backgroundColor: 'rgba(255, 99, 132, 0.2)', // Reddish background color
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                    callback: function(value, index, values) {
                        return '₹' + value; // Add the rupee symbol
                    },
                    //stepSize: 2000, // Adjust the step size as needed
                    },
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return "@lang('quickadmin.reports.total_order_amount') : ₹" + context.parsed.y; // Display rupee symbol in tooltip
                        }
                    }
                }
            }
        }
    });

    function updateChart(timeFrame) {
        var data = { timeFrame: timeFrame };
        $.ajax({
            type: 'GET',
            url: "{{ route('fetchReportData') }}",
            data: data,
            success: function(response) {
                var newData = response.data;
                $('#timeFrameOrderChartSelect').val(response.timeFrame);
                orderChart.data.labels = newData.labels;
                orderChart.data.datasets[0].data = newData.values;
                orderChart.update();
            },
            error: function(error) {
                console.error(error);
            }
        });
    }

    // Initialize the chart with the default data (monthly)
    updateChart('daily');

    // Handle the time frame select change for the chart
    $('#timeFrameOrderChartSelect').on('change', function(e) {
        e.preventDefault();
        var selectedTimeFrame = this.value;
        updateChart(selectedTimeFrame);
    });

    // Function to update the table data
    function updateTableData(timeFrame) {
        var data = { timeFrame: timeFrame };
        $.ajax({
            type: 'GET',
            url: "{{ route('fetchReportData') }}",
            data: data,
            success: function(response) {
                var newData = response.data;
                // Update the table content
                var tableBody = $('#order-table-body tbody');
                tableBody.empty();
                newData.forEach(function(order) {
                    var row = '<tr><td><div class="media-body"><div class="media-tab-title">' + order.customer.full_name +'</div>' +
                              '<div class="text-job text-muted">#' + order.invoice_number + '</div></div></td><td class="">' + order.grand_total  + '</td></tr>';
                    tableBody.append(row);
                });
            },
            error: function(error) {
                console.error(error);
            }
        });
    }

    // Handle the time frame select change for the table
    $('#timeFrameOrderSelectTable').on('change', function(e) {
        e.preventDefault();
        var selectedTimeFrame = this.value;
        updateTableData(selectedTimeFrame);
    });

    // Load the default data when the page loads
    updateTableData('today');
});
</script>


@endsection
