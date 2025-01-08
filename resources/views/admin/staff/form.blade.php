
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="name">@lang('quickadmin.users.fields.name')</label>
                <div class="input-group">
                    <input type="text" class="form-control" name="name" value="{{ isset($user) ? $user->name : old('name') }}" id="name" autocomplete="true">
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="username">@lang('quickadmin.users.fields.usernameid')</label>
                <div class="input-group">
                    <input type="text" class="form-control" name="username" value="{{ isset($user) ? $user->username : old('username') }}" id="username" autocomplete="true">
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="email">@lang('quickadmin.users.fields.email')</label>
                <div class="input-group">
                    <input type="text" class="form-control" name="email" value="{{ isset($user) ? $user->email : old('email') }}" id="email" autocomplete="true">
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="phone">@lang('quickadmin.users.fields.phone')</label>
                <div class="input-group">
                    <input type="text" class="form-control" name="phone" value="{{ isset($user) ? $user->phone : old('phone') }}" id="phone" autocomplete="true">
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label for="role_id">@lang('quickadmin.users.fields.role')</label>
                <div class="input-group">
                    <select class="form-control @error('role_id') is-invalid @enderror" name="role_id" id="role_id" >
                        <option value="{{ isset($user) ? $user->roles->first()->id : old('role_id') }}">
                            {{ isset($user) ? $user->roles->first()->name : 'Select Role' }}
                        </option>
                        @foreach($roles as $rolee)
                        @if (!isset($user) || $user->roles->first()->id !== $rolee->id)
                            <option value="{{ $rolee->id }}">{{ $rolee->name }}</option>
                        @endif
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
    @isset($user)

    @else
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="password">@lang('quickadmin.qa_new_password')</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <i class="fas fa-lock"></i>
                        </div>
                    </div>
                    <input type="password" value="{{ old('password') }}" id="password" class="form-control @error('password') is-invalid @enderror" name="password" tabindex="1" autofocus>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="password_confirmation">@lang('quickadmin.qa_confirm_password')</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <i class="fas fa-lock"></i>
                        </div>
                    </div>
                    <input type="password" value="{{ old('password_confirmation') }}" id="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" name="password_confirmation" tabindex="1" autofocus>
                </div>
            </div>
        </div>
    </div>
    @endisset


    <div class="row">
        <div class="col-md-6">
            <button type="submit" class="btn btn-primary">@lang('quickadmin.qa_submit')</button>
        </div>
    </div>

