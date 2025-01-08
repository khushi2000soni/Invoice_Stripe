
    <div class="form-group">
      <label>@lang('quickadmin.roles.fields.list.name')</label>
      <div class="input-group">
        <div class="input-group-prepend">
          <div class="input-group-text">
            <i class="fas fa-user"></i>
          </div>
        </div>
        <input type="text" class="form-control phone-number" name="name" value="{{ isset($role) ? $role->name : old('name') }}" id="name" autocomplete="true">
      </div>
    </div>

    <div class="form-group">
        <label>@lang('quickadmin.roles.fields.add-role.givepermit')</label>
        <div class="row">
            @foreach($permissions as $module => $modulePermissions)

            @php
            $moduleName = str_replace('_', ' ', $module); // Replace underscores with spaces
            $moduleName = ucfirst($moduleName); // Capitalize the first letter
            @endphp

            @if (!(auth()->user()->hasRole(config('app.roleid.super_admin'))))
                @if ($module == 'roles')
                    @continue
                @endif
            @endif
                <div class="col-12 col-md-12 col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>{{ $moduleName }}</h4>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <div class="row">
                                    @foreach($modulePermissions as $permission)
                                    <div class="col-md-3">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input permission-checkbox" name="permissions[]" value="{{ $permission->id }}" id="permission{{ $permission->id }}"
                                            @if(isset($role) && $selectedPermissions->contains('id', $permission->id)) checked @endif>
                                            <label class="custom-control-label" for="permission{{ $permission->id }}">{{ $permission->title }}</label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    <button type="submit" class="btn btn-primary">@lang('quickadmin.qa_submit')</button>

