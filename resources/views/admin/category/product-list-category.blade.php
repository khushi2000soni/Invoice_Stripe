<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th colspan="4" class="text-center">{{ $category->name }} @lang('quickadmin.product-management.fields.list') <span class="float-right"><button class="btn btn-link close-accordion" data-category-id="{{ $category->id }}"><i class="fas fa-times"></i></button></span></th>
            </tr>
            <tr>
                <th>@lang('quickadmin.qa_sn')</th>
                <th class="text-center">@lang('quickadmin.product.fields.name')</th>
                <th class="text-center">@lang('quickadmin.product.fields.created_at')</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($products as $key => $product)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td class="text-center">{{ $product->name }}</td>
                    <td class="text-center">{{ $product->created_at->format('d-M-Y'); }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">No Record Found!</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
