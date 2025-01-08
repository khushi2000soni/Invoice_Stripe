@extends('layouts.auth')

@section('main-content')
<section class="section">
    <div class="container mt-5">
      <div class="row">
        <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
          <div class="login-brand login-brand-color">
             {{-- <img alt="@lang('quickadmin.qa_company_name')" src="{{ asset('admintheme/assets/img/logo.png') }}" height="80" width="80"/> --}}
             <span>{{ getSetting('company_name') ?? ''}}</span>
          </div>
          @if (Session::has('success'))
          <div class="alert alert-success alert-dismissible show fade">
              <div class="alert-body">
                <button class="close" data-dismiss="alert">
                  <span>×</span>
                </button>
                {{ Session::get('success') }}
                </div>
          </div>
          @endif
          @error('wrongcrendials')
            <div class="alert alert-danger alert-dismissible show fade">
                <div class="alert-body">
                <button class="close" data-dismiss="alert">
                    <span>×</span>
                </button>
                {{ $message }}
                </div>
            </div>
            @enderror
          <div class="card">
            <div class="card-header card-header-auth">
              <h4>@lang('quickadmin.qa_login')</h4>
            </div>
            <div class="card-body">
              <form method="POST" action="{{route("authenticate")}}"  novalidate="">
                @csrf

                <div class="form-group">
                    <label for="username">@lang('quickadmin.qa_username')</label>
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <div class="input-group-text">
                          <i class="fas fa-user"></i>
                        </div>
                      </div>
                      <input type="text" value="{{ old('username') }}" class="form-control @error('username') is-invalid @enderror" name="username" tabindex="1" required autofocus autocomplete="off">
                      @error('username')
                      <div class="invalid-feedback">
                        {{ $message }}
                      </div>
                      @enderror
                    </div>
                </div>
                <div class="form-group">
                    <label for="password" class="control-label">@lang('quickadmin.qa_password')</label>
                    <div class="float-right">
                        <a href="{{route('forgot.password')}}" class="text-small">
                            @lang('quickadmin.qa_forgot_password')
                        </a>
                    </div>
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <div class="input-group-text">
                          <i class="fas fa-lock"></i>
                        </div>
                      </div>
                      <input type="password" class="form-control  @error('password') is-invalid @enderror" name="password" id="password" tabindex="2" required autocomplete="off">
                      <div class="input-group-append">
                        <div class="input-group-text toggle-password" data-toggle="#password">
                          <i class="fas fa-eye view-password"></i>
                          <i class="fas fa-eye-slash hide-password" style="display: none;"></i>
                        </div>
                      </div>

                        @error('password')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                      <input type="checkbox" name="remember_me" class="custom-control-input" tabindex="3" id="remember-me">
                      <label class="custom-control-label" for="remember-me">@lang('quickadmin.qa_remember_me')</label>
                    </div>
                </div>
                <div class="form-group">
                  <button type="submit" class="btn btn-lg btn-block btn-auth-color" tabindex="4">
                    @lang('quickadmin.qa_login')
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

@section('customJS')
  <script type="text/javascript">
    $(".toggle-password").click(function() {
        var inputId = $(this).data("toggle");
        var input = $(inputId);
        var viewPasswordIcon = $(this).find(".view-password");
        var hidePasswordIcon = $(this).find(".hide-password");

        if (input.attr("type") === "password") {
            input.attr("type", "text");
            viewPasswordIcon.hide();
            hidePasswordIcon.show();
        } else {
            input.attr("type", "password");
            viewPasswordIcon.show();
            hidePasswordIcon.hide();
        }
    });

   </script>
@endsection
