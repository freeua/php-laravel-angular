<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Auto-responder</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link href="https://fonts.googleapis.com/css?family=Ubuntu:400,400i,500,500i,700,700i" rel="stylesheet">
</head>
<body>
<table align="center" border="0" cellspacing="0" cellpadding="0" width="100%">
    <tr>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td></td>
        <td width="602px" align="center">
            <div class="content" style="margin-top: 30px">
                @if(!isset($message))
                    <table align="center" border="0" cellspacing="0" cellpadding="0" width="100%">
                        <tr>
                            <td>
                                <h1>Auto-Responder</h1>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p>Lorem Ipsun</p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <h3>Ihr System-Administrator</h3>
                            </td>
                        </tr>
                    </table>
                @else
                    <table align="center" border="0" cellspacing="0" cellpadding="0" width="100%">
                        <tr>
                            <td>
                                {!! $message !!}
                            </td>
                        </tr>
                    </table>
                @endif
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

                    </td>
                </tr>
            </table>
        </td>
        <td></td>
    </tr>
</table>
</body>
</html>
