@extends('emails.layouts.mail')
@section('styles')
  <style>

  </style>
@endsection

@section('email-content')
    <tr>
        <td>
            {{-- <p class="mail-title" style="font-size:14px;">
                <b>Dear</b> Owner,
            </p> --}}
            <div class="mail-desc">
                {{-- <p style="font-size:14px;">{{ getSetting('share_invoice_mail_message') }}</p> --}}
                Please find the attached Backup File !
            </div>
        </td>
    </tr>
@endsection
