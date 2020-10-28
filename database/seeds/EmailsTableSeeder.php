<?php

use Illuminate\Database\Seeder;

class EmailsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        \DB::table('emails')->delete();
        
        \DB::table('emails')->insert(array (
            0 => 
            array (
                'id' => 1,
                'key' => 'change_password',
                'subject' => 'Sie haben Ihr Passwort geändert',
                'body' => '<table align="center" border="0" cellspacing="0" cellpadding="0" width="100%">
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
<p>Falls Sie ihr Passwort nicht geändert haben, <a href="{{ $url }}" style="color: {{$styles[\'color\']}}">setzen Sie Ihr Passwort</a> jetzt zurück.</p>
</td>
</tr>
</table>',
                'vars' => '["{{$user->first_name}}", "{{$user->last_name}}", "{{ $date }}", "{{ $time }}", "{{ $domain }}", "{{ $url }}"]',
                'created_by' => NULL,
                'created_at' => '2019-07-30 09:15:12',
                'updated_by' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'key' => 'company_changed',
                'subject' => 'Unternehmensinformationen geändert',
                'body' => '<table align="center" border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
<td>
<h1>Unternehmensinformationen geändert</h1>
</td>
</tr>
<tr>
<td>
<p>
Portaladministrator {{$portalAdmin->first_name}} {{$portalAdmin->last_name}} hat die
Informationen der Firma {{$company->name}} geändert. Dies ist die Information, die sich
geändert hat:
</p>
<table align="center" border="0" cellspacing="0" cellpadding="0" width="100%">
<tr style="font-weight: bold">
<td style="min-width: 100px">
Feld
</td>
<td style="min-width: 200px">
Alter Wert
</td>
<td style="min-width: 200px">
Neuer Wert
</td>
</tr>

@foreach($formatChanges->formatArrayAsTupleOfChanges() as $field)
<tr>
<td><b>{{$field[0]}}</b>:</td>
{!! $field[1] !!}
</tr>
@endforeach
</table>
</td>
</tr>
</table>',
                'vars' => '["{{$portalAdmin->first_name}}", "{{$portalAdmin->last_name}}", "{{$company->name}}"]',
                'created_by' => NULL,
                'created_at' => '2019-07-30 17:33:43',
                'updated_by' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'key' => 'company_leasing_condition_changed',
                'subject' => 'Unternehmensinformationen geändert',
                'body' => '<table align="center" border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
<td>
<h1>Unternehmensinformationen geändert</h1>
</td>
</tr>
<tr>
<td>
@if(is_null($newLeasingCondition))
<p>
Portaladministrator {{$portalAdmin->first_name}} {{$portalAdmin->last_name}} hat die
Lasing-Bedingung "{{$oldLeasingCondition->name}}" für die Firma {{$company->name}}
gelöscht.
</p>
@elseif($newLeasingCondition->isFuture())
<p>
Portaladministrator {{$portalAdmin->first_name}} {{$portalAdmin->last_name}} hat die
Lasing-Bedingung "{{$newLeasingCondition->name}}" für die Firma {{$company->name}}
geändert:
</p>
<p>
Die Leasingbedingung "{{$newLeasingCondition->name}}" mit Leasingfaktor
von {{$newLeasingCondition->factor}}% für {{$newLeasingCondition->period}} Monate
ist morgen
aktiv. Diese Leasingbedingung überschreibt "{{$oldLeasingCondition->name}}" und wird
morgen deaktiviert.
</p>
@else
<p>
Portaladministrator {{$portalAdmin->first_name}} {{$portalAdmin->last_name}} hat die
Lasing-Bedingung "{{$newLeasingCondition->name}}" für die Firma {{$company->name}}
geändert:
</p>
<p>
Die Leasingbedingung "{{$newLeasingCondition->name}}" mit
Leasingfaktor {{$newLeasingCondition->factor}}% für {{$newLeasingCondition->period}}
Monate ist jetzt aktiv. Die Leasingbedingung "{{$oldLeasingCondition->name}}", die
morgen aktiv sein sollte, ist jetzt deaktiviert.
</p>
@endif
</td>
</tr>
</table>',
                'vars' => '["{{$portalAdmin->first_name}}", "{{$portalAdmin->last_name}}", "{{$oldLeasingCondition->name}}", "{{$company->name}}", "{{$newLeasingCondition->name}}", "{$newLeasingCondition->factor}}", "{{$newLeasingCondition->period}}"]',
                'created_by' => NULL,
                'created_at' => '2019-07-30 17:47:02',
                'updated_by' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'key' => 'company_admin_changed',
                'subject' => 'Unternehmensinformationen geändert',
                'body' => '<table align="center" border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
<td>
<h1>Unternehmensinformationen geändert</h1>
</td>
</tr>
<tr>
<td>
<p>
Portaladministrator {{$portalAdmin->first_name}} {{$portalAdmin->last_name}} hat die
Informationen der Firma {{$company->name}} geändert. Dies ist die Information, die sich
geändert hat:
</p>
<table align="center" border="0" cellspacing="0" cellpadding="0" width="100%">
<tr style="font-weight: bold">
<td style="min-width: 100px">
Feld
</td>
<td style="min-width: 200px">
Alter Wert
</td>
<td style="min-width: 200px">
Neuer Wert
</td>
</tr>

@foreach($formatChanges->formatArrayAsTupleOfChanges() as $field)
<tr>
<td><b>{{$field[0]}}</b>:</td>
{!! $field[1] !!}
</tr>
@endforeach
</table>
</td>
</tr>
</table>',
                'vars' => '["{{$portalAdmin->first_name}}", "{{$portalAdmin->last_name}}", "{{$company->name}}", "{{$employee->first_name}}", "{{$employee->last_name}}", "{{$permissions}}"]',
                'created_by' => NULL,
                'created_at' => '2019-07-30 17:54:58',
                'updated_by' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'key' => 'company_admin_created',
                'subject' => 'Ihre Zugangsdaten',
                'body' => '<table align="center" border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
<td>
<h1>Ihre Zugangsdaten</h1>
</td>
</tr>
<tr>
<td>
<p>Hallo {{$user->fullName}},</p>
</td>
</tr>
<tr>
<td>
<p>herzlich Willkommen im Dienstrad-Portal. Ihr Passswort lautet <b>{{ $password }}</b>. Den Link zum Login in das Dienstrad-Portal haben Sie in einer separaten Email erhalten.</p>
</td>
</tr>
<tr>
<td>
<p>Viel Spaß bei der Abwicklung des Dienstrad-Programmes.</p>
</td>
</tr>
</table>',
                'vars' => '["{{$user->fullName}}", "{{ $password }}"]',
                'created_by' => NULL,
                'created_at' => '2019-07-31 07:08:40',
                'updated_by' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            5 => 
            array (
                'id' => 6,
                'key' => 'user_created',
                'subject' => 'Benutzer erstellt',
                'body' => '<table align="center" border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
<td>
<h1>Benutzer erstellt</h1>
</td>
</tr>
<tr>
<td>
<p>Hallo {{$user->fullName}},</p>
</td>
</tr>
<tr>
<td>
<p>Ihr Passwort auf <a href="{{$url}}" style="color: {{$styles[\'color\']}}">{{$url}}</a> ist <strong>{{$password}}</strong></p>
</td>
</tr>
</table>',
                'vars' => '["{{$user->fullName}}", "{{$url}}", "{{$password}}"]',
                'created_by' => NULL,
                'created_at' => '2019-07-31 08:15:38',
                'updated_by' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            6 => 
            array (
                'id' => 7,
                'key' => 'company_created',
                'subject' => 'Ihre Registrierung im Dienstrad Portal',
                'body' => '<table align="center" border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
<td>
<h1>Ihre Registrierung im Dienstrad Portal</h1>
</td>
</tr>
<tr>
<td>
<p>Hallo {{$user->fullName}},</p>
</td>
</tr>
<tr>
<td>
<p>herzlich Willkommen im Dienstrad-Portal. Sie gelangen ab sofort unter folgendem Link zum Login-Bereich: <a href="{{$url}}" style="color: {{$styles[\'color\']}}">{{$url}}</a></p>
</td>
</tr>
<tr>
<td>
<p>Viel Spaß bei der Abwicklung des Dienstrad-Programmes.</p>
</td>
</tr>
</table>',
                'vars' => '["{{$user->fullName}}", "{{$url}}"]',
                'created_by' => NULL,
                'created_at' => '2019-07-31 08:22:58',
                'updated_by' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            7 => 
            array (
                'id' => 8,
                'key' => 'offer_created',
                'subject' => 'Sie haben ein Angebot für Ihr Dienstrad erhalten!',
                'body' => '<table align="center" border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
<td>
<h1>Sie haben ein Angebot für Ihr Dienstrad erhalten!</h1>
</td>
</tr>
<tr>
<td>
<p>Hallo {{$employee->fullName}},</p>
</td>
</tr>
<tr>
<td>
<p>der Lieferant {{$offer->supplierName}} hat Ihnen ein Angebot für Ihr Dienstrad mit der #{{$offer->number}} zugeschickt.
Sie können das Angebot unter folgendem <a href="{{$url}}" style="color: {{$styles[\'color\']}}">link</a> prüfen, akzeptieren oder ablehnen.
Mit dem Akzeptieren des Angebotes generieren Sie gleichzeitig einen Überlassungsvertrag.
Dieser muss jedoch erst von Ihrem Arbeitgeber genehmigt werden.</p>
</td>
</tr>
<tr>
<td align="center">
<p class="action">Unter folgendem Link:</p>
<p class="action"><a href="{{$url}}" style="color: {{$styles[\'color\']}}">{{$url}}</a></p>
<p class="action">können Sie das Angebot einsehen.</p>
</td>
</tr>
</table>',
                'vars' => '["{{$employee->fullName}}", "{{$offer->supplierName}}", "{{$offer->number}}", "{{$url}}"]',
                'created_by' => NULL,
                'created_at' => '2019-07-31 10:17:15',
                'updated_by' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            8 => 
            array (
                'id' => 9,
                'key' => 'supplier_created',
                'subject' => 'Ihre Lieferant wurde bereits im System angelegt!',
                'body' => '<table align="center" border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
<td>
<h1>Ihre Lieferant wurde bereits im System angelegt!</h1>
</td>
</tr>
<tr>
<td>
<p>Hallo {{$user->fullName}},</p>
</td>
</tr>
<tr>
<td>
<p>herzlich Willkommen im Dienstrad-Portal. Sie gelangen ab sofort unter folgendem Link zum <a href="{{$url}}" style="color: {{$styles[\'color\']}}">{{$url}}</a>.
Viel Spaß bei der Abwicklung des Dienstrad-Programmes.</p>
</td>
</tr>
</table>',
                'vars' => '["{{$user->fullName}}", "{{$url}}"]',
                'created_by' => NULL,
                'created_at' => '2019-07-31 11:06:18',
                'updated_by' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            9 => 
            array (
                'id' => 10,
                'key' => 'offer_approved_employee',
                'subject' => 'Ihr Überlassungsvertrag wurde genehmigt!',
                'body' => '<table align="center" border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
<td >
<h1>Ihr Überlassungsvertrag wurde genehmigt!</h1>
</td>
</tr>
<tr>
<td>
<p>Hallo {{$user->fullName}},</p>

<p>
Ihr eingereichter Überlassungsvertrag wurde von Ihrem Arbeitgeber genehmigt. Gleichzeitig wurde die Bestellung Ihres Dienstrads beim Lieferanten ausgelöst.
Sobald Ihr Dienstrad abhol- bzw. versandbereit ist, erhalten Sie Ihren Abholcode per E-Mail. Bitte bewahren Sie daher sorgfältig die E-Mail mit Ihrem Abholcode auf. Dieser ist auch in Ihrem Dienstrad-Portal abrufbar. Ohne diesen Code ist keine Übernahme des Fahrrads möglich.
Unter folgendem Link gelangen Sie zu den abholbereiten Bestellungen <a href="{{$url}}" style="color: {{$styles[\'color\']}}">{{$url}}</a>.
</td>
</tr>
</table>',
                'vars' => '["{{$user->fullName}}", "{{$url}}"]',
                'created_by' => NULL,
                'created_at' => '2019-07-31 11:20:38',
                'updated_by' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            10 => 
            array (
                'id' => 11,
                'key' => 'offer_approved_supplier',
                'subject' => 'Bestellung',
                'body' => '<table align="center" border="0" cellspacing="0" cellpadding="0" width="100%">
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
<tr class="email_footer">
<td>
<p>Dies ist eine automatisch generierte Mail. Bitte antworten Sie daher nicht auf diese Mail.</p>
</td>
</tr>
</table>',
                'vars' => '["{{$offer->number}}", "{{$offer->order->number}}", "{{$offer->company->name}}", "{{$offer->company->address}}", "{{$offer->company->zip}}", "{{$offer->company->city->name}}", "{{$offer->user->first_name}}", "{{$offer->user->last_name}}", "{{$offer->productCategory->name}}", "{{$offer->productBrand}}", "{{$offer->productModel}}", "{{$offer->productSize}}", "{{$offer->productColor}}", "{{$netPriceWithAccessories}}", "{{$vatApplied}}", "{{$grossPriceWithAccessories}}", "{{$portalName}}"]',
                'created_by' => NULL,
                'created_at' => '2019-07-31 11:42:00',
                'updated_by' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            11 => 
            array (
                'id' => 12,
                'key' => 'leasing_budget_low',
                'subject' => NULL,
                'body' => '<table align="center" border="0" cellspacing="0" cellpadding="0" width="100%">
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
Alloted Leasing Budget: {{ number_format($company->leasing_budget, 2, \',\', \'.\') }}€<br>
Leasing budget used: {{ number_format($company->acceptedOffers()->sum(\'product_discounted_price\'), 2, \',\', \'.\') }}€<br>
Leasing budget remaining: {{ number_format($company->remaining_leasing_budget, 2, \',\', \'.\') }}€
</p>
<p>
Please be advised that your leasing budget will be automatically replenished when existing contracts expire.
</p>
</td>
</tr>
</table>',
            'vars' => '"[\'{{ $company->name }}\',\'{{ number_format($company->leasing_budget, 2, \',\', \'.\') }}\',\'{{ number_format($company->acceptedOffers()->sum(\'product_discounted_price\'), 2, \',\', \'.\') }}\',\'{{ number_format($company->remaining_leasing_budget, 2, \',\', \'.\') }}\']"',
                'created_by' => NULL,
                'created_at' => '2019-07-31 11:58:37',
                'updated_by' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            12 => 
            array (
                'id' => 13,
                'key' => 'offer_rejected_employee',
                'subject' => 'Leider konnte Ihr Überlassungsvertrag nicht genehmigt werden!',
                'body' => '<table align="center" border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
<td>
<h1>Leider konnte Ihr Überlassungsvertrag nicht genehmigt werden!</h1>
</td>
</tr>
<tr>
<td>
<p>Hallo {{$user->fullName}},</p>
</td>
</tr>
<tr>
<td>
<p>Die Genehmigung Ihres Überlassungsvertrags konnte leider nicht genehmigt werden.
Bei weiteren Fragen setzen Sie sich bitte mit dem zuständigen Ansprechpartner für das Dienstrad-Programm in Ihrem Unternehmen in Verbindung.</p>
</td>
</tr>
</table>',
                'vars' => '["{{$user->fullName}}"]',
                'created_by' => NULL,
                'created_at' => '2019-07-31 15:51:40',
                'updated_by' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            13 => 
            array (
                'id' => 14,
                'key' => 'offer_signed_employee',
                'subject' => 'Sie haben Ihren Überlassungsvertrag hochgeladen',
                'body' => '<table align="center" border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
<td>
<h1>Sie haben Ihren Überlassungsvertrag hochgeladen</h1>
</td>
</tr>
<tr>
<td>
<p>Hallo {{$user->fullName}},</p>
</td>
</tr>
<tr>
<td>
<p>vielen Dank, dass Sie ihren unterschriebenen Überlassungsvertrag hochgeladen haben.
Im nächsten Schritt muss dieser jedoch noch von Ihrem Arbeitgeber genehmigt werden.
Sie erhalten eine separate E-Mail über die Freigabe.
Bei weiteren Fragen setzen Sie sich bitte mit dem zuständigen Ansprechpartner für das Dienstrad-Programm in Ihrem Unternehmen in Verbindung.</p>
</td>
</tr>
</table>',
                'vars' => '["{{$user->fullName}}"]',
                'created_by' => NULL,
                'created_at' => '2019-07-31 16:06:29',
                'updated_by' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            14 => 
            array (
                'id' => 15,
                'key' => 'offer_rejected_supplier',
                'subject' => 'Ihr Angebot wurde abgelehnt!',
                'body' => '<table align="center" border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
<td>
<h1>Ihr Angebot wurde abgelehnt!</h1>
</td>
</tr>
</table>
<table align="left" border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
<td>
<p>Hallo {{$supplier->name}},</p>
</td>
</tr>
<tr>
<td>
<p>das von Ihnen erstellte Angebot #{{$offer->number}} wurde abgelehnt.</p>
</td>
</tr>
</table>',
                'vars' => '["{{$supplier->name}}", "{{$offer->number}}"]',
                'created_by' => NULL,
                'created_at' => '2019-07-31 16:18:31',
                'updated_by' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            15 => 
            array (
                'id' => 16,
                'key' => 'offers_exported',
                'subject' => 'Angebote exportiert',
                'body' => '<table align="center" border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
<td>
<h1>Angebote exportiert</h1>
</td>
</tr>
<tr>
<td>
<p>Ihr Export von Angeboten wurde generiert. Finden Sie Ihr Dokument im Anhang.</p>
</td>
</tr>
<tr>
<td align="center">
<a href="#"
style="display: block; margin: 0 auto 20px; text-decoration: none; color:#fff; background: {{$styles[\'color\']}}; height: 50px; width: 199px;	border-radius: 3px; line-height: 50px; font-size: 16px;	font-weight: bold; text-align: center;">Siehe Einzelheiten</a>
</td>
</tr>
</table>',
                'vars' => '[]',
                'created_by' => NULL,
                'created_at' => '2019-07-31 16:25:32',
                'updated_by' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            16 => 
            array (
                'id' => 17,
                'key' => 'offer_created_company_admin',
                'subject' => 'Ein Mitarbeiter*in hat ein Angebot für ein Dienstrad erhalten!',
                'body' => '<table align="center" border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
<td>
<h1>Ein Mitarbeiter*in hat ein Angebot für ein Dienstrad erhalten!</h1>
</td>
</tr>
<tr>
<td>
<p>Hallo {{$companyAdmin->fullName}},</p>
</td>
</tr>
<tr>
<td>
<p>der/die Mitarbeiter*in {{$offer->user->fullName}} hat ein Dienstrad-Angebot mit der
Angebotsnummer {{$offer->number}} vom Lieferanten {{$offer->supplierName}} erhalten.</p>
</td>
</tr>
<tr>
<td align="center">
<p class="action">Unter folgendem Link</p>
<p class="action"><a href="{{$url}}" style="color: {{$styles[\'color\']}}">{{$url}}</a></p>
<p class="action">können Sie das Angebot einsehen.</p>
</td>
</tr>
</table>',
                'vars' => '["{{$companyAdmin->fullName}}", "{{$offer->user->fullName}}", "{{$offer->number}}", "{{$offer->supplierName}}", "{{$url}}"]',
                'created_by' => NULL,
                'created_at' => '2019-07-31 17:11:43',
                'updated_by' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            17 => 
            array (
                'id' => 18,
                'key' => 'password_reset',
                'subject' => 'Passwort zurücksetzen',
                'body' => '<table align="center" border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
<td>
<h1>Sie haben beantragt, Ihr Passwort zurückzusetzen</h1>
</td>
</tr>
<tr>
<td>
<div style="font-size: 18px; line-height: 21px; color: #767676; margin-bottom: 57px;">{{ $date }}</div>
</td>
</tr>
<tr>
<td>
<p>Um Ihr Passwort zurückzusetzen, klicken Sie auf den folgenden Link und folgen Sie den Anweisungen.</p>
</td>
</tr>
<tr>
<td align="center">
<a href="{{ $url }}"
style="display: block; margin: 0 auto 40px; text-decoration: none; color:#fff; background: {{ $styles[\'color\'] }}; height: 50px; width: 199px;    border-radius: 3px; line-height: 50px; font-size: 16px;    font-weight: bold; text-align: center;">Passwort zurücksetzen</a>
</td>
</tr>
</table>',
                'vars' => '["{{ $date }}", "{{ $url }}"]',
                'created_by' => NULL,
                'created_at' => '2019-07-31 17:52:37',
                'updated_by' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            18 => 
            array (
                'id' => 19,
                'key' => 'order_ready',
                'subject' => 'Ihr Dienstrad ist abholbereit!',
                'body' => '<table align="center" border="0" cellspacing="0" cellpadding="0" width="100%">
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
</table>',
            'vars' => '["{{$employee->getFullNameAttribute()}}", "{{ $order->pickup_code }}"]',
                'created_by' => NULL,
                'created_at' => '2019-08-01 07:28:57',
                'updated_by' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            19 => 
            array (
                'id' => 20,
                'key' => 'order_pickup_employee',
                'subject' => 'Sie haben Ihr Dienstrad übernommen',
                'body' => '<table align="center" border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
<td>
<p>Hallo {{ $user->fullName }},</p>
</td>
</tr>
<tr>
<td>
<p>Sie haben Ihr Dienstrad übernommen. Im Anhang finden Sie die Übernahmebestätigung und Ihren Überlassungsvertrag.</p>
</td>
</tr>
</table>',
                'vars' => '["{{ $user->fullName }}"]',
                'created_by' => NULL,
                'created_at' => '2019-08-01 11:55:12',
                'updated_by' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            20 => 
            array (
                'id' => 21,
                'key' => 'contract_created',
                'subject' => 'Erfolgreiche Genehmigung eines Überlassungsvertrages!',
                'body' => '<table align="center" border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
<td>
<h1>Erfolgreiche Genehmigung eines Überlassungsvertrages!</h1>
</td>
</tr>
<tr>
<td>
<p>Hallo {{$user->fullName}},</p>
</td>
</tr>
<tr>
<td>
<p>Sie haben den Überlassungsvertrag Ihres Mitarbeiters {{$contract->user->fullName}} genehmigt. Gleichzeitig wurde die Bestellung des Dienstrads beim Lieferanten ausgelöst.
Sobald das Dienstrad abhol- bzw. versandbereit ist, erhält Ihr Mitarbeiter {{$contract->user->fullName}} einen entsprechenden Abholcode per E-Mail. Sobald die Übernahme durch den Mitarbeiter erfolgt ist, erhalten Sie in einer separaten E-Mail den Einzel-Leasingantrag.</p>
</td>
</tr>
</table>',
                'vars' => '["{{$user->fullName}}", "{{$contract->user->fullName}}"]',
                'created_by' => NULL,
                'created_at' => '2019-08-01 12:12:39',
                'updated_by' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            21 => 
            array (
                'id' => 22,
                'key' => 'order_pickup_company',
                'subject' => 'Ein Mitarbeiter hat sein Dienstrad übernommen',
                'body' => '<table align="center" border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
<td>
<p>Hallo {{ $user->fullName }},</p>
</td>
</tr>
<tr>
<td>
<p>der Mitarbeiter {{$order->employeeName}} hat sein Dienstrad übernommen. Im Anhang finden Sie den Überlassungsvertrag, die Übernahmebestätigung sowie den Antrag zum Einzelleasingvertrag.</p>
</td>
</tr>
</table>',
                'vars' => '["{{$user->fullName}}", "{{$order->employeeName}}"]',
                'created_by' => NULL,
                'created_at' => '2019-08-01 12:20:16',
                'updated_by' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            22 => 
            array (
                'id' => 23,
                'key' => 'order_pickup_supplier',
                'subject' => 'Ein Kunde hat sein Dienstrad übernommen',
                'body' => '<table align="center" border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
<td>
<p>Hallo {{ $userName }},</p>
</td>
</tr>
<tr>
<td>
<p>der Kunde {{$order->employeeName}} der Firma {{$order->company->name}} hat sein Dienstrad mit der Bestellnummer {{$order->number}} übernommen. Im Anhang finden Sie die Übernahmebestätigung.</p>
</td>
</tr>
</table>',
                'vars' => '["{{ $userName }}", "{{$order->employeeName}}", "{{$order->company->name}}", "{{$order->number}}"]',
                'created_by' => NULL,
                'created_at' => '2019-08-01 12:31:37',
                'updated_by' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            23 => 
            array (
                'id' => 24,
                'key' => 'order_pickup_admin',
                'subject' => 'Ein Mitarbeiter hat sein Dienstrad übernommen',
                'body' => '<table align="center" border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
<td>
<p>Hallo {{ $user->fullName }},</p>
</td>
</tr>
<tr>
<td>
<p>der Mitarbeiter {{ $order->employeeName }} hat sein Dienstrad übernommen. Im Anhang finden Sie die Übernahmebestätigung und den Antrag zum Einzelleasingvertrag.</p>
</td>
</tr>
</table>',
                'vars' => '["{{ $user->fullName }}", "{{ $order->employeeName }}"]',
                'created_by' => NULL,
                'created_at' => '2019-08-02 07:32:52',
                'updated_by' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            24 => 
            array (
                'id' => 25,
                'key' => 'order_pickup_company_dienstrad_support',
                'subject' => 'Ein Mitarbeiter hat sein Dienstrad übernommen',
                'body' => '<table align="center" border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
<td>
<p>Hallo,</p>
</td>
</tr>
<tr>
<td>
<p>der Mitarbeiter {{$order->employeeName}} hat sein Dienstrad übernommen. Im Anhang finden Sie den Überlassungsvertrag, die Übernahmebestätigung sowie den Antrag zum Einzelleasingvertrag.</p>
</td>
</tr>
</table>',
                'vars' => '["{{$order->employeeName}}"]',
                'created_by' => NULL,
                'created_at' => '2019-08-02 07:43:44',
                'updated_by' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            25 => 
            array (
                'id' => 26,
                'key' => 'admin_created_for_portal_admin',
                'subject' => 'Ein neues Firmen-Profil wurde im Dienstrad-Portal erstellt',
                'body' => '<table align="center" border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
<td>
<h1>Ein neues Firmen-Profil wurde im Dienstrad-Portal erstellt</h1>
</td>
</tr>
<tr>
<td>
<p>Hallo {{$portalAdmins->fullName}},</p>
</td>
</tr>
<tr>
<td>
<p>der Dienstrad-Rahmenvertrag der Firma</p>
</td>
</tr>
<tr>
<td>
<p>{{ $company->name }}</p>
</td>
</tr>
<tr>
<td>
<p>{{ $company->address }}</p>
</td>
</tr>
<tr>
<td>
<p>{{ $company->zip }}, {{ $company->city->name }}</p>
</td>
</tr>
<tr>
<td>
<p>wurde bestätigt und das entsprechende Firmenprofil im Dienstrad-Portal erstellt.</p>
</td>
</tr>
<tr>
<td>
<p>Der Firmen-Administrator hat eine separate E-Mail zu diesem Vorgang erhalten.</p>
</td>
</tr>
</table>',
                'vars' => '["{{$portalAdmins->fullName}}", "{{ $company->name }}", "{{ $company->address }}", "{{ $company->zip }}", "{{ $company->city->name }}"]',
                'created_by' => NULL,
                'created_at' => '2019-08-02 08:34:46',
                'updated_by' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            26 => 
            array (
                'id' => 27,
                'key' => 'contracts_exported',
                'subject' => 'Verträge exportiert',
                'body' => '<table align="center" border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
<td>
<h1>Vertrag exportiert</h1>
</td>
</tr>
<tr>
<td>
<p>Ihr Export von Verträgen wurde generiert. Finden Sie Ihr Dokument im Anhang.</p>
</td>
</tr>
<tr>
<td align="center">
<a href="#"
style="display: block; margin: 0 auto 20px; text-decoration: none; color:#fff; background: {{$styles[\'color\']}}; height: 50px; width: 199px;	border-radius: 3px; line-height: 50px; font-size: 16px;	font-weight: bold; text-align: center;">Siehe Einzelheiten</a>
</td>
</tr>
</table>',
                'vars' => '[]',
                'created_by' => NULL,
                'created_at' => '2019-08-02 08:58:06',
                'updated_by' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            27 => 
            array (
                'id' => 28,
                'key' => 'orders_exported',
                'subject' => 'Bestellungen exportiert',
                'body' => '<table align="center" border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
<td>
<h1>Bestellungen exportiert</h1>
</td>
</tr>
<tr>
<td>
<p>Ihr Export von Bestellungen wurde generiert. Finden Sie Ihr Dokument im Anhang.</p>
</td>
</tr>
<tr>
<td align="center">
<a href="#"
style="display: block; margin: 0 auto 20px; text-decoration: none; color:#fff; background: {{$styles[\'color\']}}; height: 50px; width: 199px;	border-radius: 3px; line-height: 50px; font-size: 16px;	font-weight: bold; text-align: center;">Siehe Einzelheiten</a>
</td>
</tr>
</table>',
                'vars' => '[]',
                'created_by' => NULL,
                'created_at' => '2019-08-02 09:09:58',
                'updated_by' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            28 => 
            array (
                'id' => 29,
                'key' => 'registration_approved',
                'subject' => 'Willkommen im Dienstrad-Portal!',
                'body' => '<table align="center" border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
<td align="center">
<h1>Willkommen im Dienstrad-Portal!</h1>
</td>
</tr>
<tr>
<td>
<p>Hallo {{$user->fullName}},</p>
</td>
</tr>
<tr>
<td>
<p>Ihre Registrierung zum Dienstrad-Programm war erfolgreich.
Sie können sich ab sofort unter folgendem Link <a href="{{$url}}" style="color: {{$styles[\'color\']}}">{{$url}}</a> einloggen und am
Dienstrad-Programm teilnehmen.</p>
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
</table>',
                'vars' => '["{{$user->fullName}}", "{{ $url }}"]',
                'created_by' => NULL,
                'created_at' => '2019-08-02 09:26:14',
                'updated_by' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            29 => 
            array (
                'id' => 30,
                'key' => 'registration_completed',
                'subject' => 'Ihre Registrierung im Dienstrad-Portal!',
                'body' => '<table align="center" border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
<td>
<h1>Ihre Registrierung im Dienstrad-Portal!</h1>
</td>
</tr>
<tr>
<td>
<p>Hallo {{$user->fullName}},</p>
</td>
</tr>
<tr>
<td>
<p>wir haben Ihre Registrierung zur Teilnahme am Dienstrad-Programm erhalten. Diese muss
vorab durch Ihren Arbeitgeber genehmigt werden. Wir werden Sie über den Status der
Genehmigung per E-Mail informieren. Bei weiteren Fragen setzen Sie sich bitte mit dem
zuständigen Ansprechpartner für das Dienstrad-Programm in Ihrem Unternehmen in
Verbindung.</p>
</td>
</tr>
<tr>
<td>
<p>Mit dem Einloggen in in das Dienstrad-Portal akzeptieren Sie unsere anhängende
Datenschutzerklärung.</p>
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
</table>',
                'vars' => '["{{$user->fullName}}"]',
                'created_by' => NULL,
                'created_at' => '2019-08-02 09:49:52',
                'updated_by' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            30 => 
            array (
                'id' => 31,
                'key' => 'registration_link',
                'subject' => 'Registrierungslink',
                'body' => '<table align="center" border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
<td>
<h1>Herzlich willkommen!</h1>
</td>
</tr>
<tr>
<td>
<p class="action">Setzen Sie Ihren Registrierungsprozess fort, indem Sie auf den folgenden Link klicken</p>
</td>
</tr>
<tr>
<td align="center">
<a href="{{ $url }}" style="display: block; margin: 0 auto 30px; text-decoration: none; color:#fff; background: {{$styles[\'color\']}}; height: 50px; width: 199px;	border-radius: 3px; line-height: 50px; font-size: 16px;	font-weight: bold; text-align: center;">Passwort zurücksetzen</a>
</td>
</tr>
</table>',
                'vars' => '["{{$url}}"]',
                'created_by' => NULL,
                'created_at' => '2019-08-02 10:03:51',
                'updated_by' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            31 => 
            array (
                'id' => 32,
                'key' => 'registration_new',
                'subject' => 'Prüfung der Berechtigung für den Zugang zum Dienstrad-Portal!',
                'body' => '<table align="center" border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
<td>
<h1>Prüfung der Berechtigung für den Zugang zum Dienstrad-Portal!</h1>
</td>
</tr>
<tr>
<td>
<p>Hallo Firmen-Administrator,</p>
</td>
</tr>
<tr>
<td>
<p>Ihr(e) Mitarbeiter(in) {{$user->fullName}} hat sich im Dienstrad-Portal zur Bestellung
eines Dienstfahrrads registriert.
</p>
</td>
</tr>
<tr>
<td>
<p>
Bitte prüfen Sie die entsprechende Berechtigung vor Freigabe der Registrierung.
</p>
</td>
</tr>
<tr>
<td>
<p class="action"> Unter folgendem Link gelangen Sie zu den <a href="{{$url}}" style="color: {{$styles[\'color\']}}">offenen Anträgen</a>.</p>
</td>
</tr>
</table>',
                'vars' => '["{{$user->fullName}}", "{{$url}}"]',
                'created_by' => NULL,
                'created_at' => '2019-08-02 10:11:18',
                'updated_by' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            32 => 
            array (
                'id' => 33,
                'key' => 'registration_rejected',
                'subject' => 'Leider war Ihre Registrierung im Dienstrad-Portal nicht erfolgreich!',
                'body' => '<table align="center" border="0" cellspacing="0" cellpadding="0" width="100%">
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
</table>',
                'vars' => '["{{$user->fullName}}"]',
                'created_by' => NULL,
                'created_at' => '2019-08-02 10:15:52',
                'updated_by' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            33 => 
            array (
                'id' => 34,
                'key' => 'offer_approved_portal_admin',
                'subject' => 'Angebot und Überlassungsvertrag wurden vom Arbeitgeber genehmigt',
                'body' => '<table align="center" border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
<td>
<p>Hallo {{$portalAdmin->fullName}},</p>
<p>das Angebot {{$offer->number}} und der Überlassungsvertrag des/der Mitarbeiters*in</p>
<p>{{$offer->user->fullName}}</p>
<p>wurden vom Arbeitgeber genehmigt. Gleichzeitig wurde die Bestellung</p>
<p>{{$offer->order->number}}</p>
<p>des Dienstrads beim Lieferanten ausgelöst.</p>
<p>Sobald das Dienstrad abhol- bzw. versandbereit ist, erhält der/die Mitarbeiterin einen entsprechenden Abholcode per E-Mail. Sobald die Übernahme durch den/die Mitarbeiterin erfolgt ist, erhalten Sie in einer separaten E-Mail den Einzel-Leasingantrag.</p>
</td>
</tr>
</table>',
                'vars' => '["{{$portalAdmin->fullName}}", "{{$offer->number}}", "{{$offer->user->fullName}}", "{{$offer->order->number}}"]',
                'created_by' => NULL,
                'created_at' => '2019-08-02 10:20:06',
                'updated_by' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            34 => 
            array (
                'id' => 35,
                'key' => 'contract_started',
                'subject' => 'Neuer Vertrag',
                'body' => '<table align="center" border="0" cellspacing="0" cellpadding="0" width="100%">
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
<p>Ein neuer Vertrag wurde in <a href="{{ $domainUrl }}" style="color: {{$styles[\'color\']}}">{{ $domain }}</a> gestartet.</p>
@endif
</td>
</tr>
<tr>
<td align="center">
<a href="{{ $url }}"
style="display: block; margin: 0 auto 20px; text-decoration: none; color:#fff; background: {{ $styles[\'color\'] }}; height: 50px; width: 199px;    border-radius: 3px; line-height: 50px; font-size: 16px;    font-weight: bold; text-align: center;">Siehe Einzelheiten</a>
</td>
</tr>
</table>',
                'vars' => '["{{ $contract->number }}", "{{ $contract->user->code }}", "{{ $domainUrl }}", "{{ $domain }}", "{{ $url }}"]',
                'created_by' => NULL,
                'created_at' => '2019-08-04 07:53:08',
                'updated_by' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            35 => 
            array (
                'id' => 36,
                'key' => 'offer_rejected_portal_admin',
                'subject' => 'Angebot und Überlassungsvertrags konnten nicht genehmigt werden!',
                'body' => '<table align="center" border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
<td>
<h1>Angebot und Überlassungsvertrags konnten nicht genehmigt werden!</h1>
</td>
</tr>
</table>
<table align="left" border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
<td>
<p>Hallo {{$portalAdmin->fullName}},</p>
</td>
</tr>
<tr>
<td>
<p>die Genehmigung des Überlassungsvertrags zum Angebot</p>
<p>{{$offer->number}}</p>
<p>wurde vom Arbeitgeber leider nicht erteilt. Somit wurde keine Bestellung beim Lieferanten ausgelöst.</p>
<p>Bei weiteren Fragen setzen Sie sich bitte mit dem zuständigen Ansprechpartner für das Dienstrad-Programm im betreffenden Unternehmen in Verbindung.</p>
</td>
</tr>
</table>',
                'vars' => '["{{$portalAdmin->fullName}}", "{{$offer->number}}"]',
                'created_by' => NULL,
                'created_at' => '2019-08-04 08:00:36',
                'updated_by' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            36 => 
            array (
                'id' => 37,
                'key' => 'offer_signed_admins',
                'subject' => 'Bitte prüfen Sie den Überlassungsvertrag Ihres Mitarbeiters!',
                'body' => '<table align="center" border="0" cellspacing="0" cellpadding="0" width="100%">
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
<p class="action"><a href="{{$url}}" style="color: {{$styles[\'color\']}}">{{$url}}</a></p>
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
</table>',
                'vars' => '["{{ $admin->fullName }}", "{{$offer->user->fullName}}", "{{$url}}"]',
                'created_by' => NULL,
                'created_at' => '2019-08-04 08:29:18',
                'updated_by' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            37 => 
            array (
                'id' => 38,
                'key' => 'feedback',
                'subject' => NULL,
                'body' => '{!! $body !!}',
                'vars' => '[]',
                'created_by' => NULL,
                'created_at' => '2019-08-04 08:41:45',
                'updated_by' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            38 => 
            array (
                'id' => 39,
                'key' => 'report',
                'subject' => 'Report',
            'body' => '@if ($categories)
<div><small>{{ implode(\',\', $categories) }}</small></div>
@endif
<div>{!! $body !!}</div>',
                'vars' => '[]',
                'created_by' => NULL,
                'created_at' => '2019-08-04 08:48:17',
                'updated_by' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            39 => 
            array (
                'id' => 40,
                'key' => 'custom_notification',
                'subject' => NULL,
            'body' => '<table align="center" border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
<td>
<h1>{{ $subject }}</h1>
</td>
</tr>
<tr>
<td>
</td>
</tr>
<tr>
<td align="center">
    {!! $body !!}
    <br /><br />
</td>
</tr>
</table>',
                'vars' => '["{{ $subject }}", "{{ $body }}"]',
                'created_by' => NULL,
                'created_at' => '2019-08-012 12:00:00',
                'updated_by' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}