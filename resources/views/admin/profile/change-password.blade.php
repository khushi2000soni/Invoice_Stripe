
@extends('layouts.app')
@section('title')@lang('quickadmin.qa_change_password') @endsection
@section('customCss')
<meta name="csrf-token" content="{{ csrf_token() }}" >
@endsection

@section('main-content')

<section class="section">
    <div class="section-body">
        <div class="row mt-sm-4">
            <div class="col-12 col-md-12 col-lg-8">
                <div class="card">
                    <div class="padding-20">
                        <form method="post" >
                            <div class="card-header">
                            <h4>@lang('quickadmin.qa_change_password') </h4>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="{{route('reset-password')}}">
                                    @csrf
                                    <div class="form-group">
                                        <label for="currentpassword">@lang('quickadmin.qa_current_password')</label>
                                        <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                            <i class="fas fa-lock"></i>
                                            </div>
                                        </div>
                                        <input type="password" value="{{ old('currentpassword') }}" class="form-control  @error('currentpassword') is-invalid @enderror" name="currentpassword" id="currentpassword" tabindex="1" required>
                                            <div class="input-group-append">
                                                <div class="input-group-text toggle-password" data-toggle="#currentpassword">
                                                <i class="fas fa-eye view-password"></i>
                                                <i class="fas fa-eye-slash hide-password" style="display: none;"></i>
                                                </div>
                                            </div>
                                        @error('currentpassword')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="password">@lang('quickadmin.qa_new_password')</label>
                                        <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                            <i class="fas fa-lock"></i>
                                            </div>
                                        </div>
                                            <input type="password" value="{{ old('password') }}" class="form-control  @error('password') is-invalid @enderror" name="password" id="password" tabindex="1" required>
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
                                        <label for="password_confirmation">@lang('quickadmin.qa_confirm_password')</label>
                                        <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                            <i class="fas fa-lock"></i>
                                            </div>
                                        </div>

                                        <input type="password" value="{{ old('password_confirmation') }}" class="form-control  @error('password_confirmation') is-invalid @enderror" name="password_confirmation" id="password_confirmation" required>
                                            <div class="input-group-append">
                                                <div class="input-group-text toggle-password" data-toggle="#password_confirmation">
                                                <i class="fas fa-eye view-password"></i>
                                                <i class="fas fa-eye-slash hide-password" style="display: none;"></i>
                                                </div>
                                            </div>
                                        @error('password_confirmation')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                        </div>
                                    </div>

                                    <div class="form-group">
                                    <button type="submit" class="btn btn-submit-block btn-lg btn-block" tabindex="4">
                                        @lang('quickadmin.qa_change_password')
                                    </button>
                                    </div>
                                </form>
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
