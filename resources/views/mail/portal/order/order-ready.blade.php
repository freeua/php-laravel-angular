<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bestellung bereit</title>

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
                            <h1>Ihr Dienstrad ist abholbereit!</h1>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>Hallo {{$employee->getFullNameAttribute()}},</p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>Ihr Dienstrad ist abholbereit. Der Abholcode lautet: <strong>{{ $order->pickup_code }}</strong>.
                                    Bitte bewahren Sie diese E-Mail mit dem Abholcode gut auf. Den Abholcode kennen nur Sie. Den Abholcode können Sie ebenfalls jederzeit in Ihrem Dienstrad-Portal einsehen.
                                    Geben Sie den Abholcode keinesfalls an Dritte weiter und bringen Sie Ihren Personalausweis zur Feststellung Ihrer Identität zur Abholung mit.</p>
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
