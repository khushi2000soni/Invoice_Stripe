<div class="modal fade px-3" id="categoryProductModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">{{ $category_name }} - @lang('quickadmin.product-management.fields.list')</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="row w-100">
                            <div class="col-md-8">
                                <h4>@lang('quickadmin.reports.total_amount') : {{ $totalAmount }} </h4>
                                @if ($duration)
                                    <h4>@lang('quickadmin.order.fields.duration') : {{ $duration }}</h4>
                                @endif
                                <h4>@lang('quickadmin.reports.sale_percent') : {{ $category_percent }}</h4>
                            </div>
                            <div class="col-md-4 text-end d-flex justify-content-end p-0">
                                <div class="col-auto px-md-1 pr-1">
                                    <a href="{{ route('reports.category.product.print',['category_id'=>$category_id , 'from_date'=>$from_date , 'to_date'=> $to_date, 'category_percent' => $category_percent])}}" class="btn printbtn h-10 col circlebtn" id="report-product-print" title="@lang('quickadmin.qa_print')">
                                    <x-svg-icon icon="print" />
                                    </a>
                                </div>
                                <!--  -->
                                <div class="col-auto px-md-1 pr-1">
                                    <a href="{{ route('reports.category.product.export',['category_id'=>$category_id , 'from_date'=>$from_date , 'to_date'=> $to_date, 'category_percent' => $category_percent])}}" class="btn excelbtn h-10 col circlebtn" id="report-product-excel" title="@lang('quickadmin.qa_excel')">
                                    <x-svg-icon icon="excel" />
                                    </a>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="card-body p-0">
                        <table id="CatPRoductdataTable" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>@lang('quickadmin.qa_sn')</th>
                                    <th>@lang('quickadmin.qa_product_name')</th>
                                    <th>@lang('quickadmin.reports.total_quantity')</th>
                                    <th>@lang('quickadmin.reports.sale_amount')</th>
                                    <th>@lang('quickadmin.reports.sale_percent')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($alldata as $index => $data)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $data->product_name ?? '' }}</td>
                                        <td>{{ $data->total_quantity ?? '' }}</td>
                                        <td>{{ $data->amount ?? '0' }}</td>
                                        <td>{{ CategoryAmountPercent($data->amount ,$totalAmount) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


