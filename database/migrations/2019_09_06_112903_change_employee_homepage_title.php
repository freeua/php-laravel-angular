<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeEmployeeHomepageTitle extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('homepages', function (Blueprint $table) {
            DB::table('homepages')->where('type', \App\Portal\Models\Homepage::EMPLOYEE_DEFAULT_HOMEPAGE)->update([
                'items' => json_encode([
                    'title' => 'Herzlich willkommen {{fullName}},',
                    'description' =>
                        "<p>
                    der {{company}}. Über das digitale Dienstrad-Portal können Sie ganz einfach und bequem Ihr neues Dienstrad beziehen. 
                    Ihr Arbeitgeber überlässt Ihnen ganz unkompliziert Ihr Dienstrad zur betrieblichen und privaten Nutzung per monatlicher Gehaltsumwandlung. 
                    Hierbei sparen Sie maßgeblich gegenüber einem Direktkauf.
                </p>                
                <p>
                    Bei der Abwicklung über das Dienstrad-Portal werden Sie, Ihr Arbeitgeber und der
                    Fachhändler automatisch über jeden einzelnen Schritt des Bestellvorgangs per E-Mail
                    informiert: vom Angebot bis zur Übernahme Ihres Fahrrads.
                </p>
                <p>                 
                    Wussten Sie schon? Sie können zusammen mit Ihrem Dienstrad auch ganz einfach Zubehör
                    beziehen. Mehr über leasingfähiges Zubehör erfahren Sie {{link}}.
                </p>
                <p>In wenigen Schritten zu Ihrem Dienstrad:
                </p>",
                    'steps' => [
                        [
                            'title' => 'Angebot',
                            'description' => "Lassen Sie sich von Ihrem Fachhändler ein Angebot für Ihr Wunschrad direkt über das Dienstrad-Portal erstellen <u>oder</u> stellen Sie hier (als Link) einen „Neuen Dienstrad-Antrag“ und laden Sie das ausgehändigte Angebot Ihres Fachhändlers in Papierform hoch.",
                        ],
                        [
                            'title' => 'Überlassungsvertrag',
                            'description' => 'Klicken Sie auf „Angebot akzeptieren“. Damit generieren Sie automatisch Ihren Überlassungsvertrag. Bitte drucken Sie den Überlassungsvertrag aus oder speichern Sie diesen lokal ab.',
                        ],
                        [
                            'title' => 'Unterschrift',
                            'description' => 'Nachdem Sie Ihren Überlassungsvertrag unterschrieben haben, scannen sie ihn ein und laden ihn anschließend wieder in das Dienstrad-Portal hoch.',
                        ],
                        [
                            'title' => 'Bestellung',
                            'description' => 'Ihr Arbeitgeber erhält automatisch den Überlassungsvertrag zur Freigabe und löst damit die Bestellung Ihres Fahrrads aus.',
                        ],
                        [
                            'title' => 'Übernahme',
                            'description' => 'Bei Verfügbarkeit Ihres Fahrrads erhalten Sie automatisch einen Abholcode von Ihrem Fachhändler per E-Mail. Mit der Eingabe Ihres Abholcodes beim Fachhändler bestätigen Sie die mängelfreie Übernahme. Bitte nehmen Sie bei der Abholung einen gültigen Ausweis zur Identifikation mit.',
                        ],
                        [
                            'title' => 'Wir wünschen gute Fahrt mit Ihrem Dienstrad!',
                            'description' => 'Was tun im Schadensfall? Wir haben für Sie ein <a href="https://www.meinedienstrad-versicherung.de/">Portal</a> eingerichtet, mit dem Sie einfach, schnell und von überall einen Schaden melden können, um Ihnen den Prozess eine Schadensmeldung zu erleichtern.',
                        ],
                    ]
                ])
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('homepages', function (Blueprint $table) {
            DB::table('homepages')->where('type', \App\Portal\Models\Homepage::EMPLOYEE_DEFAULT_HOMEPAGE)->update([
                'items' => json_encode([
                    'title' => 'Herzlich willkommen im Dienstrad-Portal,',
                    'description' =>
                        "<p>
                    der {{company}}. Über das digitale Dienstrad-Portal können Sie ganz einfach und bequem Ihr neues Dienstrad beziehen. 
                    Ihr Arbeitgeber überlässt Ihnen ganz unkompliziert Ihr Dienstrad zur betrieblichen und privaten Nutzung per monatlicher Gehaltsumwandlung. 
                    Hierbei sparen Sie maßgeblich gegenüber einem Direktkauf.
                </p>                
                <p>
                    Bei der Abwicklung über das Dienstrad-Portal werden Sie, Ihr Arbeitgeber und der
                    Fachhändler automatisch über jeden einzelnen Schritt des Bestellvorgangs per E-Mail
                    informiert: vom Angebot bis zur Übernahme Ihres Fahrrads.
                </p>
                <p>                 
                    Wussten Sie schon? Sie können zusammen mit Ihrem Dienstrad auch ganz einfach Zubehör
                    beziehen. Mehr über leasingfähiges Zubehör erfahren Sie {{link}}.
                </p>
                <p>In wenigen Schritten zu Ihrem Dienstrad:
                </p>",
                    'steps' => [
                        [
                            'title' => 'Angebot',
                            'description' => "Lassen Sie sich von Ihrem Fachhändler ein Angebot für Ihr Wunschrad direkt über das Dienstrad-Portal erstellen <u>oder</u> stellen Sie hier (als Link) einen „Neuen Dienstrad-Antrag“ und laden Sie das ausgehändigte Angebot Ihres Fachhändlers in Papierform hoch.",
                        ],
                        [
                            'title' => 'Überlassungsvertrag',
                            'description' => 'Klicken Sie auf „Angebot akzeptieren“. Damit generieren Sie automatisch Ihren Überlassungsvertrag. Bitte drucken Sie den Überlassungsvertrag aus oder speichern Sie diesen lokal ab.',
                        ],
                        [
                            'title' => 'Unterschrift',
                            'description' => 'Nachdem Sie Ihren Überlassungsvertrag unterschrieben haben, scannen sie ihn ein und laden ihn anschließend wieder in das Dienstrad-Portal hoch.',
                        ],
                        [
                            'title' => 'Bestellung',
                            'description' => 'Ihr Arbeitgeber erhält automatisch den Überlassungsvertrag zur Freigabe und löst damit die Bestellung Ihres Fahrrads aus.',
                        ],
                        [
                            'title' => 'Übernahme',
                            'description' => 'Bei Verfügbarkeit Ihres Fahrrads erhalten Sie automatisch einen Abholcode von Ihrem Fachhändler per E-Mail. Mit der Eingabe Ihres Abholcodes beim Fachhändler bestätigen Sie die mängelfreie Übernahme. Bitte nehmen Sie bei der Abholung einen gültigen Ausweis zur Identifikation mit.',
                        ],
                        [
                            'title' => 'Wir wünschen gute Fahrt mit Ihrem Dienstrad!',
                            'description' => 'Was tun im Schadensfall? Wir haben für Sie ein <a href="https://www.meinedienstrad-versicherung.de/">Portal</a> eingerichtet, mit dem Sie einfach, schnell und von überall einen Schaden melden können, um Ihnen den Prozess eine Schadensmeldung zu erleichtern.',
                        ],
                    ]
                ])
            ]);
        });
    }
}
