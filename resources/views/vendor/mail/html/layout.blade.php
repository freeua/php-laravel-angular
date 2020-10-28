<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link href="https://fonts.googleapis.com/css?family=Ubuntu:400,400i,500,500i,700,700i" rel="stylesheet">
</head>
<body>
    <style>
        @media only screen and (max-width: 600px) {
            .inner-body {
                width: 100% !important;
            }

            .footer {
                width: 100% !important;
            }
        }

        @media only screen and (max-width: 500px) {
            .button {
                width: 100% !important;
            }
        }
    </style>

    <table align="center" border="0" cellspacing="0" cellpadding="0" width="100%">
        {{-- Header --}}
        @component('mail::header', ['url' => config('app.url'), 'styles' => $styles])
        @endcomponent
        <!-- Email Body -->
        <tr>
            <td></td>
            <td width="602px" align="center">
                <div class="content">
                    <!-- Body content -->
                    {{ Illuminate\Mail\Markdown::parse($slot) }}
                    {{ $subcopy ?? '' }}
                    <table>
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
        @component('mail::footer')
        @endcomponent
    </table>
</body>
</html>
