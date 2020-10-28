<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SeedHomepagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $portalDefault = [
            'type' => \App\Portal\Models\Homepage::PORTAL_DEFAULT_HOMEPAGE,
            'items' => json_encode([
                'title' => 'Lorem ipsum dolor sit amet, consectetur',
                'description' => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>',
                'steps' => [
                    [
                        'title' => 'Lorem ipsum',
                        'description'=> 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore'

                    ],
                    [
                        'title' => 'Lorem ipsum',
                        'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore',
                    ],
                    [
                        'title' => 'Lorem ipsum',
                        'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore',
                    ],
                    [
                        'title' => 'Lorem ipsum',
                        'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore',
                    ],
                    [
                        'title' => 'Lorem ipsum',
                        'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore',
                    ],
                    [
                        'title' => 'Lorem ipsum',
                        'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore',
                    ]
                ]
            ])
        ];
        DB::table('homepages')->insert($portalDefault);

        $supplierDefault = [
            'type' => \App\Portal\Models\Homepage::SUPPLIER_DEFAULT_HOMEPAGE,
            'items' => json_encode([
                'title' => 'Herzlich willkommen im Fachhändlerbereich des Dienstrad-Portals',
                'description' => '<p>Mit Ihrem Fachhändler-Zugang können Sie einfach und bequem Angebote an Kunden erstellen und direkt versenden, den Genehmigungs-Status einer Bestellung einsehen und bei Verfügbarkeit des Dienstrads den Abholcode für den Kunden generieren.</p><p>Bei der Abwicklung über das digitale Dienstrad-Portal werden Sie, der Kunde und dessen Arbeitgeber automatisiert über jeden einzelnen Schritt des Bestellvorgangs per E-Mail informiert, vom Angebot bis zur Bezahlung.</p><p>Wussten Sie schon? Zusammen mit seinem Dienstrad kann der Kunde auch ganz einfach Zubehör leasen. Erfahren Sie mehr über leasingfähiges Zubehör {{link}}.</p><p><a href="/lieferanten/admin/orders?status_id=14" class="ng-star-inserted">Derzeit gibt es keinen offenen Bestellungen</a></p>',
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
        ];
        DB::table('homepages')->insert($supplierDefault);

        $companyDefault = [
            'type' => \App\Portal\Models\Homepage::COMPANY_DEFAULT_HOMEPAGE,
            'items' => json_encode([
                'title' => 'Herzlich willkommen im Unternehmensbereich des Dienstrad-Portals',
                'description' => "<p>als Firmen-Administrator können Sie ganz einfach und bequem Bestellungen freigeben, die Registrierungen und Dienstrad-Dokumente Ihrer Mitarbeiter verwalten sowie Ihre Firmeneinstellungen anpassen.</p><p>Bei der Abwicklung über das digitale Dienstrad-Portal werden Sie, Ihre Mitarbeiter und der Fachhändler automatisiert über jeden einzelnen Schritt des Bestellvorgangs per E-Mail informiert, vom Angebot bis zum Einzelleasingvertrag.</p>
                                    <p>Wussten Sie schon? Zusammen mit dem Dienstrad können Ihre Mitarbeiter auch ganz einfach Zubehör beziehen. Erfahren Sie mehr über leasingfähiges Zubehör {{link}}.</p>",
                'steps' => [
                    [
                        'title' => 'Registrierung',
                        'description' => 'Genehmigen Sie die Registrierung Ihrer Mitarbeiter zur Teilnahme am Dienstrad-Programm.'
                    ],
                    [
                        'title' => 'Überlassungsvertrag',
                        'description' => 'Ihr Mitarbeiter reicht sein Dienstrad-Angebot mit dem Überlassungsvertrag zur Freigabe ein, Sie werden automatisch per E-Mail informiert.',
                    ],
                    [
                        'title' => 'Bestellung',
                        'description' => 'Mit der Freigabe des Überlassungsvertrags lösen Sie automatisch die Bestellung des Dienstrads beim Fachhändler aus.',
                    ],
                    [
                        'title' => 'Übernahme',
                        'description' => 'Ihr Mitarbeiter übernimmt sein Dienstrad beim Fachhändler per Abholcode oder nimmt es per Spedition und Übernahmecode in Empfang.',
                    ],
                    [
                        'title' => 'Einzel-Leasingvertrag',
                        'description' => 'Durch die Eingabe des Abholcodes durch den Mitarbeiter wird automatisch der Antrag zum Einzelleasingvertrag generiert. Die Vertragsunterlagen erhalten Sie per E-Mail.',
                    ],
                ]
            ])
        ];
        DB::table('homepages')->insert($companyDefault);

        $employeeDefault = [
            'type' => \App\Portal\Models\Homepage::EMPLOYEE_DEFAULT_HOMEPAGE,
            'items' => json_encode([
                'title' => 'Herzlich willkommen im Dienstrad-Portal',
                'description' => 
                "<p>
                    der Firma {{company}}. Über das digitale Dienstrad-Portal können Sie ganz einfach und bequem Ihr neues Dienstrad beziehen. 
                    Ihr Arbeitgeber überlässt Ihnen ganz unkompliziert Ihr Dienstrad betrieblich und privat per monatlicher Gehaltsumwandlung, 
                    hierbei sparen Sie maßgeblich gegenüber einem Direktkauf.
                </p>
                <p>
                    Bei der Abwicklung über das digitale Dienstrad-Portal werden Sie, Ihr Arbeitgeber und der Fachhändler automatisiert über jeden einzelnen 
                    Schritt des Bestellvorgangs per E-Mail informiert, vom Angebot bis zur Übernahme.
                </p>
                <p>
                    Wussten Sie schon? Sie können zusammen mit Ihrem 
                    Dienstrad auch ganz einfach Zubehör beziehen. Erfahren Sie mehr über leasingfähiges Zubehör {{link}}.
                </p>",
                'steps' => [
                    [
                        'title' => 'Angebot',
                        'description' => "Lassen Sie sich von Ihrem Fachhändler das Angebot für Ihr Wunschrad direkt über das Dienstrad-Portal 
                        erstellen oder stellen Sie einen neuen Dienstrad-Antrag und laden ein in Papierform oder als PDF (per E-Mail) erhaltenes Angebot hoch.",
                    ],
                    [
                        'title' => 'Überlassungsvertrag',
                        'description' => 'Klicken Sie auf Angebot akzeptieren und erzeugen damit automatisch Ihren Überlassungsvertrag. Drucken Sie den Überlassungsvertrag jetzt aus.',
                    ],
                    [
                        'title' => 'Unterschrift',
                        'description' => 'Nachdem Sie Ihren Überlassungsvertrag unterschrieben haben, scannen Sie ihn ein und laden ihn anschließend wieder in das Dienstrad-Portal hoch.',
                    ],
                    [
                        'title' => 'Bestellung',
                        'description' => 'Ihr Arbeitgeber erhält automatisch den Überlassungsvertrag zur Freigabe und löst die Bestellung aus.',
                    ],
                    [
                        'title' => 'Übernahme',
                        'description' => 'Übernehmen Sie Ihr Dienstrad beim Fachhändler per Abholcode oder nehmen es per Spedition in Empfang und bestätigen mit dem Übernahmecode.',
                    ],
                    [
                        'title' => 'Wir wünschen gute Fahrt mit Ihrem Dienstrad!',
                        'description' => '',
                    ],
                ]
            ])
        ];
        DB::table('homepages')->insert($employeeDefault);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('homepages')->where('type', \App\Portal\Models\Homepage::PORTAL_DEFAULT_HOMEPAGE)->delete();
        DB::table('homepages')->where('type', \App\Portal\Models\Homepage::SUPPLIER_DEFAULT_HOMEPAGE)->delete();
        DB::table('homepages')->where('type', \App\Portal\Models\Homepage::COMPANY_DEFAULT_HOMEPAGE)->delete();
        DB::table('homepages')->where('type', \App\Portal\Models\Homepage::EMPLOYEE_DEFAULT_HOMEPAGE)->delete();
    }
}
