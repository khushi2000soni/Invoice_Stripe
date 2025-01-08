
@extends('layouts.app')
@section('title')@lang('quickadmin.roles.fields.role_detail') @endsection
@section('customCss')
<meta name="csrf-token" content="{{ csrf_token() }}" >
@endsection

@section('main-content')

<section class="section">
    <div class="section-body">
      <div class="row">
        <div class="col-12 col-md-12 col-lg-12">
          <div class="card">
            <div class="card-body">
            <form >
                <div class="form-group">
                    <label>@lang('quickadmin.roles.fields.list.name')</label>
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <div class="input-group-text">
                          <i class="fas fa-user"></i>
                        </div>
                      </div>
                      <input type="text" class="form-control phone-number" readonly name="name" value="{{ isset($role) ? $role->name : old('name') }}" id="name" autocomplete="true">
                    </div>
                </div>

                <div class="form-group">
                    <label>@lang('quickadmin.permissions.allow_permissions')</label>
                    @foreach($groupedPermissions as $moduleName => $permissions)
                    @php
                    $moduleName = str_replace('_', ' ', $moduleName); // Replace underscores with spaces
                    $moduleName = ucfirst($moduleName); // Capitalize the first letter
                    @endphp

                    @if (!(auth()->user()->hasRole(config('app.roleid.super_admin'))))
                        @if ($moduleName == 'roles')
                            @continue
                        @endif
                    @endif
                        <div class="card">
                            <div class="card-header">
                                <h4>{{ $moduleName }}</h4>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <div class="row">
                                        @foreach($permissions as $permission)
                                            <div class="col-md-3">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input"  checked  readonly>
                                                    <label class="custom-control-label" for="permission{{ $permission->id }}">{{ $permission->title }}</label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>



@endsection

@section('customJS')


@endsection
