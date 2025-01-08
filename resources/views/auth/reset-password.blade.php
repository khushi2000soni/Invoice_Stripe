@extends('layouts.auth')

@section('main-content')
<section class="section">
    <div class="container mt-5">
      <div class="row">
        <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
          <div class="login-brand login-brand-color">
            {{-- <img alt="@lang('quickadmin.qa_company_name')" src="{{asset('admintheme/assets/img/logo.png') }}" height="80" width="80"/> --}}
            <span>{{ getSetting('company_name') ?? ''}}</span>
          </div>
          @if (Session::has('success'))
          <div class="alert alert-success alert-dismissible show fade">
              <div class="alert-body">
                <button class="close" data-dismiss="alert">
                  <span>×</span>
                </button>
                {{ Session::get('success') }}    </div>
          </div>
          @endif

          @if (Session::has('error'))
          <div class="alert alert-danger alert-dismissible show fade">
              <div class="alert-body">
                <button class="close" data-dismiss="alert">
                  <span>×</span>
                </button>
                {{ Session::get('error') }}    </div>
          </div>
          @endif
          <div class="card">
                <div class="card-header card-header-auth">
                <h4>@lang('quickadmin.qa_reset_password')</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{route('reset-new-password')}}">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">
                        <input type="hidden" name="email" value="{{ $email }}" >
                        <div class="form-group">
                            <label for="password">@lang('quickadmin.qa_new_password')</label>
                            <div class="input-group">
                              <div class="input-group-prepend">
                                <div class="input-group-text">
                                  <i class="fas fa-lock"></i>
                                </div>
                              </div>
                              <input type="password" value="{{ old('password') }}" id="password" class="form-control  @error('password') is-invalid @enderror" name="password" tabindex="1"   autofocus>
                              @error('password')
                              <div class="invalid-feedback">
                                {{ $message }}
                              </div>
                              @enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="password-confirm">@lang('quickadmin.qa_confirm_password')</label>
                            <div class="input-group">
                              <div class="input-group-prepend">
                                <div class="input-group-text">
                                  <i class="fas fa-lock"></i>
                                </div>
                              </div>
                              <input type="password" value="{{ old('password_confirmation') }}" id="password-confirm" class="form-control @error('password_confirmation') is-invalid @enderror" name="password_confirmation" tabindex="1"   autofocus>
                              @error('password_confirmation')
                              <div class="invalid-feedback">
                                {{ $message }}
                              </div>
                              @enderror
                            </div>
                        </div>

                        <div class="form-group">
                        <button type="submit" class="btn btn-auth-color btn-lg btn-block" tabindex="4">
                            @lang('quickadmin.qa_reset_password')
                        </button>
                        </div>
                    </form>
                </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  @endsection
