
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="name">@lang('quickadmin.customers.fields.name')<span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="text" class="form-control" name="name" value="{{ isset($customer) ? $customer->name : old('name') }}" id="name" autocomplete="true">
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="guardian_name">@lang('quickadmin.customers.fields.guardian_name')<span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="text" class="form-control" name="guardian_name" value="{{ isset($customer) ? $customer->guardian_name : old('guardian_name') }}" id="guardian_name" autocomplete="true">
                </div>
            </div>
        </div>
    </div>

    <div class="row">

        <div class="col-md-6">
            <div class="form-group">
                <label for="phone">@lang('quickadmin.customers.fields.phone')<small class="text-danger"> (Optional)</small></label>
                <div class="input-group">
                    <input type="text" class="form-control" name="phone" value="{{ isset($customer) ? $customer->phone : old('phone') }}" id="phone" autocomplete="true">
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="phone2">@lang('quickadmin.customers.fields.phone2')<small class="text-danger"> (Optional)</small></label>
                <div class="input-group">
                    <input type="text" class="form-control" name="phone2" value="{{ isset($customer) ? $customer->phone2 : old('phone2') }}" id="phone" autocomplete="true">
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">

            <div class="fullselect2">
                <div class="form-control-inner">
                    <label>@lang('quickadmin.customers.fields.address')</label>
                    <select class="js-example-basic-single @error('address_id') is-invalid @enderror" name="address_id" id="address_id" >
                        <option value="{{ isset($customer) ? $customer->address->id : old('address_id') }}">
                            {{ isset($customer) ? $customer->address->address : trans('quickadmin.customers.select_address') }}
                        </option>
                        @foreach($addresses as $address)
                            @if (!isset($customer) || $customer->address->id !== $address->id)
                            <option value="{{ $address->id }}">{{ $address->address }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-auto ml-auto mt-4">
            <button type="submit" class="btn btn-primary">@lang('quickadmin.qa_submit')</button>
        </div>
    </div>

