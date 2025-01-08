<div class="modal fade px-3" id="OrderModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            {{-- <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">@lang('quickadmin.qa_view') @lang('quickadmin.order.invoice')</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div> --}}
            <div class="modal-body p-2">
                <div class="mt-5">
                    @include('admin.order.pdf.invoice-pdf')
                </div>
                {{-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> --}}
            </div>
        </div>
    </div>
</div>
