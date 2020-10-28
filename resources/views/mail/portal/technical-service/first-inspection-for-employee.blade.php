<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>First inspection for employee</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link href="https://fonts.googleapis.com/css?family=Ubuntu:400,400i,500,500i,700,700i" rel="stylesheet">
</head>
<body>
<table align="center" border="0" cellspacing="0" cellpadding="0" width="100%">
    <tr>
        <td></td>
        <td width="602px" align="center">
            <a href="#" style="display: inline-block; margin: 40px auto;">
                <img src="{{ $logoUrl }}" alt="logo" border="0">
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
                            <p>Lieber {{ $user->fullName }},</p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>Sie haben Ihr Fahrrad vor 6 Monaten abgeholt und haben die Inspektionsmodalität gewählt, damit Sie zum Lieferanten Ihres Fahrrads gehen und eine Inspektion durchführen können.</p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>Um dies zu tun, benötigen Sie einen Code, den Sie in unserem System erhalten, indem Sie auf den untenstehenden Link klicken.</p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a href="{{$url}}" style="color: {{$styles['color']}}">{{$url}}</a>
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
