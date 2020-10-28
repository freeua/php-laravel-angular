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
            <td class="title-td">Bestellungen exportiert</td>
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

<table class="mb15" cellpadding="10" width="100%">
    <thead>
    <tr>
        <td class="title" width="15%">
            Bestellung
        </td>
        <td class="title" width="20%">
            Benutzer
        </td>
        <td class="title" width="10%">
            Marke
        </td>
        <td class="title" width="20%">
            Modell
        </td>
        <td class="title" width="20%">
            Lieferant
        </td>
        <td class="title" width="15%">
            Status
        </td>
    </tr>
    </thead>
    <tbody>
    @foreach($orders as $order)
        <tr>
            <td class="data">
                {{$order->number}}
            </td>
            <td class="data">
                {{$order->employeeName}}
            </td>
            <td class="data">
                {{$order->productBrand}}
            </td>
            <td class="data">
                {{$order->productModel}}
            </td>
            <td class="data">
                {{$order->supplierName}}
            </td>
            <td class="data">
                {{$order->status->label}}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

</body>
</html>
