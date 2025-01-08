
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="name">@lang('quickadmin.device.fields.name')</label>
            <div class="input-group">
                <input type="text" class="form-control" name="name" value="{{ isset($device) ? $device->name : old('name') }}" id="name" autocomplete="true">
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label for="staff_id">@lang('quickadmin.device.fields.staff_name')</label>
            <div class="input-group">
                <select class="form-control @error('staff_id') is-invalid @enderror" name="staff_id" id="staff_id">
                    <option value="{{ isset($device) ? $device->staff->id : old('staff_id') }}">
                        {{ isset($device) ? $device->staff->name : trans('quickadmin.device.select_staff') }}
                    </option>
                    @foreach($staffs as $staff)
                        @if(!isset($device) || $device->staff->id !== $staff->id)
                        <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <label for="device_ip">@lang('quickadmin.device.fields.device_ip')</label>
            <div class="input-group">
                <input type="text" class="form-control" name="device_ip" value="{{ isset($device) ? $device->device_ip : old('device_ip') }}" id="device_ip" autocomplete="true">
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="device_id">@lang('quickadmin.device.fields.device_id')</label>
            <div class="input-group">
                <input type="text" class="form-control" name="device_id" value="{{ isset($device) ? $device->device_id : old('device_id') }}" id="device_id" autocomplete="true">
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label for="pin">@lang('quickadmin.device.fields.pin')</label>
            <div class="input-group">
                <input type="numeric" class="form-control" name="pin" value="{{ isset($device) ? $device->pin : old('pin') }}" id="pin" autocomplete="true">
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <button type="submit" class="btn btn-primary">@lang('quickadmin.qa_submit')</button>
    </div>
</div>

