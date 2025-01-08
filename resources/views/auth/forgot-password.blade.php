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
                <h4>@lang('quickadmin.qa_forgot_password')</h4>
                </div>
                <!-- <center>
                <div class="logo-auth">
                    <img alt="image" src="assets/img/logo.png" />
                </div>
                <div>
                    <span class="logo-name-auth">Grexa</span>
                </div>
                </center> -->
                <div class="card-body">
                <p class="text-muted">@lang('quickadmin.qa_otp_line')</p>
                <form method="POST" action="{{route("password_mail_link")}}">
                    @csrf
                    <div class="form-group">
                        <label for="email">@lang('quickadmin.qa_email')</label>
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <div class="input-group-text">
                              <i class="fas fa-envelope"></i>
                            </div>
                          </div>
                          <input type="email" value="{{ old('email') }}" id="email" class="form-control @error('email') is-invalid @enderror" name="email" tabindex="1"   autofocus>
                          @error('email')
                          <div class="invalid-feedback">
                            {{ $message }}
                          </div>
                          @enderror
                        </div>
                    </div>
                    <div class="form-group">
                    <button type="submit" class="btn btn-lg btn-block btn-auth-color" tabindex="4">
                        @lang('quickadmin.qa_submit')
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
