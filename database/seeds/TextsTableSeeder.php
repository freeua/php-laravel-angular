<?php

use Illuminate\Database\Seeder;

class TextsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        \DB::table('texts')->delete();
        
        \DB::table('texts')->insert(array (
            0 => 
            array (
                'id' => 1,
                'data' => '{"key": "firma_dashboard", "title": "Dashboard", "subtitle": "Firmenadministrator", "description": "Titel und Untertitel im Dashboard-Bereich des Unternehmens"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-08-18 07:43:43',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'data' => '{"key": "firma_mitarbeiter", "title": "Mitarbeiter verwalten", "subtitle": "", "description": "Titel und Untertitel im Mitarbeiterbereich des Unternehmens"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-08-18 23:49:25',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'data' => '{"key": "firma_mitarbeiter_detail", "title": "Mitarbeiter", "subtitle": "", "description": "Titel und Untertitel im Detailbereich der Mitarbeiter des Unternehmens"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-08-18 23:52:32',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'data' => '{"key": "firma_angebote", "title": "Angebote verwalten", "subtitle": "", "description": "Titel und Untertitel im Angebotsbereich des Unternehmens"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-08-18 23:56:21',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'data' => '{"key": "firma_angebote_detail", "title": "Angebot", "subtitle": "", "description": "Titel und Untertitel im Detailbereich des Unternehmensangebots"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-08-19 00:06:11',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            5 => 
            array (
                'id' => 6,
                'data' => '{"key": "firma_bestellungen", "title": "Bestellungen", "subtitle": "", "description": "Titel und Untertitel im Abschnitt Firmenbestellungen"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-08-19 00:08:49',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            6 => 
            array (
                'id' => 7,
                'data' => '{"key": "firma_bestellungen_detail", "title": "Bestellung", "subtitle": "", "description": "Titel und Untertitel im Detailbereich der Unternehmensaufträge"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-08-19 00:15:35',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            7 => 
            array (
                'id' => 8,
                'data' => '{"key": "firma_verträge", "title": "Verträge", "subtitle": "", "description": "Titel und Untertitel im Abschnitt Unternehmensverträge"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-08-19 00:18:12',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            8 => 
            array (
                'id' => 9,
                'data' => '{"key": "firma_verträge_detail", "title": "Vertrag", "subtitle": "", "description": "Titel und Untertitel in den Detailabschnitt Unternehmensverträge einfügen"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-08-19 00:40:41',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            9 => 
            array (
                'id' => 10,
                'data' => '{"key": "firma_dokumente", "title": "Dokumente", "subtitle": "", "description": "Titel und Untertitel im Abschnitt Unternehmensdokumente"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-08-19 00:42:38',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            10 => 
            array (
                'id' => 11,
                'data' => '{"key": "firma_häufig_gestellte_fragen", "title": "Häufig gestellte Fragen", "subtitle": "", "description": "Titel und Untertitel in das Unternehmen Häufig gestellte Fragen Abschnitt"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-08-19 00:49:00',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            11 => 
            array (
                'id' => 12,
                'data' => '{"key": "firma_faq_kategorien", "title": "FAQ-Kategorien", "subtitle": "", "description": "Titel und Untertitel im Abschnitt Firma Neu Häufig gestellte Fragekategorien"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-08-19 00:54:37',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            12 => 
            array (
                'id' => 13,
                'data' => '{"key": "firma_einstellungen", "title": "Einstellungen", "subtitle": "", "description": "Titel und Untertitel im Abschnitt Firmeneinstellungen"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-08-19 07:01:08',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            13 => 
            array (
                'id' => 14,
                'data' => '{"key": "firma_lieferanten", "title": "Lieferanten", "subtitle": "", "description": "Titel und Untertitel im Anbieterbereich des Unternehmens"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-08-19 07:03:27',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            14 => 
            array (
                'id' => 15,
                'data' => '{"key": "firma_lieferanten_detail", "title": "Lieferant", "subtitle": "", "description": "Titel und Untertitel im Detail des Unternehmensanbieters"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-08-19 07:04:47',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            16 => 
            array (
                'id' => 17,
                'data' => '{"key": "firma_mein_account", "title": "Mein Account", "subtitle": "", "description": "Titel und Untertitel in der Sektion Firma Mein Konto"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-08-19 07:09:17',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            17 => 
            array (
                'id' => 18,
                'data' => '{"key": "firma_benachrichtigungen", "title": "Ihre Benachrichtigunge", "subtitle": "", "description": "Titel und Untertitel im Abschnitt Unternehmensmeldungen"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-08-19 07:18:24',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            18 => 
            array (
                'id' => 19,
                'data' => '{"key": "firma_benachrichtigungen_detail", "title": "Ihre Benachrichtigunge", "subtitle": "", "description": "Titel und Untertitel in den Details der Unternehmensmeldungen"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-08-19 07:19:36',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            19 => 
            array (
                'id' => 20,
                'data' => '{"key": "mitarbeiter_fahrradhändler", "title": "Fahrradhändler", "subtitle": "Bei der Auswahl Ihres Dienstrads können Sie unter folgenden Lieferanten auswählen, die Ihnen in dieser Übersicht angezeigt werden.", "description": "Titel und Untertitel im Bereich Mitarbeiterfahrradladen"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-08-19 15:32:46',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            20 => 
            array (
                'id' => 21,
                'data' => '{"key": "mitarbeiter_neues_angebot", "title": "Neuen Dienstrad-Antrag stellen", "subtitle": "Sie haben ihr Angebot in Papierform? \nLaden Sie es hier hoch und beantragen Sie ihr Dienstrad \nIhr Fachhändler hat Ihnen kein Angebot Direkt über das Dienstrad-Portal erstellt? Hier können Sie selbst einen neuen Dienstrad-Antrag stellen. Laden Sie am Seitenende das ausgehändigte Angebot Ihres Fachhändlers in Papierform hoch und klicken dann auf \'Angebot akzeptieren und Überlassungsvertrag erstellen\' oder speichern Sie es ab. Im nächsten Schritt wird Ihnen der erzeugte Überlassungsvertrag und die weiteren Schritte angezeigt.", "description": "Titel und Untertitel des neuen Angebots des Mitarbeiters"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-08-19 15:40:18',
                'updated_at' => '2019-08-19 17:40:53',
                'deleted_at' => NULL,
            ),
            21 => 
            array (
                'id' => 22,
                'data' => '{"key": "mitarbeiter_angebote", "title": "Angebote", "subtitle": "", "description": "Titel und Untertitel im Abschnitt Mitarbeiterangebote"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-08-19 15:43:57',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            22 => 
            array (
                'id' => 23,
                'data' => '{"key": "mitarbeiter_angebote_detail", "title": "Angebot", "subtitle": "", "description": "Titel und Untertitel in Mitarbeiter bietet Detailinformationen"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-08-19 15:45:42',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            23 => 
            array (
                'id' => 24,
                'data' => '{"key": "mitarbeiter_bestellungen", "title": "Bestellungen", "subtitle": "", "description": "Titel und Untertitel im Abschnitt Mitarbeiteraufträge"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-08-19 15:48:42',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            24 => 
            array (
                'id' => 25,
                'data' => '{"key": "mitarbeiter_bestellungen_detail", "title": "Bestellung", "subtitle": "", "description": "Titel und Untertitel in den Details der Mitarbeiteraufträge"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-08-19 15:49:11',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            25 => 
            array (
                'id' => 26,
                'data' => '{"key": "mitarbeiter_verträge", "title": "Verträge", "subtitle": "", "description": "Titel und Untertitel im Abschnitt Mitarbeiterverträge"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-08-19 15:54:39',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            26 => 
            array (
                'id' => 27,
                'data' => '{"key": "mitarbeiter_verträge_detail", "title": "Vertrag", "subtitle": "", "description": "Titel und Untertitel in den Details zum Mitarbeitervertrag"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-08-19 15:57:02',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            27 => 
            array (
                'id' => 28,
                'data' => '{"key": "mitarbeiter_dokumente", "title": "Dokumente", "subtitle": "Hier haben Sie einen Überblick über alle Dokumente, die Ihnen von Ihrem Arbeitgeber (unternehmensinterne Informationen zum Dienstrad-Programm, Merkblätter etc.) zur Verfügung gestellt werden. Des Weiteren werden hier Ihre unterzeichneten und hochgeladenen Überlassungsverträge sowie die vom System erzeugten Übernahmebestätigungen angezeigt.", "description": "Titel und Untertitel im Bereich Mitarbeiterdokumente"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-08-19 16:13:17',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            28 => 
            array (
                'id' => 29,
                'data' => '{"key": "mitarbeiter_häufig_gestellte_fragen", "title": "Häufig gestellte Fragen", "subtitle": "", "description": "Titel und Untertitel in den Abschnitt Häufig gestellte Fragen zum Mitarbeiter"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-08-19 16:18:22',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            29 => 
            array (
                'id' => 30,
                'data' => '{"key": "mitarbeiter_mein_account", "title": "Mein Account", "subtitle": "Hier können Sie Ihre persönlichen Informationen vervollständigen. Bitte füllen Sie daher alle Pflichtfelder aus, da diese Daten für die Erstellung welterer vertraglicher Dokumente relevant sind. Ihre Privatadresse wird insbesondere für die Erstellung des Überlassungsvertrages benötigt.", "description": "Titel und Untertitel in den Bereich Mitarbeiter Mein Konto"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-08-19 16:24:07',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            30 => 
            array (
                'id' => 31,
                'data' => '{"key": "mitarbeiter_benachrichtigungen", "title": "Ihre Benachrichtigunge", "subtitle": "", "description": "Titel und Untertitel im Abschnitt Benachrichtigungen für Mitarbeiter"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-08-19 16:32:07',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            31 => 
            array (
                'id' => 32,
                'data' => '{"key": "mitarbeiter_benachrichtigungen_detail", "title": "Ihre Benachrichtigunge", "subtitle": "", "description": "Titel und Untertitel in den Details der Mitarbeiterbenachrichtigung"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-08-19 16:32:27',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            32 => 
            array (
                'id' => 33,
                'data' => '{"key": "portal_dashboard", "title": "Dashboard", "subtitle": "Portaladministrator", "description": "Titel und Untertitel im Dashboard-Bereich des Portals"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-08-19 23:19:48',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            33 => 
            array (
                'id' => 34,
                'data' => '{"key": "portal_firmen", "title": "Firmenübersicht", "subtitle": "", "description": "Titel und Untertitel im Portal Firmenbereich des Portals"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-08-19 23:21:48',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            34 => 
            array (
                'id' => 35,
                'data' => '{"key": "portal_firmen_detail", "title": "Firma", "subtitle": "", "description": "Titel und Untertitel im Firmendetail des Portals"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-08-19 23:24:42',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            35 => 
            array (
                'id' => 36,
                'data' => '{"key": "portal_firmen_neue", "title": "Neue Firma anlegen", "subtitle": "", "description": "Titel und Untertitel im Portal Firma Neu"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-08-19 23:29:45',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            36 => 
            array (
                'id' => 37,
                'data' => '{"key": "portal_lieferanten", "title": "Lieferantenübersicht", "subtitle": "", "description": "Titel und Untertitel im Bereich Portal-Lieferanten"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-08-19 23:31:29',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            37 => 
            array (
                'id' => 38,
                'data' => '{"key": "portal_lieferanten_detail", "title": "Lieferant", "subtitle": "", "description": "Titel und Untertitel in den Details der Portalanbieter"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-08-19 23:34:17',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            38 => 
            array (
                'id' => 39,
                'data' => '{"key": "portal_lieferanten_neue", "title": "Lieferanten hinzufügen", "subtitle": "", "description": "Titel und Untertitel in Portalanbieter Neu"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-08-19 23:34:56',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            39 => 
            array (
                'id' => 40,
                'data' => '{"key": "portal_benutzer_bearbeiten", "title": "Benutzer bearbeiten", "subtitle": "Systemadministrator", "description": "Titel und Untertitel in der Ausgabe eines Benutzers im Portal"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-09-09 11:44:56',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            40 => 
            array (
                'id' => 41,
                'data' => '{"key": "portal_benutzer_ubersicht", "title": "Benutzerübersicht", "subtitle": "", "description": "Titel und Untertitel in den Lebenslauf des Portalbenutzers einfügen"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-08-19 23:40:23',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            41 => 
            array (
                'id' => 42,
                'data' => '{"key": "portal_benutzer_neue", "title": "Benutzer hinzufügen", "subtitle": "Systemadministrator", "description": "Titel und Untertitel Portalbenutzer Neu"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-08-19 23:42:24',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            42 => 
            array (
                'id' => 43,
                'data' => '{"key": "portal_dokumente", "title": "Dokumente", "subtitle": "", "description": "Titel und Untertitel im Abschnitt Portaldokumente"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-08-19 23:44:50',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            43 => 
            array (
                'id' => 44,
                'data' => '{"key": "portal_verträge", "title": "Suchen Verträge", "subtitle": "", "description": "Titel und Untertitel im Abschnitt Portalverträge"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-08-19 23:47:02',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            44 => 
            array (
                'id' => 45,
                'data' => '{"key": "portal_einstellungen", "title": "Portal hinzufügen", "subtitle": "Systemadministrator", "description": "Titel und Untertitel im Abschnitt Portal-Einstellungen"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-08-19 23:51:28',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            45 => 
            array (
                'id' => 46,
                'data' => '{"key": "portal_mein_account", "title": "Mein Account", "subtitle": "", "description": "Titel und Untertitel in Portal Mein Kontoabschnitt einfügen"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-08-19 23:55:28',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            46 => 
            array (
                'id' => 47,
                'data' => '{"key": "portal_benachrichtigungen", "title": "Ihre Benachrichtigunge", "subtitle": "", "description": "Titel und Untertitel in den Abschnitt Portalbenachrichtigungen einfügen"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-08-20 00:00:25',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            47 => 
            array (
                'id' => 48,
                'data' => '{"key": "portal_benachrichtigungen_detail", "title": "Ihre Benachrichtigunge", "subtitle": "", "description": "Titel und Untertitel in Portalbenachrichtigungen Detailansicht"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-08-20 00:00:58',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            48 => 
            array (
                'id' => 49,
                'data' => '{"key": "portal_anspassung_system", "title": "Anspassung System", "subtitle": "", "description": "Titel und Untertitel im Anspassung System des Portals"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-08-24 00:18:32',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            49 => 
            array (
                'id' => 50,
                'data' => '{"key": "portal_häufig_gestellte_fragen", "title": "Häufig gestellte Fragen", "subtitle": "", "description": "Titel und Untertitel im Abschnitt Häufig gestellte Fragen zum Portal"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-08-20 00:07:39',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            50 => 
            array (
                'id' => 51,
                'data' => '{"key": "portal_faq_kategorien", "title": "FAQ-Kategorien", "subtitle": "", "description": "Titel und Untertitel in den Kategoriebereichen Häufig gestellte Fragen im Portal"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-08-20 00:09:33',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            51 =>
            array (
                'id' => 52,
                'data' => '{"key": "mitarbeiter_überlassungsvertrag", "title": "Überlassungsvertrag", "subtitle": "", "description": "Titel und Untertitel im Mitarbeiterüberlassungsvertrag"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-08-26 01:00:33',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            52 =>
            array (
                'id' => 53,
                'data' => '{"key": "mitarbeiter_services", "title": "Services", "subtitle": "Dies ist eine Übersicht über alle Leistungen, die Sie an jedem Ihrer Fahrräder erbracht haben", "description": "Titel und Untertitel im Services des Mitarbeiter"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-08-26 01:00:33',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            53 =>
            array (
                'id' => 54,
                'data' => '{"key": "mitarbeiter_service_detail", "title": "Service", "subtitle": "", "description": "Titel und Untertitel im Servicedetail des Mitarbeiter"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-08-26 01:00:33',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            54 =>
            array (
                'id' => 55,
                'data' => '{"key": "firma_services", "title": "Services", "subtitle": "", "description": "Titel und Untertitel im Services des Unternehmens"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-08-26 01:00:33',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            55 =>
            array (
                'id' => 56,
                'data' => '{"key": "firma_service_detail", "title": "Service", "subtitle": "", "description": "Titel und Untertitel im Servicedetail des Unternehmens"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-08-26 01:00:33',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            56 =>
            array (
                'id' => 57,
                'data' => '{"key": "lieferanten_dashboard", "title": "Dashboard", "subtitle": "", "description": "Titel und Untertitel des Lieferanten-Dashboards"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-09-16 09:00:33',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            57 =>
            array (
                'id' => 58,
                'data' => '{"key": "lieferanten_neue_angebot", "title": "Neues Angebot erstellen", "subtitle": "Hier haben Sie die Möglickkeit, Ihrem Kunden ein Angebot zu erstellen und es ihm direkt über das Dienstrad-Portal unter Angabe seiner E-Mail zu senden. Bitte füllen Sie alle gekennzeichneten Pflichtfelder (*) aus", "description": "Titel und Untertitel neues Angebot"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-09-16 09:10:33',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            58 =>
            array (
                'id' => 59,
                'data' => '{"key": "lieferanten_angebote", "title": "Angebote", "subtitle": "Hier können Sie alle von Ihnen erstellte Angebote einsehen und nach jeweiligem Status filtern.", "description": "Titel und Untertitel der Angebotsliste"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-09-16 09:00:33',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            59 =>
            array (
                'id' => 60,
                'data' => '{"key": "lieferanten_angebote_detail", "title": "Angebot", "subtitle": "", "description": "Titel und Untertitel der Details eines Angebots"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-09-16 09:18:33',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            60 =>
            array (
                'id' => 61,
                'data' => '{"key": "lieferanten_bestellungen", "title": "Bestellungen", "subtitle": "Hier können Sie alle von Ihnen erstellte Bestellungen einsehen und nach jeweiligem Status filtern.", "description": "Titel und Untertitel der Bestellliste"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-09-16 09:20:33',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            61 => 
            array (
                'id' => 62,
                'data' => '{"key": "lieferanten_benutzer_neue", "title": "Benutzer hinzufügen", "subtitle": "", "description": "Titel und Untertitel des neuen Benutzers"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-08-19 23:42:24',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            62 =>
            array (
                'id' => 63,
                'data' => '{"key": "lieferanten_bestellungen_detail", "title": "Auftrag", "subtitle": "", "description": "Titel und Untertitel des Details einer Bestellung"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-09-16 09:50:33',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            63 =>
            array (
                'id' => 64,
                'data' => '{"key": "lieferanten_inspections", "title": "Wartung / Service", "subtitle": "Dies ist eine Übersicht über alle Leistungen, die Sie an jedem Ihrer Fahrräder erbracht haben...", "description": "Titel und Untertitel der Liste der Inspektionen einer Dienstleistung"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-09-17 09:50:33',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            64 =>
            array (
                'id' => 65,
                'data' => '{"key": "lieferanten_inspection_detail", "title": "Wartung / Service", "subtitle": "", "description": "Titel und Untertitel der Details einer Inspektion"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-09-17 09:50:33',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            65 =>
            array (
                'id' => 66,
                'data' => '{"key": "lieferanten_services", "title": "Wartung / Service", "subtitle": "Dies ist eine Übersicht über alle Leistungen, die Sie an jedem Ihrer Fahrräder erbracht haben...", "description": "Titel und Untertitel der Liste der technischen Dienste"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-09-17 09:50:33',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            66 =>
            array (
                'id' => 67,
                'data' => '{"key": "lieferanten_service_detail", "title": "Wartung / Service", "subtitle": "", "description": "Titel und Untertitel der Details eines technischen Dienstes"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-09-17 09:50:33',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            67 =>
            array (
                'id' => 68,
                'data' => '{"key": "lieferanten_dokumente", "title": "Dokumente", "subtitle": "", "description": "Titel und Untertitel der Dokumentenliste"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-09-17 09:50:33',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            68 =>
            array (
                'id' => 69,
                'data' => '{"key": "lieferanten_benutzer", "title": "Benutzer verwalten", "subtitle": "In der Benutzerverwaltung können Sie definieren, welcher Ihrer Mitarbeiter Zugang zur Bearbeitung von Angeboten im Dienstrad-Portal erhält.", "description": "Titel und Untertitel der Benutzerliste"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-09-17 09:50:33',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            69 =>
            array (
                'id' => 70,
                'data' => '{"key": "lieferanten_portal_konfiguration", "title": "Portal-Konfiguration", "subtitle": "In der Portal-Konfiguration sehen Sie Ihren voreingestellten Daten. Diese können nur vom Systemadministrator verändert werden. Sollten sich diese Daten ändern, sprechen Sie bitte Ihren Portal-Betreiber an. Gerne können Sie jedoch hier Ihr Logo hochladen und eine Farbe auswählen, die für die Darstellung der Buttons u.a. verwendet wird.", "description": "Titel und Untertitel der Portalkonfiguration"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-09-17 09:50:33',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            70 => 
            array (
                'id' => 71,
                'data' => '{"key": "lieferanten_benachrichtigungen", "title": "Ihre Benachrichtigunge", "subtitle": "", "description": "Titel und Untertitel der Liste der Benachrichtigungen"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-09-17 10:18:24',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            71 => 
            array (
                'id' => 72,
                'data' => '{"key": "lieferanten_benachrichtigungen_detail", "title": "Ihre Benachrichtigunge", "subtitle": "", "description": "Titel und Untertitel des Benachrichtigungsdetails"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-09-17 10:18:24',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            72 => 
            array (
                'id' => 73,
                'data' => '{"key": "lieferanten_mein_account", "title": "Mein Account", "subtitle": "", "description": "Titel und Untertitelung des Abschnitts Mein Konto beim Anbieter"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-09-17 11:09:17',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            73 => 
            array (
                'id' => 74,
                'data' => '{"key": "portal_services", "title": "Services", "subtitle": "", "description": "Titel und Untertitel im Dashboard-Bereich des Portal"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-08-18 07:43:43',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            74 => 
            array (
                'id' => 75,
                'data' => '{"key": "portal_service_detail", "title": "Service", "subtitle": "", "description": "Titel und Untertitel im Servicedetail des Portal"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-08-18 07:43:43',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            75 => 
            array (
                'id' => 76,
                'data' => '{"key": "firma_inspection_detail", "title": "Inspektion", "subtitle": "", "description": "Titel und Untertitel im Inspektiondetail des Unternehmens"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-08-18 07:43:43',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            76 => 
            array (
                'id' => 77,
                'data' => '{"key": "mitarbeiter_inspection_detail", "title": "Inspektion", "subtitle": "", "description": "Titel und Untertitel im Inspektiondetail des Mitarbeiter"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-08-18 07:43:43',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            77 => 
            array (
                'id' => 78,
                'data' => '{"key": "portal_inspection_detail", "title": "Inspektion", "subtitle": "", "description": "Titel und Untertitel im Inspektiondetail des Portal"}',
                'portal_id' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2019-08-18 07:43:43',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
        ));
    }
}
