@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => config('app.url'), 'styles' => $styles])
        @endcomponent
    @endslot
    <table align="center" border="0" cellspacing="0" cellpadding="0" width="100%">
        <tr>
            <td>
                <h1>Sie haben Ihr Passwort geändert</h1>
            </td>
        </tr>
        <tr>
            <td>
                <p>Hallo {{$user->first_name}} {{$user->last_name}},</p>
            </td>
        </tr>
        <tr>
            <td>
                <p>Sie haben am {{ $date }} um {{ $time }} Ihr Passwort auf <a href="{{ $domainUrl }}">{{ $domain }}</a> geändert.</p>
            </td>
        </tr>
        <tr>
            <td>
                <p>Diese Mail erhalten Sie zu Ihrer information über den Vorgang.</p>
            </td>
        </tr>
        <tr>
            <td>
                <p>Falls Sie ihr Passwort nicht geändert haben, <a href="{{ $url }}" style="color: {{$styles['color']}}">setzen Sie Ihr Passwort</a> jetzt zurück.</p>
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
    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
        @endcomponent
    @endslot
@endcomponent