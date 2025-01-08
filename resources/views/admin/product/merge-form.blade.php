<div class="modal fade px-3" id="MergeProductModal" tabindex="-1" role="dialog" aria-labelledby="MergeProductModalTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="MergeProductModalTitle">@lang('quickadmin.product.fields.merge') {{ isset($product) ? $product->name : '' }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" id="submitMergeForm" action="{{route('products.merge')}}">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <input type="hidden" name="from_product_id" value="{{ isset($product) ? $product->id : old('from_product_id') }}"  />
                                </div>
                            </div>
                            <div class="col-md-12 mb-4">
                                <div class="form-group mb-1 fullselect2 label-position">
                                    <label for="to_product_id" >@lang('quickadmin.product.fields.to_product')</label>
                                    <select id="timeFrameOrderChartSelect" class="form-select @error('to_product_id') is-invalid @enderror" name="to_product_id" id="to_product_id">
                                        <option value="">@lang('quickadmin.product.select_item')</option>
                                            @foreach($allproducts as $product)
                                                <option value="{{ $product->id }}">{{ $product->full_name }}</option>
                                            @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary">@lang('quickadmin.qa_submit')</button>
                            </div>
                        </div>
                </form>
            </div>
        </div>
    </div>
</div>

