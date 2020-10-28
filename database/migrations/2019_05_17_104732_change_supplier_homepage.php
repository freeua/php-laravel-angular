<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeSupplierHomepage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('homepages')->where('type', \App\Portal\Models\Homepage::SUPPLIER_DEFAULT_HOMEPAGE)->update([
            'items' => json_encode([
                'title' => 'Herzlich willkommen im Fachhändlerbereich des Dienstrad-Portals',
                'description' => '<p>
                mit Ihrem Fachhändler-Zugang können Sie einfach und bequem Angebote an Kunden erstellen und direkt versenden, 
                den Genehmigungs-Status einer Bestellung einsehen und bei Verfügbarkeit des Dienstrads den Abholcode für den Kunden generieren.</p>
                <p>Bei der Abwicklung über das digitale Dienstrad-Portal werden Sie, der Kunde und dessen Arbeitgeber automatisiert über jeden 
                einzelnen Schritt des Bestellvorgangs per E-Mail informiert, vom Angebot bis zur Bezahlung.</p><p>Wussten Sie schon? Zusammen mit seinem Dienstrad 
                kann der Kunde auch ganz einfach Zubehör leasen. Mehr über leasingfähiges Zubehör erfahren Sie {{link}}</p>
                <p>{{link_to_order}}</p>
                <p>In wenigen Schritten zur Angebotserstellung:</p>',
                'steps' => [
                    [
                        'title' => 'Angebot erstellen',
                        'description'=> 'Erstellen Sie ein Angebot und senden es direkt über das Dienstrad-Portal unter Angabe der E-Mail-Adresse zum Kunden. Alternativ können Sie für den Kunden ein Angebot in Papierform erzeugen. Die Angebotsdaten können von ihm auch manuell eingegeben werden.'

                    ],
                    [
                        'title' => 'Genehmigung',
                        'description' => 'Der Kunde reicht das Dienstrad-Angebot mit dem Überlassungsvertrag zur Freigabe bei seinem Arbeitgeber ein.',
                    ],
                    [
                        'title' => 'Bestellung',
                        'description' => 'Mit der Freigabe des Überlassungsvertrags löst der Arbeitgeber automatisch die Bestellung des Dienstrads bei Ihnen aus. Sie werden per E-Mail informiert.',
                    ],
                    [
                        'title' => 'Abholcode',
                        'description' => 'Bei Verfügbarkeit des Dienstrads generieren Sie einen Abholcode für den Kunden, der automatisch darüber benachrichtigt wird, sein Dienstrad abzuholen.',
                    ],
                    [
                        'title' => 'Übernahme',
                        'description' => 'Der Kunde übernimmt sein Dienstrad bei Ihnen vor Ort mit seinem Abholcode und bestätigt damit die mängelfreie Übernahme.',
                    ],
                    [
                        'title' => 'Bezahlung',
                        'description' => 'Nach erfolgreicher Übernahme erfolgt die automatische Gutschrift des Rechnungsbetrags.',
                    ]
                ]
            ])
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('homepages')->where('type', \App\Portal\Models\Homepage::SUPPLIER_DEFAULT_HOMEPAGE)->update([
            'items' => json_encode([
                'title' => 'Herzlich willkommen im Fachhändlerbereich des Dienstrad-Portals',
                'description' => '<p>Mit Ihrem Fachhändler-Zugang können Sie einfach und bequem Angebote an Kunden erstellen und direkt versenden, den Genehmigungs-Status einer Bestellung einsehen und bei Verfügbarkeit des Dienstrads den Abholcode für den Kunden generieren.</p><p>Bei der Abwicklung über das digitale Dienstrad-Portal werden Sie, der Kunde und dessen Arbeitgeber automatisiert über jeden einzelnen Schritt des Bestellvorgangs per E-Mail informiert, vom Angebot bis zur Bezahlung.</p><p>Wussten Sie schon? Zusammen mit seinem Dienstrad kann der Kunde auch ganz einfach Zubehör leasen. Erfahren Sie mehr über leasingfähiges Zubehör {{link}}.</p><p>{{link_to_order}}</p>',
                'steps' => [
                    [
                        'title' => 'Angebot erstellen',
                        'description'=> 'Erstellen Sie ein Angebot und senden es direkt über das Dienstrad-Portal unter Angabe der E-Mail-Adresse zum Kunden. Alternativ können Sie dem Kunden ein Angebot in Papierform erzeugen und dieser gibt die Angebotsdaten manuell ein.'

                    ],
                    [
                        'title' => 'Genehmigung',
                        'description' => 'Der Kunde reicht das Dienstrad-Angebot mit dem Überlassungsvertrag zur Freigabe bei seinem Arbeitgeber ein.',
                    ],
                    [
                        'title' => 'Bestellung',
                        'description' => 'Mit der Freigabe des Überlassungsvertrags löst der Arbeitgeber automatisch die Bestellung des Dienstrads bei Ihnen aus. Sie werden per E-Mail informiert.',
                    ],
                    [
                        'title' => 'Abholcode',
                        'description' => 'Bei Verfügbarkeit des Dienstrads generieren Sie den Abholcode für den Kunden, dieser wird automatisch benachrichtigt sein Dienstrad abzuholen. Bei Versand des Dienstrads generieren Sie einen Übernahme-Code.',
                    ],
                    [
                        'title' => 'Übernahme',
                        'description' => 'Der Kunde übernimmt sein Dienstrad bei Ihnen vor Ort per Abholcode oder nimmt es per Spedition und Übernahmecode in Empfang.',
                    ],
                    [
                        'title' => 'Bezahlung',
                        'description' => 'Nach erfolgreicher Übernahme erfolgt die automatische Gutschrift des Rechnungsbetrags.',
                    ]
                ]
            ])
        ]);
    }
}
