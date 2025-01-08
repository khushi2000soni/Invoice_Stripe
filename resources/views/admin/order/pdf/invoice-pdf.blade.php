<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <style>

            p, td {
                 font-family: freeserif;
                 /* font-family: 'Mangal', sans-serif; */
            }

            /* @page {
            margin-top: 5cm;
            margin-header: 0.5cm;
            margin-footer: 2cm;
            header: page-header;
            footer: page-footer;
            }
            @page :first {
                header: page-header;
            } */

            .header {
            text-align: center;
            /* border-bottom: 1px solid #000; */
            }
            footer{
                position: fixed;
                bottom:-50px;
                left: 0;
                right: 0px;
                height: 50px;
                margin-bottom: 0px;
            }

            .tablebody * {
                box-sizing: border-box;
            }

            /* @page {
                size: A5;
                margin:0 auto;
            } */

        </style>
    </head>
<body  class="tablebody" >
    <header name="page-header" class="header">
        <table  style="max-width: 100%; width: 100%; margin: 0px auto; padding-bottom: 10px;">
            <tbody>
                @if (!is_null(getSetting('invoice_pdf_top_title')))
                <tr>
                    <th colspan="2" style="text-align: center; font-size: 22px; padding-bottom: 20px; font-weight: 400;">{{ getSetting('invoice_pdf_top_title') ?? ''}}</th>
                </tr>
                @endif
                <tr>
                    <td style="font-size: 22px;"><strong>Bills To: {{ str_limit_custom($order->customer->name, 25) }}
                    </strong></td>
                    <td style="font-size: 22px; text-align: right;"><strong>Invoice no : #{{ $order->invoice_number }}</strong>  </td>
                </tr>
                <tr>
                    <td style="font-size: 22px;"><strong>Address : {{ $order->customer->address->address }}</strong> </td>
                    <td style="font-size: 22px; text-align: right;"><strong>Date : {{$order->created_at->format('d-m-Y')}}</strong> </td>
                </tr>
                <tr>
                    <td style="font-size: 22px;"><strong>Phone no: {{ $order->customer->phone ?? 'N/A' }}</strong>  </td>
                    <td style="font-size: 22px; text-align: right;"><strong>Time : {{$order->created_at->format('h:i A')}}</strong>  </td>
                </tr>
            </tbody>
        </table>
    </header>

    @include('admin.order.pdf.main-pdf-table')

    <footer name="page-footer">
    </footer>

</body>
</html>

