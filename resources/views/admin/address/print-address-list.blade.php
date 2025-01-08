<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>@lang('quickadmin.address.fields.list-title')</title>
</head>
<body style="margin: 0;font-family: Arial, Helvetica, sans-serif;" class="">

    <div class="page-header">
		<header style="padding: 1px 0;">
			<h2 style="margin: 0;color: #2a2a33;font-size: 20px;font-weight: bold; text-align:center;"><strong>@lang('quickadmin.address.fields.list-title')</strong></h2>
		</header>
    </div>
        {{-- <footer>
            <div class="pagenum-container"><small>Page <span class="pagenum"></span></small></div>
        </footer> --}}
		<main class="main" style="width:100%; max-width: 100%; margin: 0 auto;padding: 40px 0;padding-top: 20px;">
			<table cellpadding="0" cellspacing="0" width="100%" style="color: #000;font-size: 16px;">
				<thead>
					<tr>
                        <th style="padding: 10px;border: 1px solid #000;border-right: none;" align="left">@lang('quickadmin.qa_sn')</th>
						<th style="padding: 10px;border: 1px solid #000;border-right: none;" align="center">@lang('quickadmin.address.fields.list.address')</th>
						<th style="padding: 10px;border: 1px solid #000;" align="center">@lang('quickadmin.address.fields.list.no_of_customer')</th>
					</tr>
				</thead>
				<tbody>
					@forelse ($addresses as $key => $address)
				    <tr>
                        <td style="padding: 10px;border: 1px solid #000;border-right: none;border-top: none;" align="left">{{ $key + 1 }}</td>
						<td style="padding: 10px;border: 1px solid #000;border-right: none;border-top: none;" align="center">{{ $address->address }}</td>
						<td style="padding: 10px;border: 1px solid #000;border-top: none;" align="center">{{ $address->customers->count() ?? 0 }}</td>
					</tr>
                    @empty
                    <tr>
                        <td colspan="4">No Record Found!</td>
                    </tr>
                    @endforelse
				</tbody>

			</table>

		</main>

</body>
</html>
