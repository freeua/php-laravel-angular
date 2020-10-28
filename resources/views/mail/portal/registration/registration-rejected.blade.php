<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Leider war Ihre Registrierung im Dienstrad-Portal nicht erfolgreich!</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link href="https://fonts.googleapis.com/css?family=Ubuntu:400,400i,500,500i,700,700i" rel="stylesheet">
    <style>
        a {
            color: {{$styles['color']}};
            text-decoration: none;
        }
    </style>
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
                            <h1>Leider war Ihre Registrierung im Dienstrad-Portal nicht erfolgreich!</h1>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>Hallo {{$user->fullName}},</p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>Ihre Registrierung zum Dienstrad-Programm war leider nicht erfolgreich.
                                Bei weiteren Fragen setzen Sie sich bitte mit dem zuständigen Ansprechpartner für das Dienstrad-Programm in Ihrem Unternehmen in Verbindung.</p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>Dies ist eine automatisch generierte Mail. Bitte antworten Sie daher nicht auf diese Mail.</p>
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
