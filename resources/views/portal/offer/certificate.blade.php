<!--suppress ALL -->
<html>
<head>
    <link href="css/contract-pdf.css" rel="stylesheet" type="text/css">
</head>
<body>
<table style="margin-bottom: 15px">
    <tr>
        <td class="td-color header-warning">
           Das Dokument wir digital erzeugt und elektronisch übermittelt. Die Eingabe des Abholcodes ersetzt die Unterschrift 
        </td>
    </tr>
</table>
<table style="margin-bottom: 15px">
    <tbody>
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
    </tbody>
</table>
<table>
    <tbody>
        <tr>
            <td class="label">Angebotsnummer</td>
            <td class="field-value" style="width: 300px;">{{$offer_number}}</td>
        </tr>
    </tbody>
</table>
<table>
    <tbody>
        <tr>
            <td class="label">Auftragsnummer</td>
            <td class="field-value" style="width:300px;">{{$order_number}}</td>
        </tr>
    </tbody>
</table>

<h2>Übernahmebestätigung</h2>
<h3 class="subtitle">Leasingnehmer</h3>
<table cellpadding="0" cellspacing="0" class="field-value-table">
    <tr>
        <td class="label">Leasingnehmer/Firma:</td>
        <td class="field-value" style="width: 300px;">{{$company['name']}}</td>
    </tr>
    <tr>
        <td class="label">Straße, Nr.</td>
        <td class="field-value" style="width:300px;">{{$company['address']}}</td>
    </tr>
    <tr>
        <td class="label">PLZ, ORT</td>
        <td class="field-value" style="width: 300px;">{{$company['city']}} {{$company['zip']}}</td>
    </tr>
    <tr>
        <td class="label">Mitarbeiter/-in</td>
        <td class="field-value" style="width: 300px;">{{$employee_name}}</td>
    </tr>
</table>

<h3 class="subtitle">Leasingobjekt</h3>
<table class="field-value-table" >
    <tr>
        <td class="label">Marke:</td>
        <td class="field-value" style="width: 300px;">{{$brand}}</td>
    </tr>
    <tr>
        <td class="label">Modell:</td>
        <td class="field-value" style="width: 300px;">{{$model}}</td>
    </tr>
    <tr>
        <td class="label"><strong>Rahmen-Nr.</strong> </td>
    <td class="field-value" style="width: 300px;"><strong>{{$frameNumber}}</strong></td>
    </tr>
</table>
<table class="field-value-table" style="margin-bottom: 15px;">
    
    <tr>
        <td class="label"><strong>Datum der Übernahme</strong></td>
        <td class="field-value"><strong>{{$pickupDate}}</strong></td>
    </tr>
</table>

<table cellpadding="0" cellspacing="0">
    <tbody>
    <tr>
        <td>
            <p class="p-with-mb signature-text">
                Das Leasingobjekt wurde am oben genannten Übergabetag durch den Lieferanten ausgeliefert. Ich habe es gründlich
                überprüft. Es ist betriebsbereit, mängelfrei und befindet sich in einem ordnungsgemäßen und funktionsfähigen Zustand.
                Das Leasingobjekt entspricht den vertraglichen Vereinbarungen.
            </p><br>

            <p class="p-with-mb signature-text">
                Mit Übergabe des Abholcodes an den Lieferanten oder Eingabe des Abholcodes in dieses Dokument wird gleichzeitig der Leasinggeber beauftragt, den
Kaufpreis bei Fälligkeit an den Lieferanten zu zahlen.
            </p><br>

            <p class="p-with-mb bold signature-text">
                Hinweis:
            </p><br>

            <p class="p-with-mb bold signature-text">
                Unzutreffende Angaben in der Übernahmebestätigung – insbesondere die Unterzeichnung der Übernahmebestätigung vor
                tatsächlicher Übernahme es Leasingobjekt – sind vertragswidrig und können zu Schadenersatzforderungen des
                Leasinggebers gegen den Leasingnehmer führen.
            </p><br>
            <br>
        </td>
    </tr>
    </tbody>
</table>
<table>
    <tbody>
    <tr><td class="field-value">{{$date}}</td></tr>
        <tr><td class="tr5 td0">Ort, Datum</td></tr>
    <tr>
        <td class="field-value">{{ $pickupCode }}</td>
    </tr>
    </tbody>
</table>
<p>Abholcode durch Mitarbeiter im Auftrag des Leasingnehmers</p>

<table style="margin-top: 15px;">
    <tr>
        <td>
            <p class="p-with-mb signature-text">
                Hiermit bestätigen wir die Identität des/der abholenden und unterschreibenden Mitarbeiters/in nach Überprüfung des Personalausweises oder Reisepasses.
            </p>
        </td>
    </tr>
</table>
<table class="field-value-table">
    <tr>
        <td class="label">Ausstellungsdatum </td>
    <td class="field-value">{{$idIssueDate}}</td>
    </tr>
    <tr>
        <td class="label">Ausstellende Behörde</td>
        <td class="field-value">{{$idAuthority}}</td>
    </tr>
</table>
<table>
    <tbody>
    <tr><td class="field-value">{{$date}}</td></tr>
    <tr><td class="tr5 td0">Ort, Datum</td></tr>
    <tr>
        <td class="field-value">{{ $supplierName }}</td>
    </tr>
</tbody>
</table>

<htmlpagefooter name="footer" id="footer">
    <table cellpadding="0" cellspacing="0" id="footer-table">
        <tbody>
            <tr>
                <td rowspan="2" class="contact-td">
                    <p class="p1 ft11">
                        {{ $company['name'] }}
                        @if ($company['address'])
                            <span class="delimiter"> · </span>
                            {{ $company['address'] }}
                        @endif
                        <span class="delimiter"> · </span>
                        @if ($company['zip']) {{ $company['zip'] }} @endif
                        {{ $company['city'] }}
                        @if ($company['phone'])
                            <span class="delimiter"> · </span>{{ $company['phone'] }}
                        @endif
                    </p>
                </td>
                <td class="tr1 page-number-date">
                    <p class="page-number-p">Seite {PAGENO}/{nbpg}</p>
                </td>
            </tr>
            <tr>
                <td class="date-td page-number-date">
                    <p class="date-p">Stand: {{ $date }}</p>
                </td>
            </tr>
        </tbody>
    </table>
</htmlpagefooter>
</body>
</html>
