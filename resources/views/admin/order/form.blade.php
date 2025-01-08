
<tr class="template-row" style="display:none;">
    <td><div class="sr_no"></div></td>
    <td>
        <div class="form-group m-0">
            <div class="custom-select2 fullselect2">
                <div class="form-control-inner">
                    <select class="js-product-basic-single @error('product_id') is-invalid @enderror" name="product_id" id="product_id">
                        <option value="">{{ trans('quickadmin.order.fields.select_product') }}</option>
                        @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->full_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </td>
    <td>
        <div class="form-group m-0">
            <input type="number" class="form-control"  min="0" step=".001" name="quantity" value="{{ isset($order) ? $order->quantity : old('quantity') }}" id="quantity" autocomplete="true" onkeydown="javascript: return ['Tab','NumpadDecimal','Period','Backspace','Delete','ArrowLeft','ArrowRight'].includes(event.code) ? true : !isNaN(Number(event.key)) && event.code!=='Space'">
        </div>
    </td>
    <td>
        <div class="form-group m-0">
            <input type="number" class="form-control" min="0" step=".01" name="price" value="{{ isset($order) ? $order->price : old('price') }}" id="price" autocomplete="true" onkeydown="javascript: return ['Tab','NumpadDecimal','Period', 'Backspace','Delete','ArrowLeft','ArrowRight'].includes(event.code) ? true : !isNaN(Number(event.key)) && event.code!=='Space'">
        </div>
    </td>
    <td>
        <div class="form-group m-0">
            <input type="number" class="form-control" name="total_price" value="{{ isset($order) ? $order->total_price : old('total_price') }}" id="total_price" autocomplete="true" min="0" step=".01"  readonly>
        </div>
    </td>
    <td class="text-right">
        <div class="d-flex align-items-center buttonGroup justify-content-end">
            @can('order_product_create')
            <button  class="btn btn-success addNewBlankRow"><i class="fas fa-plus"></i></button>
            @endcan
            @can('order_product_copy')
            <button class="btn btn-dark btn-sm copy-product"><x-svg-icon icon="copy" /></button>
            @endcan
            @can('order_product_delete')
            <button class="btn btn-danger btn-sm delete-product"><x-svg-icon icon="delete" /></button>
            @endcan
        </div>
    </td>
</tr>
