<!--suppress ALL -->
<html>
<head>
    <link href="css/contract-pdf.css" rel="stylesheet" type="text/css">
</head>
<body>
<table cellpadding="0" cellspacing="0">
    <tbody>
    <tr>
        <td>
            @if ($companyLogo)
                <img class="company-logo"
                     src="{{ $companyLogo}}">
            @else
                <img class="company-logo"
                     src="img/logo/company-logo.png">
            @endif
        </td>
        <td style="width: 200px;"></td>
        <td class="company-logo-td">
            @if ($logo)
            <img class="company-logo"
                 src="{{$logo}}">
            @else
                <img class="company-logo"
                     src="img/logo/portal-logo.png">
            @endif
        </td>
    </tr>
    </tbody>
</table>
<table cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 15px;margin-top:10px;">
    <tbody>
    <tr>
        <td align="center">
            <h2>Dienstrad-Überlassungsvertrag</h2>
        </td>
    </tr>
    </tbody>
</table>

<h3 class="subtitle">Arbeitgeber</h3>

<table class="field-value-table">
    <tbody>
    <tr>
        <td class="label">Firma</td>
        <td class="field-value">{{$offer->user->company->name}}</td>
    </tr>
    <tr>
        <td class="label">Straße, Nr.</td>
        <td class="field-value">{{$offer->user->company->address}}</td>
    </tr>
    <tr>
        <td class="label">PLZ, ORT</td>
        <td class="field-value">{{$offer->user->company->zip}} {{$offer->user->company->city->name}}</td>
    </tr>
    </tbody>
</table>

<h3 class="subtitle">Mitarbeiter</h3>

<table class="field-value-table">
    <tbody>
    <tr>
        <td class="title"></td>
    </tr>
    <tr>
        <td class="label">Anrede</td>
        <td class="field-value">{{$offer->employeeSalutation == 'herr' ? 'Herr' : 'Frau'}}</td>
    </tr>
    <tr>
        <td class="label">Vor-/Nachname</td>
        <td class="field-value">{{$offer->employeeName}}</td>
    </tr>
    <tr>
        <td class="label">Straße, Nr.</td>
        <td class="field-value">{{$offer->employeeStreet}}</td>
    </tr>
    <tr>
        <td class="label">PLZ, ORT</td>
        <td class="field-value">{{$offer->employeePostalCode}}, {{$offer->employeeCity}}</td>
    </tr>
    <tr>
        <td class="label">E-Mail</td>
        <td class="field-value">{{$offer->employeeEmail}}</td>
    </tr>
    <tr>
        <td class="label">Telefon</td>
        <td class="field-value">{{$offer->employeePhone}}</td>
    </tr>
    <tr>
        <td class="label">Pers.Nr.</td>
        <td class="field-value">{{$offer->employeeNumber}}</td>
    </tr>
    </tbody>
</table>

<h3 class="subtitle">Dienstrad</h3>

<table class="field-value-table">
    <tr>
        <td class="label">Marke</td>
        <td class="field-value">{{$offer->productBrand}}</td>
    </tr>
    <tr>
        <td class="label">Modell</td>
        <td class="field-value">{{$offer->productModel}}</td>
    </tr>
    <tr>
        <td class="label">Größe</td>
        <td class="field-value">{{$offer->productSize}}</td>
    </tr>
    <tr>
        <td class="label">Kategorie</td>
        <td class="field-value">{{$offer->productCategory->name}}</td>
    </tr>
    <tr>
        <td class="label">Farbe</td>
        <td class="field-value">{{$offer->productColor}}</td>
    </tr>
    <tr>
        <td class="label">Zubehör</td>
        <td class="field-value">{{$offer->notes}}</td>
    </tr>
</table>
<table class="field-value-table" style="margin-top: 10px">
    <tr>
        <td class="label" style="font-size: 11pt"><strong>Bruttolistenpreis inkl. MwSt. in €</strong></td>
        <td class="field-value">{{$totalPrice}}</td>
    </tr>
    <tr>
        <td class="label" style="font-size: 11pt"><strong>Vereinbarter Kaufpreis inkl. MwSt. in €</strong></td>
        <td class="field-value">{{$agreedPurchasePrice}}</td>
    </tr>
</table>
<h3 class="subtitle">Lieferant</h3>

<table class="field-value-table">
    <tr>
        <td class="label"></td>
    </tr>
    <tr>
        <td class="label">Fachhändler</td>
        <td class="field-value">{{$offer->supplierName}}</td>
    </tr>
    <tr>
        <td class="label">Straße, Nr.</td>
        <td class="field-value">{{$offer->supplierStreet}}</td>
    </tr>
    <tr>
        <td class="label">PLZ, ORT</td>
        <td class="field-value">{{$offer->supplierCity}} {{$offer->supplierPostalCode}}</td>
    </tr>
</table>
<table class="field-value-table" style="margin-top: 15px">
    <tbody>
    </tr>
    <tr>
        @if($offer->user->company->gross_conversion == 'netto')
            <td class="label" style="font-size: 11pt"><strong>Monatspauschale in €</strong></td>
        @else
            <td class="label" style="font-size: 11pt"><strong>Umwandlungsbetrag in €</strong></td>
        @endif
        <td class="field-value">{{ $totalRates }}</td>
    </tr>
    <tr>
        <td class="label" style="font-size: 11pt"><strong>Laufzeit in Monaten</strong></td>
        <td class="field-value">{{ $period }}</td>
    </tr>
    </tbody>
</table>


<pagebreak/>

<h3 class="contract-title">1. Überlassung</h3>
<p class="p-with-mb">
    Der Arbeitgeber überlässt dem Mitarbeiter das Fahrzeug zur dienstlichen und privaten Nutzung. Die nachstehenden
    Regelungen gelten auch für Folgefahrzeuge. Bei Folgefahrzeugen wird die
    @if($offer->user->company->gross_conversion == 'netto')
        <span>nutzungsunabhängige Monatspauschale</span>
    @else
        <span>Barlohnumwandlung</span>
    @endif
    neu geregelt.
</p>
@if($offer->user->company->gross_conversion == 'netto')
    <h3 class="contract-title">2. Monatspauschale</h3>
@else
    <h3 class="contract-title">2. Bruttolohnumwandlung</h3>
@endif
@if($isFullySubsidied)
    <p class="p-with-mb">
        Das Fahrrad wird zusätzlich zum ohnehin geschuldeten Arbeitslohn gemäß §3 Ziffer 37 EstG überlassen.
    </p>
@else
    @if($offer->user->company->gross_conversion == 'netto')
        <p class="p-with-mb">
            Der Mitarbeiter entrichtet die o.g. nutzungsunabhängige Monatspauschale, die im Rahmen der Gehaltsabrechnung
            vom Lohn einbehalten wird.
        </p>
        <p class="p-with-mb">
            Die Monatspauschale ist erstmals mit dem auf die Übernahme des Fahrzeugs folgenden Monatsersten zu leisten
            und läuft auf die o.g. Laufzeit.
        </p>
        <p class="p-with-mb">
            Ein sich eventuell aus der Fahrradüberlassung ergebender geldwerter Vorteil unterliegt der Lohnsteuer- und
            Sozialversicherungspflicht durch den Mitarbeiter, was den Anspruch auf Barlohn zusätzlich verringert. Den
            Vertragsparteien ist bewusst, dass sich die Regelungen der Versteuerung auch während der Laufzeit der
            Überlassung ändern können.
        </p>
    @else
        <p class="p-with-mb">
            Der Mitarbeiter verzichtet für die Dauer der Überlassung, in entsprechender Abänderung des bestehenden
            Arbeitsvertrags, auf einen Teilbetrag seines laufenden Arbeitsentgeltes in Höhe des o.g.
            Umwandlungsbetrages. Die Entgeltumwandlung beginnt mit dem auf die Übernahme des Fahrzeugs folgenden
            Monatsersten und läuft auf die o.g. Laufzeit.
        </p>
        <p class="p-with-mb">
            Ein sich eventuell aus der Fahrradüberlassung ergebender geldwerter Vorteil unterliegt der Lohnsteuer- und
            Sozialversicherungspflicht durch den Mitarbeiter, was den Anspruch auf Barlohn zusätzlich verringert. Den
            Vertragsparteien ist bewusst, dass sich die Regelungen der Versteuerung auch während der Laufzeit der
            Überlassung ändern können.
        </p>
    @endif

@endif
<h3 class="contract-title">3. Privatnutzung</h3>
<p class="p-with-mb">
    Das Fahrzeug kann vom Mitarbeiter sowohl im Inland als auch im europäischen Ausland privat genutzt werden. Eine
    Nutzung durch Ehe- oder Lebenspartner oder andere Personen des Haushalts des Mitarbeiters ist bei
    gesamtschuldnerischer Mithaftung des Mitarbeiters zulässig.
</p>
<h3 class="contract-title">4. Übernahme und Beginn der Überlassung, Nutzung</h3>
<p class="p-with-mb">
    Die Überlassung steht unter der Bedingung der ordnungsgemäßen Lieferung des Fahrzeugs durch den Fachhändler und der
    Übernahme durch den Mitarbeiter. Der Mitarbeiter wird schon jetzt angewiesen und bevollmächtigt, im Namen des
    Arbeitgebers das Fahrzeug bei Auslieferung auf Mängel zu untersuchen und bei Mängelfreiheit die Übernahme zu
    bestätigen und den Leasinggeber zu beauftragen, den Kaufpreis des Fahrzeugs bei Fälligkeit an den benannten
    Lieferanten zu zahlen. Verweigert der Mitarbeiter dies pflichtwidrig, so hat er dem Arbeitgeber den daraus
    entstehenden Schaden zu ersetzen. Überlassen wird ein Fahrzeug zur vertragsmäßigen Nutzung, die sich insbesondere
    aus den Eigenschaften des Fahrzeugs, der Bedienungsanleitung und den Herstellerbestimmungen ergibt.
</p>
<p class="p-with-mb">
    Eventuelle kaufrechtliche Ansprüche aus § 439 BGB (Nacherfüllung) sind von dem Mitarbeiter gegenüber dem
    ausliefernden Fachhändler geltend zu machen. Der Mitarbeiter wird hierzu schon jetzt beauftragt und bevollmächtigt.
    Ein Aufwendungsersatz dafür wird ausgeschlossen. Der Mitarbeiter ist verpflichtet, den Arbeitgeber unverzüglich
    darüber zu informieren, wenn wegen eines Mangels der erste Nachbesserungsversuch gescheitert ist. Der Mitarbeiter
    darf einen Mangel nicht selbst beheben, da sonst die Gewährleistungsansprüche erlöschen.
</p>
<h3 class="contract-title">5. Allgemeine Nutzungsregelungen</h3>
<p class="p-with-mb">
    Die gesetzlichen Verkehrsbestimmungen sind einzuhalten und zu beachten. Der Mitarbeiter hat für einen stets
    betriebs- und verkehrssicheren Zustand des Fahrzeuges zu sorgen. Sämtliche vom Mitarbeiter oder Dritten verursachten
    Bußgelder oder sonstige Geldstrafen trägt der Mitarbeiter.
    Die Teilnahme an Sportveranstaltungen und Wettkämpfen ist nicht gestattet.
</p>
<h3 class="contract-title">6. Pflege und Wartung</h3>
<p class="p-with-mb">
    Das zur Nutzung überlassene Fahrzeug ist pfleglich zu behandeln und stets in betriebs- und verkehrssicherem Zustand
    zu erhalten. Das Fahrzeug ist regelmäßig einer ordnungsgemäßen Pflege und Wartung zu unterziehen. Entstehende Kosten
    hierfür trägt grundsätzlich der Mitarbeiter. Hat der Arbeitgeber in Verbindung mit dem Einzel-Leasingvertrag
    Dienstrad-Service mit vereinbart, hat der Mitarbeiter Anspruch auf die Serviceleistungen gemäß den Bedingungen des
    Merkblatts „Dienstrad-Service“. Dieses Merkblatt ist wesentlicher Bestandteil dieses Vertrages und wird dem
    Mitarbeiter durch den Arbeitgeber zur Verfügung gestellt. Der Mitarbeiter ist insbesondere verpflichtet, die
    jährliche Inspektion durchführen zu lassen.
    Die Kosten zur Wiederherstellung eines ordnungsgemäßen Zustands bei Rückgabe des Fahrzeugs trägt der Mitarbeiter.
</p>
<h3 class="contract-title">7. Versicherungsschutz</h3>
<p class="p-with-mb">
    Für das Fahrzeug hat der Arbeitgeber über den Leasinggeber eine Dienstrad-Vollkaskoversicherung abgeschlossen.
    Darüber ist das Fahrzeug gegen Beschädigungen, Verlust und Untergang versichert. Zum genauen Inhalt des
    Versicherungsschutzes siehe Merkblatt zum Rundumschutz eines Dienstrades.
    Das Merkblatt mit den Bedingungen zur Dienstrad-Vollkaskoversicherung ist beim Arbeitgeber zu erhalten bzw. wird dem
    Mitarbeiter auf Anforderung vom Leasinggeber übermittelt.
</p>
<p class="p-with-mb">
    Die Sicherung des Fahrzeuges vor Diebstahl oder Einbruchdiebstahl ist eine elementare Pflicht des Mitarbeiters. Er
    hat die Anschluss-/Verschlusspflichten gem. Ziffer 2.1. der Bedingungen zur Dienstrad-Vollkaskoversicherung zwingend
    einzuhalten.
</p>
<p class="p-with-mb">
    Bei einem Schaden am Fahrzeug oder versichertem Zubehör durch (Teile-) Diebstahl, Einbruchdiebstahl, Raub,
    Vandalismus oder Unfall hat dies der Mitarbeiter bei einer Polizeidienststelle zur Anzeige zu bringen und sich
    hierüber eine Bescheinigung aushändigen zu lassen.
    Der Mitarbeiter ist verpflichtet den Arbeitgeber unverzüglich zu informieren und der MLF Mercator-Leasing den
    Schaden zu melden und die notwendigen Dokumente zu übergeben. Dazu wurde von MLF Mercator-Leasing ein elektronisches
    Schadensportal unter: <a href="https://www.meinedienstrad-versicherung.de/">https://www.meinedienstrad-versicherung.de/</a>
    eingerichtet.
</p>
<p class="p-with-mb">
    Der Mitarbeiter trägt alle nicht von der Versicherung gedeckte und von ihm zu verantwortende Schäden, wie z.B.
    Schäden aus grober Fahrlässigkeit und Vorsatz sowie aus Verletzung der Versicherungsobliegenheiten. Der Mitarbeiter
    haftet für Schäden und eine Wertminderung des Fahrzeugs, die durch nicht vertragsgemäßem Gebrauch des Fahrzeugs
    entstehen.
</p>
<p class="p-with-mb">
    Bei einem Schaden am Fahrzeug oder versichertem Zubehör durch (Teile-) Diebstahl, Einbruchdiebstahl, Raub,
    Vandalismus oder Unfall hat dies der Mitarbeiter bei einer Polizeidienststelle zur Anzeige zu bringen und sich
    hierüber eine Bescheinigung aushändigen zu lassen.
</p>
<p class="p-with-mb">
    Der Mitarbeiter ist verpflichtet den Arbeitgeber unverzüglich zu informieren und das Formular Schadenanzeige
    vollständig ausgefüllt dem Arbeitgeber zu übergeben bzw. an den Leasinggeber zu übermitteln.
</p>
<h3 class="contract-title">8. Überlassung an Dritte, Rechte Dritter</h3>
<p class="p-with-mb">
    Die Überlassung des Fahrzeuges an Dritte ist unzulässig. Ausgenommen davon sind die Familienangehörigen gem. Ziffer
    3.<br>
    Verstößt der Mitarbeiter gegen diese Regelung, haftet er für jeden Schaden, die am Fahrzeug selbst oder im
    Zusammenhang mit der Fahrzeugbenutzung entsteht.<br>
    Der Mitarbeiter muss das Fahrzeug von Rechten Dritter freihalten. Er darf das Fahrzeug nicht vermieten, verpfänden,
    verleihen, verschenken, veräußern oder zur Sicherheit übereignen. Es bleibt während der gesamten Zeit der
    Überlassung Eigentum des Leasinggebers.
</p>
<h3 class="contract-title">9. Umbau / Tausch von Teilen / Manipulation des Motors eines E-Bikes</h3>
<p class="p-with-mb">
    Ein Umbau des Fahrzeugs ist nicht zulässig. Ein Anbau/Tausch von Sattel, Lenkergriffen, Pedalen, Klingel,
    Rückspiegel und/oder Tacho ist jedoch zulässig, sofern diese Teile der Erstausstattung gleichwertig oder höherwertig
    sind.
    <br>
    Die <strong>Manipulation des Motors eines E-Bikes</strong> ist weder vorübergehend noch dauerhaft
    gestattet. Jeglicher Verstoß führt dazu, dass der Mitarbeiter alleine für die
    rechtlichen Konsequenzen verantwortlich ist und dem Arbeitgeber jeglichen Schaden,
    der daraus entsteht, z.B. wegen Wegfall des Versicherungsschutzes oder Unmöglichkeit der
    Vermarktung während der Laufzeit oder am Ende der Laufzeit zu ersetzen hat.
</p>
<h3 class="contract-title">10. Rückgabepflicht / Ende der Überlassung</h3>
<p class="p-with-mb">Der Mitarbeiter ist verpflichtet, das Fahrzeug an den Arbeitgeber herauszugeben, wenn:</p>
<ul>
    <li>
        das Dienst-/Arbeitsverhältnisses beendet wird
        Scheidet der Mitarbeiter vor Ablauf des vereinbarten Überlassungszeitraums aus dem Arbeitsverhältnis aus, endet
        die Überlassung. <br>
        Im Falle der ordentlichen Kündigung des Arbeitsverhältnisses endet die Nutzungsberechtigung spätestens mit
        Ablauf der Kündigungsfrist bzw. bei einer fristlosen Kündigung mit deren Ausspruch. Die Erhebung einer
        Kündigungsschutzklage entbindet den Mitarbeiter nicht von der Herausgabepflicht. <br>
        Der Mitarbeiter verpflichtet sich, dem Arbeitgeber den Schaden zu ersetzen, der sich aus der vorzeitigen
        Auflösung des Vertrags ergibt, sofern die Beendigung des Arbeitsverhältnisses aus von ihm zu vertretenden
        Gründen erfolgt.
    </li>
    <li>
        der Mitarbeiter erheblich gegen Überlassungsbestimmungen verstößt oder aus sonstigen, in der Person des
        Mitarbeiters liegenden Gründen, wenn die Fortsetzung der Überlassung dem Arbeitgeber nicht mehr zugemutet werden
        kann.
    </li>
    <li>
        Der Mitarbeiter einen vollen Monat oder mehr kein Entgelt bezieht (z.B. während einer Freistellung aufgrund
        Elternzeit oder unbezahltem Urlaub). <br>
        Der Mitarbeiter kann die Rückgabe bei vorübergehenden Entfall des Arbeitsentgeltes verhindern, wenn er die
        monatlichen Gesamtaufwendungen des Arbeitgebers für jeden Monat ohne Entgeltzahlung direkt an den Arbeitgeber
        überweist.
        @if($offer->user->company->gross_conversion == 'brutto')
        <span>Der steuerliche Vorteil durch die Entgeltumwandlung entfällt für diesen Zeitraum.</span>
        @endif
        <br>
        Alternativ kann dem Mitarbeiter ein Ablöseangebot unterbreitet werden.
    </li>
</ul>
<p class="p-with-mb">Die Überlassung endet auch:</p>
<ul>
    <li>mit Ablauf der
        @if($offer->user->company->gross_conversion == 'netto')
            <span>Überlassungsvereinbarung</span>
        @else
            <span>Entgeltumwandlung</span>
        @endif
    </li>
    <li>Im Falle des Verlustes, der Entwendung oder Untergangs des Fahrzeuges, wenn wegen der Schwere oder wegen des
        Umfangs des Schadens wirtschaftlicher oder technischer Totalschaden vorliegt, oder bei schadensbedingten
        Reparaturkosten von mehr als 2/3 des Wiederbeschaffungswertes des Fahrzeuges.
    </li>
</ul>
<p class="p-with-mb">
    Der Mitarbeiter verpflichtet sich, bei Beendigung der Überlassung aus jeglichem Grund das Fahrzeug an einen vom
    Arbeitgeber benannten Dritten zurück zu senden. Die Kosten des Rückversands trägt der Mitarbeiter. <br>
    Das Fahrzeug muss bei der Rückgabe in einem seinem Alter und vertragsgemäßen Gebrauch entsprechenden
    Erhaltungszustand sowie frei von Schäden sein. Im Falle übermäßiger Abnutzung des Fahrzeuges hat der Mitarbeiter
    Ersatz zu leisten. <br>
    Wird das Fahrzeug nicht termingerecht zum Ende der Nutzungsberechtigung zurückgegeben, werden dem Mitarbeiter für
    jeden angefangenen Monat die Kosten einer anteiligen Monatsrate und die durch die Rückgabeverzögerung verursachten
    nachgewiesenen Kosten berechnet. Im Übrigen gelten während dieser Zeit die Pflichten des Mitarbeiters aus dieser
    Vereinbarung fort. <br>
    Die Geltendmachung eines Zurückbehaltungsrechtes ist ausgeschlossen.
</p>
<p class="p-with-mb">
    Alternativ dazu kann der Mitarbeiter das Fahrzeug erwerben, wenn ihm durch den Arbeitgeber oder einem Dritten ein
    entsprechendes Kaufangebot unterbreitet wird und der Mitarbeiter das Kaufangebot annimmt. Der Arbeitgeber kann dem
    Erwerb durch den Mitarbeiter nur aus wichtigen Gründen widersprechen. Ein Anspruch auf Erwerb des Fahrzeuges durch
    den Mitarbeiter besteht nicht.
</p>
<h3 class="contract-title">11. Helm</h3>
<p class="p-with-mb">Unabhängig von einer gesetzlichen Haftpflicht wird das Tragen eines geeigneten Helms empfohlen.</p>
@if($offer->user->company->s_pedelec_disable)
    <h3 class="contract-title">12. S-Pedelecs</h3>
    <p class="p-with-mb">
        S-Pedelecs sind nicht gestattet.
    </p>
@else
    <h3 class="contract-title">12. Sonderbestimmungen für S-Pedelecs</h3>
    <p class="p-with-mb">
        Sofern das Fahrzeug eine Fahrerlaubnis erfordert, verpflichtet sich der Mitarbeiter, diese vor Übernahme des Fahrzeugs und danach jeweils in halbjährlichem Abstand unaufgefordert dem Arbeitgeber vorzulegen. Sollte zu einem späteren Zeitpunkt ein Führerscheinentzug erfolgen, ist dies dem Arbeitgeber unverzüglich zur Kenntnis zu geben. Der Verlust der Fahrerlaubnis berechtigt nicht zur bzw. bewirkt nicht die Beendigung dieses Vertrags. Sofern das Fahrzeug ein Versicherungskennzeichen erfordert, liegt die Pflicht zur ordnungsgemäßen Versicherung samt Teilkaskoschutz und Kennzeichnung abweichend von bzw. ergänzend zu Punkt 7 bei dem Mitarbeiter.
    </p>
@endif
<h3 class="contract-title">13. Datenschutz</h3>
<p class="p-with-mb">
    Der Mitarbeiter hat die Datenschutzerklärung der
    <span class="td-color">{{ $portalName }}</span>
    die auch Verantwortlicher gem. Art. 4 Nr. 7 EU Datenschutzgrundverordnung und damit verantwortlich für die
    Einhaltung der Datenschutzbestimmungen, insbesondere der Betroffenenrechte, ist, im Zusammenhang mit seiner
    Registrierung im Dienstrad-Portal akzeptiert. Die personenbezogenen Daten des Mitarbeiters werden an Dritte nur
    weitergegeben oder sonst übermittelt, wenn dies zum Zwecke der Vertragserstellung und -durchführung im Zusammenhang
    mit Dienstrad-Überlassungen erforderlich ist, durch andere Gesetze vorgeschrieben wird oder der Mitarbeiter zuvor
    eingewilligt hat.
</p>
<h3 class="contract-title">14. Schlussbestimmungen</h3>
<p class="p-with-mb">
    Dieser Vertrag ist Bestandteil des Anstellungsvertrages. Soweit dieser Vertrag nichts Abweichendes regelt, gelten
    die Bestimmungen des Anstellungsvertrages. <br>
    Nebenabreden zu diesem Vertrag wurden nicht getroffen. Sollten eine oder mehrere der hier
    getroffenen Vereinbarungen unwirksam sein oder werden, so verpflichten sich die Vertragsparteien eine wirtschaftlich
    adäquate Lösung zu finden, ohne dass die übrigen Bestimmungen unwirksam werden.
</p>
<table class="field-value-table" style="margin-top: 15px;">
    <tr>
        <td class="field-value">{{ $offer->user->company->city->name }}, {{ $signatures['user'] }}</td>
    </tr>
</table>
<p>Ort, Datum</p>
<table cellpadding="0" cellspacing="0" class="t2">
    <tbody>
    <tr>
        <td class="tr19 td0">Unterschrift, Stempel Arbeitgeber</td>
        <td class="tr19 td29"></td>
        <td class="tr19 td0">Unterschrift Arbeitnehmer</td>
    </tr>
    </tbody>
</table>
<htmlpagefooter name="footer" id="footer">
    <table cellpadding="0" cellspacing="0" id="footer-table">
        <tbody>
        <tr>
            <td rowspan="2" class="contact-td">
                <p class="p1 ft11">
                    {{ $footer['name'] }}
                    @if ($footer['address'])
                        <span class="delimiter"> · </span>
                        {{ $footer['address'] }}
                    @endif
                    <span class="delimiter"> · </span>
                    @if ($footer['zip']) {{ $footer['zip'] }} @endif
                    {{ $footer['city'] }}
                    @if ($footer['phone'])
                        <span class="delimiter"> · </span>{{ $footer['phone'] }}
                    @endif
                </p>
            </td>
            <td class="tr1 page-number-date">
                <p class="page-number-p">Seite {PAGENO}/{nbpg}</p>
            </td>
        </tr>
        <tr>
            <td class="date-td page-number-date">
                <p class="date-p">Stand: {{ $footer['date'] }}</p>
            </td>
        </tr>
        </tbody>
    </table>
</htmlpagefooter>
</body>
</html>
