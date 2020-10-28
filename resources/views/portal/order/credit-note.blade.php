<!--suppress ALL -->
<html>
<head>
    <style type="text/css">
        body {
            font-family: pt-sans;
            font-size: 12px;
            color: #434547;
        }

        .heading-space {
            height: 20px;
            text-align: right;
        }

        .heading {
            height: 128px;
            font-size: 10px;
            vertical-align: top;
        }

        .date {
            text-align: right;
        }

        .title {
            text-align: center;
            margin: 20px 0px;
        }

        .bold {
            font-weight: bold;
        }

        .field, .value {
            vertical-align: top;
        }

        .field-group {

            margin: 20px 0px;
        }

        .squared {
            padding: 2px;
            border: 1px solid #434547;
        }

        .text-right {
            text-align: right;
        }
    </style>
</head>
<body>
<table width="100%">
    <tr>
        <td width="60%" class="heading">

        </td>
        <td class="contact" rowspan="2">
        </td>
    </tr>
    <tr>
        <td class="heading-space"></td>
    </tr>
</table>
<?php /** @var \App\Leasings\Services\ViewModels\CreditNoteViewModel $view */?>
<table width="100%" class="field-group" cellspacing="0">

    <tr>
        <td width="65%">
            {{$view->supplierName}}
        </td>
        <td>
            <span class="field">GP-Nr.:</span> <span class="value bold">{{$view->gpNumber}}</span>
        </td>
    </tr>

    @if (!$view->isLeasingCreditNote())
        <tr>
            <td>

            </td>
            <td>
                <?php /** @var \App\Leasings\Services\ViewModels\TechnicalServiceNoteViewModel $view */?>
                <span class="field">Wartung/Service-code:</span> <span class="value bold">{{$view->serviceCode}}</span>
            </td>
        </tr>
    @else
        <tr>
            <td>

            </td>
            <td>
                <span class="field">Angebot-Nr.:</span> <span class="value bold">{{$view->offerNumber}}</span>
            </td>
        </tr>
    @endif

    <tr>
        <td>
            {{$view->supplierStreet}},
        </td>
        <td>
            @if ($view->isLeasingCreditNote())
                <?php /** @var \App\Leasings\Services\ViewModels\LeasingCreditNoteViewModel $view */?>
                <span class="field">Bestellung-Nr.:</span> <span class="value bold">{{$view->orderNumber}}</span>
            @endif
        </td>
    </tr>
    <tr>
        <td>
            {{$view->supplierPostalCode}} {{$view->supplierCity}}
        </td>
        <td>
            @if ($view->isLeasingCreditNote())
                <?php /** @var \App\Leasings\Services\ViewModels\LeasingCreditNoteViewModel $view */?>
                <span class="field">Gutschriftsdatum:</span> <span class="value bold">{{$view->pickupDate}}</span>
            @endif
        </td>
    </tr>
</table>
<table width="100%" class="title">
    <tr>
        <td colspan="2">
            <h1>Gutschrift</h1>
        </td>
    </tr>
</table>
<table width="100%" class="field-group">
    <tr>
        <td width="20%">
            <span class="field bold">Gutschrift-Nr.: </span>
        </td>
        <td>
            <span class="value bold">{{$view->creditNoteNumber}}</span>
        </td>
    </tr>
    <tr>
        <td>
            <span class="field">Ihre Ust-Nr.:</span>
        </td>
        <td>
            <span class="value bold">{{$view->supplierTaxId}}</span>
        </td>
    </tr>
</table>
<table width="100%" class="field-group">
    <tr>
        <td colspan="2">
            <p>Für nachstehendes, von Ihnen geliefertes Objekt schreiben wir Ihnen wie folgt gut:</p>
        </td>
    </tr>
</table>

<table width="100%" class="field-group" cellspacing="0">

    <tr>
        <td>
            <span class="field">Menge:</span>
        </td>
        <td>
            <span class="value">{{$view->productAmount}}</span>
        </td>
    </tr>
    <tr>
        <td>
            <span class="field">Kategorie:</span>
        </td>
        <td>
            <span class="value">{{$view->productCategory}}</span>
        </td>
        <td>
            <span class="field">Marke:</span>
        </td>
        <td>
            <span class="value">{{$view->productBrand}}</span>
        </td>
    </tr>
    <tr>
        <td>
            <span class="field">Modell:</span>
        </td>
        <td>
            <span class="value">{{$view->productModel}}</span>
        </td>
        <td>
            <span class="field">Größe:</span>
        </td>
        <td>
            <span class="value">{{$view->productSize}}</span>
        </td>
    </tr>
    <tr>
        <td>
            <span class="field">Farbe:</span>
        </td>
        <td>
            <span class="value">{{$view->productColor}}</span>
        </td>
        <td>
            <span class="field">Rahmennummer:</span>
        </td>
        <td>
            <span class="value">{{$view->productSerialNumber}}</span>
        </td>
    </tr>
    <tr>
        <td class="field">
            <span>Zubehör:</span>
        </td>
        <td class="value" colspan="3">
            <span>{!! $view->accessories !!}</span>
        </td>

    </tr>
</table>

<table width="100%" class="field-group" cellspacing="0">
    <tr>
        <td width="30%">
            @if (!$view->isLeasingCreditNote())
                <span class="field">Lieferdatum:</span>
            @else
                <span class="field">Lieferdatum/Übernahmedatum:</span>
            @endif
        </td>
        <td>
            @if (!$view->isLeasingCreditNote())
                <?php /** @var \App\Leasings\Services\ViewModels\TechnicalServiceNoteViewModel $view */?>
                <span class="value">{{$view->technicalServiceCreated}}</span>
            @else
                <?php /** @var \App\Leasings\Services\ViewModels\LeasingCreditNoteViewModel $view */?>
                <span class="value">{{$view->contractDate}}</span>
            @endif
        </td>
    </tr>
</table>

<table width="100%" class="field-group" cellspacing="0">
    <tr>
        <td width="40%">
            <span class="field">Nettobetrag:</span>
        </td>
        <td width="10%" class=" value text-right">
            {{$view->netTotal}}
        </td>
        <td width="50%">
        </td>
    </tr>
    <tr>
        <td width="40%">
            <span class="field">Umsatzsteuer <span class="squared">19 %</span>:</span>
        </td>
        <td width="10%" class="value text-right">
            {{$view->vatTotal}}
        </td>
        <td width="50%">
        </td>
    </tr>
    </tr>
</table>

<table width="100%" class="field-group" cellspacing="0">
    <tr>
        <td width="40%">
            <span class="field bold">Gutschriftsbetrag:</span>
        </td>
        <td width="10%" class="value bold text-right">
            {{$view->grossTotal}}
        </td>
        <td width="50%">
        </td>
    </tr>
</table>

<table width="100%" class="field-group" cellspacing="0">
    <tr colspan="2">
        <td class="important-field">
            <p>Der Gutschriftbetrag wird Ihnen umgehend auf Ihre bei uns hinterlegte Bankverbindung
                <span class="bold">{{$view->supplierBankName}}</span> IBAN: <span
                        class="bold">{{$view->supplierBankAccount}}</span> überwiesen. Mit der
                Überweisung geht das Eigentum am Objekt uneingeschränkt auf uns über.
            </p>
        </td>
    </tr>
</table>

<table width="100%" class="field-group" cellspacing="0">

    <tr>
        <td>
            <span class="field">Leasingnehmer:</span>
        </td>
    </tr>
    <tr>
        <td>
            <span class="field">{{$view->lesseeGpNumber}}</span>
        </td>
    </tr>
    <tr>
        <td>
            <span class="field">{{$view->lesseeBoniNumber}}</span>
        </td>
    </tr>
    <tr>
        <td>
            <span class="field">{{$view->lesseeCompany}}</span>
        </td>
    </tr>
    <tr>
        <td>
            <span class="field">{{$view->lesseeStreet}},</span>
        </td>
    </tr>
    <tr>
        <td>
            <span class="field">{{$view->lesseePostalCode}} {{$view->lesseeCity}}</span>
        </td>
    </tr>
</table>

<table width="100%" class="field-group" cellspacing="0">

    <tr>
        <td>
            <span class="field">Mitarbeiter:</span>
        </td>
    </tr>
    <tr>
        <td>
            <span class="field">{{$view->employeeId}}</span>
        </td>
    </tr>
    <tr>
        <td>
            <span class="field">{{$view->employeeName}}</span>
        </td>
    </tr>
    <tr>
        <td>
            <span class="field">{{$view->employeeStreet}},</span>
        </td>
    </tr>
    <tr>
        <td>
            <span class="field">{{$view->employeePostalCode}} {{$view->employeeCity}}</span>
        </td>
    </tr>
</table>
<table width="100%" class="field-group" cellspacing="0">
    <tr colspan="2">
        <td class="important-field">
            <p>
                Diese Gutschrift wurde digital erstellt und bedarf keiner Unterschrift.
            </p>
        </td>
    </tr>
</table>
</body>
</html>
