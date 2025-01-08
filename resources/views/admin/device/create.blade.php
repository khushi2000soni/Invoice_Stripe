<div class="modal fade px-3" id="centerModal" tabindex="-1" role="dialog" aria-labelledby="centerModalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="centerModalModalLabel">@lang('quickadmin.device.fields.add')</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" id="AddForm" action="{{route('device.store')}}">
                @include('admin.device.form')
                </form>
                {{-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> --}}
            </div>
        </div>
    </div>
</div>
