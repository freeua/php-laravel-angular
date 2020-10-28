<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bitte prüfen Sie den Überlassungsvertrag Ihres Mitarbeiters</title>

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
                            <h1>Bitte prüfen Sie den Überlassungsvertrag Ihres Mitarbeiters</h1>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>Hallo {{ $admin->fullName }},</p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>
                                Ihr Mitarbeiter {{$offer->user->fullName}} hat ein Angebot akzeptiert und
                                einen
                                Überlassungsvertrag generiert und im Dienstrad-Portal hochgeladen.
                                Bitte prüfen Sie diesen Überlassungsvertrag. Bei Genehmigung lösen Sie automatisch die
                                Bestellung - im Auftrag und Namen der MLF Mercator-Leasing GmbH & Co. Finanz-KG - des
                                Dienstrads aus. Bei Nichtberechtigung des Mitarbeiters über die Bestellung eines
                                Dienstrads
                                können Sie den Vorgang ablehnen.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td align="center">
                            <p class="action">Unter folgendem Link:</p>
                            <p class="action"><a href="{{$url}}" style="color: {{$styles['color']}}">{{$url}}</a></p>
                            <p class="action">gelangen Sie zu den ausstehenden Genehmigungen.</p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>Dies ist eine automatisch generierte Mail. Bitte antworten Sie daher nicht auf diese
                                Mail.</p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <h3>Ihr System-Administrator</h3>
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
