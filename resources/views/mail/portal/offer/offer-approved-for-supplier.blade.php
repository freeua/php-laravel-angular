<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Angebot genehmigt</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link href="https://fonts.googleapis.com/css?family=Ubuntu:400,400i,500,500i,700,700i" rel="stylesheet">
</head>
<body>
<table align="center" border="0" cellspacing="0" cellpadding="0" width="100%">
    <tr>
        <td></td>
        <td width="602px" align="center">
            <a href="#" style="display: inline-block; margin: 40px auto;">
                <img src="{{ URL::asset($styles['logo']) }}" alt="logo" border="0">
            </a>
        </td>
        <td></td>
    </tr>
    <tr>
        <td></td>
        <td width="602px" align="center">
            <div class="content">
                <table align="center" border="0" cellspacing="0" cellpadding="0" width="100%">
                    <tr>
                        <td>
                            <h1>Bestellung</h1>
                        </td>
                    </tr>
                    <tr align="left">
                        <td>
                            <p>Sehr geehrte Damen und Herren,</p>
                            <br>
                            <p>
                                hiermit bestellen wir im Namen und auf Rechnung der <strong>
                                    <br>
                                    <br>
                                    MLF Mercator-Leasing GmbH & Co. Finanz-KG</strong>
                                <br>
                                Londonstr. 1
                                <br>
                                97424 Schweinfurt
                            </p><br>
                            <p>folgendes Leasingobjekt:
                            </p><br>
                            <p>
                                <strong>Angebotsnummer:</strong> {{$offer->number}}
                            </p>
                            <p>
                                <strong>Bestellnummer:</strong> {{$offer->order->number}}
                            </p>
                        </td>
                    </tr>
                </table><br>
                <table  align="center" valign="top" border="0" cellspacing="0" cellpadding="0" width="100%">
                    <tr>
                        <td valign="top">
                            <p>
                                Leasingnehmer:
                            </p>
                        </td>
                        <td valign="top">
                            <p>{{$offer->company->name}}<br>
                                {{$offer->company->address}}
                                <br>
                            {{$offer->company->zip}} {{$offer->company->city->name}}
                            </p>
                        </td>
                    </tr>
                </table><br>
                <table  align="center" border="0" cellspacing="0" cellpadding="0" width="100%">
                    <tr>
                        <td>
                            <p>
                            Mitarbeiter: {{$offer->user->first_name}} {{$offer->user->last_name}}
                            </p>
                            <br>
                            <p>
                               Kategorie: {{$offer->productCategory->name}}
                            </p>
                            <p>
                            Marke: {{$offer->productBrand}}
                            </p>
                            <p>
                            Modell: {{$offer->productModel}}
                            </p>
                            <p>
                            Größe: {{$offer->productSize}}
                            </p>
                            <p>
                                Farbe: {{$offer->productColor}}
                            </p><br>
                            <p>
                            Zubehör:
                                @if(empty($offer->notes))
                                    Es wurde kein Zubehör hinzugefügt
                                    @else
                                    @foreach($offer->accessories as $accessory)
                                    <br> - {{$accessory->amount}}x {{$accessory->name}}
                                    @endforeach
                                @endif

                            </p><br>
                            <p>
                            Kaufpreis (netto): {{$netPriceWithAccessories}}
                            </p>
                            <p>
                                zzgl. 19% Mehrwertsteuer: {{$vatApplied}}
                            </p><br>
                            <p>
                                Gesamtpreis (brutto): {{$grossPriceWithAccessories}}
                            </p><br>
                            <p>
                                Nach ordnungsgemäßer Auslieferung, Übernahme des Objektes durch den Mitarbeiter und Bestätigung der Übernahme mittels Abholcode und Einreichung einer ordnungsgemäß auf MLF Mercator-Leasing GmbH & Co. Finanz-KG ausgestellten Rechnung erhalten Sie die Kaufpreiszahlung. Die Rechnung können Sie auch elektronisch im Dienstrad-Portal hochladen.
                            </p><br>
                            <p>
                                Mit freundlichen Grüßen
                            </p>
                            <p>
                                {{$portalName}}
                            </p><br><br>
                            <p>
                                Diese Bestellung wurde digital erstellt und bedarf keiner Unterschrift.
                            </p><br>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>Dies ist eine automatisch generierte Mail. Bitte antworten Sie daher nicht auf diese Mail.</p>
                        </td>
                    </tr>
                </table>
            </div>
        </td>
        <td></td>
    </tr>
    <tr class="footer">
        <td></td>
        <td width="602px" align="center">
            <table align="center" border="0" cellspacing="0" cellpadding="0" width="100%">
                <tr>
                    <td align="center">
                        <a href="#">{{ App\Helpers\PortalHelper::name() }} {{date('Y')}} . All Rights Reserved</a>
                    </td>
                </tr>
            </table>
        </td>
        <td></td>
    </tr>
</table>
</body>
</html>
