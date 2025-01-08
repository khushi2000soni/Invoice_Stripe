<div class="modal fade px-3" id="editPhoneModal" tabindex="-1" role="dialog" aria-labelledby="editModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered " role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalCenterTitle">@lang('quickadmin.customers.fields.edit')</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form method="post" id="EditCustomerPhone" action="{{route('customers.phoneUpdate',$customer->id)}}">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="phone">@lang('quickadmin.customers.fields.phone')<span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="phone" value="{{ isset($customer) ? $customer->phone : old('phone') }}" id="phone" autocomplete="true">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="phone2">@lang('quickadmin.customers.fields.phone2')<small class="text-danger"> (Optional)</small></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="phone2" value="{{ isset($customer) ? $customer->phone2 : old('phone2') }}" id="phone" autocomplete="true">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-auto ml-auto mt-4">
                            <button type="submit" class="btn btn-primary">@lang('quickadmin.qa_submit')</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
