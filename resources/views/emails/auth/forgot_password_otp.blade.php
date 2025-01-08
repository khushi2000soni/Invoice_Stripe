@extends('emails.layouts.mail')
@section('styles')
  <style>
    .reset-password-btn{
        box-sizing:border-box;
        font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif,'Apple Color Emoji','Segoe UI Emoji','Segoe UI Symbol';
        border-radius:4px;
        color:#fff;
        display:inline-block;
        overflow:hidden;
        text-decoration:none;
        background-color:#f34d03;
        border-bottom:8px solid #f34d03;border-left:18px solid #f34d03;border-right:18px solid #f34d03;border-top:8px solid #f34d03;
    }
  </style>
@endsection

@section('email-content')
    <tr>
        <td>
            <p class="mail-title" style="font-size:14px;">
                <b>Hello</b> {{ $user->name ?? "" }},
            </p>
            <div class="mail-desc">
                <p style="font-size:14px;">We received a request to reset your password. Please use the following OTP to proceed:</p>
                <p>Your OTP: {{ $user->otp }}</p>

                <p>This OTP will expire in {{ $user->expiretime}}. If you did not request a password reset, please ignore this email.</p>
            </div>
        </td>
        <tr>
            <td>
                <p style="font-size:14px;">If you did not request a password reset, no further action is required.</p>
            </td>
        </tr>
    </tr>
@endsection
