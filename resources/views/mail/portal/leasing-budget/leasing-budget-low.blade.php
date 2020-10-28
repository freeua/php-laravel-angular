<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vertrag erstellt</title>

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
                            <h1>Leasing budget low!</h1>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>{{ $company->name }} leasing budget is low:</p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>This is a reminder to contact your Portal Administrator to request an increase. If no increase is required, please disregard this message.</p>
                            <p>
                                Alloted Leasing Budget: {{ number_format($company->leasing_budget, 2, ',', '.') }}€<br>
                                Leasing budget used: {{ number_format($company->acceptedOffers()->sum('product_discounted_price'), 2, ',', '.') }}€<br>
                                Leasing budget remaining: {{ number_format($company->remaining_leasing_budget, 2, ',', '.') }}€
                            </p>
                            <p>
                                Please be advised that your leasing budget will be automatically replenished when existing contracts expire.
                            </p>
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
