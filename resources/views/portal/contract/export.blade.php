<!--suppress ALL -->
<html>
<head>
    <link href="{{ URL::asset('css/export-pdf.css') }}" rel="stylesheet" type="text/css">
</head>
<body>
<table id="title-table" cellpadding="0" cellspacing="0">
    <tbody>
        <tr>
            <td id="title-table-empty-td"></td>
            <td class="title-td">Verträge exportiert</td>
            <td class="td3"></td>
            <td class="company-logo-td">
                @if ($user->company->logo)
                    <img class="company-logo"
                         src="data:image/*;base64, {{ base64_encode(\App\Helpers\StorageHelper::getFromDisk($user->company->logo, \App\Helpers\StorageHelper::PUBLIC_DISK))}}">
                @else
                    <img class="company-logo" src="{{ URL::asset('img/logo/company-logo.png') }}">
                @endif
            </td>
        </tr>
    </tbody>
</table>

<table class="mb15" cellpadding="10">
    <thead>
        <tr>
            <td class="title" width="8%">
                Vertrag
            </td>
            <td class="title" width="12%">
                Startdatum
            </td>
            <td class="title" width="12%">
                Enddatum
            </td>
            <td class="title" width="18%">
                Mitarbeiter
            </td>
            <td class="title" width="10%">
                Leasingrate
            </td>
            <td class="title" width="20%">
                Marke
            </td>
            <td class="title" width="20%">
                Modell
            </td>
            <td class="title" width="10%">
                Status
            </td>
        </tr>
    </thead>
    <tbody>
        @foreach($contracts as $contract)
            <tr>
                <td class="data">
                    {{$contract->number}}
                </td>
                <td class="data">
                    {{substr($contract->start_date,0,10)}}
                </td>
                <td class="data">
                    {{substr($contract->end_date,0,10)}}
                </td>
                <td class="data">
                    {{$contract->employeeName}}
                </td>
                <td class="data">
                    {{$contract->leasing_rate ? \Brick\Money\Money::of($contract->leasing_rate, 'EUR', null, RoundingMode::HALF_UP)->formatTo('de') : \Brick\Money\Money::zero('EUR')->formatTo('de')}}
                </td>
                <td class="data">
                    {{$contract->productBrand}}
                </td>
                <td class="data">
                    {{$contract->productModel}}
                </td>
                <td class="data">
                    {{$contract->status->label}}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
