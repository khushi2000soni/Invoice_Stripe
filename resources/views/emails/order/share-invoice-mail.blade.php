@extends('emails.layouts.mail')
@section('styles')
  <style>

  </style>
@endsection

@section('email-content')
        @php
            $mailContent  = getSetting('share_invoice_mail_message');

            $mailContent  = str_replace('[PARTY_NAME]',$customer_name,$mailContent);
            $mailContent  = str_replace('[SUPPORT_EMAIL]', config('app.support_email'), $mailContent);
            $mailContent  = str_replace('[SUPPORT_PHONE]', config('app.support_phone'), $mailContent);
            $mailContent  = str_replace('[APP_NAME]', config('app.app_name'), $mailContent);
           // dd($mailContent);
        @endphp

        @if($mailContent)
        {!! $mailContent !!}
        @else
            <tr>
                <td>
                    <p class="mail-title" style="font-size:14px;">
                        <b>Dear</b> {{ $customer_name ?? "" }},
                    </p>
                    <div class="mail-desc">
                        {{-- <p style="font-size:14px;">{{ getSetting('share_invoice_mail_message') }}</p> --}}
                        Please find the attached Invoice Detail Pdf !
                    </div>
                </td>
            </tr>
        @endif

@endsection
