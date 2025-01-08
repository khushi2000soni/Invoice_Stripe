
<div class="form-group">
    <label>@lang('quickadmin.address.fields.list.address')</label>

    <div class="input-group">
      <div class="input-group-prepend">
        <div class="input-group-text">
          <i class="fas fa-map-marker-alt"></i>
        </div>
      </div>
      <input type="text" class="form-control phone-number " name="address" value="{{ isset($address) ? $address->address : old('address') }}" id="address" autocomplete="true">
    </div>
</div>

<button type="submit" class="btn btn-primary">@lang('quickadmin.qa_submit')</button>

