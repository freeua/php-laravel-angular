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
            <td class="title-td">Angebote exportiert</td>
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
            <td class="title" width="10%">
                Angebot
            </td>
            <td class="title" width="20%">
                Benutzer
            </td>
            <td class="title" width="10%">
                Marke
            </td>
            <td class="title" width="10%">
                Modell
            </td>
            <td class="title" width="10%">
                Preis
            </td>
            <td class="title" width="20%">
                Lieferant
            </td>
            <td class="title" width="10%">
                Status
            </td>
        </tr>
    </thead>
    <tbody>
        @foreach($offers as $offer)
            <tr>
                <td class="data">
                    {{$offer->number}}
                </td>
                <td class="data">
                    {{$offer->employeeName}}
                </td>
                <td class="data">
                    {{$offer->productBrand}}
                </td>
                <td class="data">
                    {{$offer->productModel}}
                </td>
                <td class="data">
                    {{\Brick\Money\Money::of($offer->productListPrice, 'EUR', null, RoundingMode::HALF_UP)->formatTo('de') }}
                </td>
                <td class="data">
                    {{$offer->supplierName}}
                </td>
                <td class="data">
                    {{$offer->status->label}}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
