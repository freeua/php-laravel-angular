<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Neuer Vertrag</title>

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
                            <h1>Neuer Vertrag</h1>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            @if ($notifiable->isSupplier())
                                <p style="margin: 15px 0 30px; text-align: center; font-weight: 500;">Der Vertrag #{{ $contract->number }} mit dem Benutzer #{{ $contract->user->code }} hat begonnen.</p>
                            @else
                                <p>Ein neuer Vertrag wurde in <a href="{{ $domainUrl }}" style="color: {{$styles['color']}}">{{ $domain }}</a> gestartet.</p>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td align="center">
                            <a href="{{ $url }}"
                               style="display: block; margin: 0 auto 20px; text-decoration: none; color:#fff; background: {{ $styles['color'] }}; height: 50px; width: 199px;    border-radius: 3px; line-height: 50px; font-size: 16px;    font-weight: bold; text-align: center;">Siehe Einzelheiten</a>
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
