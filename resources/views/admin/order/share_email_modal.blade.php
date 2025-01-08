<div class="modal fade" id="shareEmailModal" tabindex="-1" role="dialog" aria-labelledby="shareEmailModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="shareEmailModalLabel">Share Order via Email</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="shareEmailForm" action="{{ route('orders.send-email',$order)}}">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="email">@lang('quickadmin.customers.fields.email')</label>
                                <div class="input-group">
                                    <input type="email" class="form-control" name="email" value="" id="email" autocomplete="true">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-auto ml-auto mt-4">
                            <button type="submit" class="btn btn-primary">Send Email</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
