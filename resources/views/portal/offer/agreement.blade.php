<!--suppress ALL -->
<html>
<head>
    <link href="css/contract-pdf.css" rel="stylesheet" type="text/css">
</head>
<body>
<table style="margin-bottom: 10px;">
    <tr>
        <td>
            @if ($companyLogo)
                <img class="company-logo"
                     src="{{ $companyLogo}}">
            @else
                <img class="company-logo"
                     src="img/logo/company-logo.png">
            @endif
        </td>
        <td style="width: 200px;"></td>
        <td class="company-logo-td">
            @if ($logo)
                <img class="company-logo"
                     src="{{$logo}}">
            @else
                <img class="company-logo"
                     src="img/logo/portal-logo.png">
            @endif
        </td>
    </tr>
</table>
<table id="contract-title-table" cellspacing="0" cellpadding="0" style="margin-bottom">
        <tr>
            <td id="contract-title-table-empty-td"></td>
            <td class="contract-title-td">Einzel-Leasingantrag</td>
        </tr>
    </tbody>
</table>
<table> <tr>
        <td class="label">Auftrags-Nr.</td>
        <td class="field-value">{{$orderNumber}}</td>
    </tr></table>
<p>Hiermit beantragen wir:</p>
<table class="field-value-table">
    <tbody>
       
    <tr>
        <td class="label">Firma:</td>
        <td class="field-value">{{$company['name']}}</td>
    </tr>
    <tr>
        <td class="label">Straße, Nr.:</td>
        <td class="field-value">{{$company['address']}}</td>
    </tr>
    <tr>
        <td class="label">PLZ Ort:</td>
        <td class="field-value">{{$company['PLZOrt']}}</td>
    </tr>
    </tbody>
</table>
<p >
    verbindlich einen Einzel-Leasingvertrag bei <strong>MLF Mercator-Leasing GmbH & Co. Finanz-KG</strong>
    auf Grundlage des geschlossenen Leasing-Rahmenvertrages, für folgendes Leasingobjekt:
</p>

<table class="field-value-table"> 
    <tbody>
    <tr>
        <td class="label">Marke:</td>
        <td class="field-value">{{$product['brand']}}</td>
    </tr>
    <tr>
        <td class="label">Modell:</td>
        <td class="field-value">{{$product['model']}}</td>
    </tr>
    <tr>
        <td class="label">Größe:</td>
        <td class="field-value">{{$product['size']}}</td>
    </tr>
    <tr>
        <td class="label">Farbe:</td>
        <td class="field-value">{{$product['color']}}</td>
    </tr>
    <tr>
        <td class="label">Zubehör:</td>
        <td class="field-value">{{$contract['notes']}}</td>
    </tr>
    <tr>
        <td class="label">Kategorie:</td>
        <td class="field-value">{{$product['type']}}</td>
    </tr>
    </tbody>
</table>
<p>Für folgende/n <strong>Mitarbeiter/in</strong>:</p>
<table class="field-value-table">
    <tbody>
    <tr>
        <td class="label">Anrede:</td>
        <td class="field-value">{{$employee['salutation'] == 'herr' ? 'Herr' : 'Frau' }}</td>
    </tr>
    <tr>
        <td class="label">Vorname, Nachname:</td>
        <td class="field-value">{{$employee['name']}}</td>
    </tr>
    <tr>
        <td class="label">Straße, Nr.:</td>
        <td class="field-value">{{$employee['street']}}</td>
    </tr>
    <tr>
        <td class="label">PLZ Ort:</td>
        <td class="field-value">{{$employee['PLZOrt']}}</td>
    </tr>
    <tr>
        <td class="label">Telefon:</td>
        <td class="field-value">{{$employee['phone']}}</td>
    </tr>
    <tr>
        <td class="label">E-Mail:</td>
        <td class="field-value">{{$employee['email']}}</td>
    </tr>
    <tr>
        <td class="label">Personal-Nr.:</td>
        <td class="field-value">{{$employee['employee_number']}}</td>
    </tr>
    </tbody>
</table>

<p><strong>Lieferant</strong>:</p>

<table class="field-value-table">
    <tbody>
    <tr>
        <td class="label">Fachhändler:</td>
        <td class="field-value">{{$supplier['name']}}</td>
    </tr>
    <tr>
        <td class="label">Straße, Nr.:</td>
        <td class="field-value">{{$supplier['address']}}</td>
    </tr>
    <tr>
        <td class="label">PLZ Ort:</td>
        <td class="field-value">{{$supplier['PLZOrt']}}</td>
    </tr>
    </tbody>
</table>

<p><strong>Leasingbedingungen</strong>:</p>

<table class="field-value-table">
    <tbody>
    <tr>
        <td class="label">Vereinbarter Kaufpreis:</td>
        <td class="field-value">{{$contract['price']}}</td>
    </tr>
    <tr>
        <td class="label">Grundlaufzeit:</td>
        <td class="field-value">{{$contract['period']}} Monate</td>
    </tr>
    <tr>
        <td class="label">Monatliche Leasingrate:</td>
        <td class="field-value">{{$contract['leasing_rate']}}</td>
    </tr>
    <br>
    <tr>
        <td class="label">Monatliche Versicherungsrate:</td>
        <td class="field-value">{{$contract['insurance_rate']}}</td>
    </tr>
    <tr>
        <td class="label">Monatliche Servicerate:</td>
        <td class="field-value">{{$contract['service_rate']}}</td>
    </tr>
    <tr>
        <td class="label bold">Monatliche Gesamtrate:</td>
        <td class="field-value bold">{{$contract['total_rate']}}</td>
    </tr>
    <tr>
        <td class="label">Kalkulierter Restwert:</td>
        <td class="field-value">{{$contract['residual']}}</td>
    </tr>
    </tbody>
</table>
<p>(alle Beträge verstehen sich zuzüglich gesetzlich gültiger MwSt.)</p>

<table cellpadding="0" cellspacing="0">
    <tbody>
    <tr>
        <td class="field-value">{{ $signatures['user']['date'] }}</td>
    </tr>
    <tr>
        <td class="tr5 td0">Ort, Datum</td>
    </tr>
    </tbody>
</table>
<table cellpadding="0" cellspacing="0">
    <tr>
        <td class="field-value">{{ $signatures['user']['name'] }}</td>
    </tr>
</table>
<p class="signature-content">Dieser Leasingantrag wurde durch den Mitarbeiter rechtsverbindlich digital für den Arbeitgeber erzeugt und ist auch ohne Unterschrift rechtsgültig</p>

<htmlpagefooter name="footer" id="footer">
    <table cellpadding="0" cellspacing="0" id="footer-table">
        <tbody>
            <tr>
                <td rowspan="2" class="contact-td">
                    <p class="p1 ft11">
                        {{ $footer['name'] }}
                        @if ($footer['address'])
                            <span class="delimiter"> · </span>
                            {{ $footer['address'] }}
                        @endif
                        <span class="delimiter"> · </span>
                        @if ($footer['zip']) {{ $footer['zip'] }} @endif
                        {{ $footer['city'] }}
                        @if ($footer['phone'])
                            <span class="delimiter"> · </span>{{ $footer['phone'] }}
                        @endif
                    </p>
                </td>
                <td class="tr1 page-number-date">
                    <p class="page-number-p">Seite {PAGENO}/{nbpg}</p>
                </td>
            </tr>
            <tr>
                <td class="date-td page-number-date">
                    <p class="date-p">Stand: {{ $footer['date'] }}</p>
                </td>
            </tr>
        </tbody>
    </table>
</htmlpagefooter>
</body>
</html>
