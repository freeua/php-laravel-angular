<?php

use App\Helpers\EmailDatabaseHelper;
use App\Modules\TechnicalServices\Notifications\AcceptedTechnicalServiceForCompany;
use App\Modules\TechnicalServices\Notifications\AcceptedTechnicalServiceForEmployee;
use App\Modules\TechnicalServices\Notifications\CompletedTechnicalServiceForCompany;
use App\Modules\TechnicalServices\Notifications\CompletedTechnicalServiceForEmployee;
use App\Modules\TechnicalServices\Notifications\CompletedTechnicalServiceForSupplier;
use App\Modules\TechnicalServices\Notifications\CreatedInspectionForEmployee;
use App\Modules\TechnicalServices\Notifications\CreatedTechnicalServiceForEmployee;
use App\Modules\TechnicalServices\Notifications\ReadyTechnicalServiceForEmployee;
use Illuminate\Database\Migrations\Migration;

class AddEmailsTechnicalServices extends Migration
{

    public function up()
    {
        EmailDatabaseHelper::insert(
            CreatedTechnicalServiceForEmployee::EMAIL_KEY,
            'Anmeldung technischer Service',
            ['$employeeName', '$inspectionCode', '$frameNumber']);
        EmailDatabaseHelper::insert(
            CreatedInspectionForEmployee::EMAIL_KEY,
            'Sie haben eine neue Inspektion zur Verfügung',
            ['$employeeName', '$inspectionCode', '$frameNumber']);
        EmailDatabaseHelper::insert(
            AcceptedTechnicalServiceForEmployee::EMAIL_KEY,
            'Annahme Fahrrad technischer Service',
            ['$employeeName', '$supplierName', '$frameNumber']);
        EmailDatabaseHelper::insert(
            AcceptedTechnicalServiceForCompany::EMAIL_KEY,
            ' Annahme Fahrrad technischer Service',
            ['$company', '$supplierName', '$frameNumber']);
        EmailDatabaseHelper::insert(
            CompletedTechnicalServiceForEmployee::EMAIL_KEY,
            'Abschluss technischer Service',
            ['$supplierName', '$serviceCode', '$frameNumber']);
        EmailDatabaseHelper::insert(
            CompletedTechnicalServiceForSupplier::EMAIL_KEY,
            'Abschluss technischer Service',
            ['$employeeName', '$serviceCode', '$frameNumber']);
        EmailDatabaseHelper::insert(
            CompletedTechnicalServiceForCompany::EMAIL_KEY,
            'Abschluss technischer Service',
            ['$company', '$supplierName', '$employeeName', '$serviceCode', '$frameNumber']);
        EmailDatabaseHelper::insert(
            ReadyTechnicalServiceForEmployee::EMAIL_KEY,
            'Technischer Service: Ihr Fahrrad ist abholbereit!',
            ['$employeeName', '$frameNumber', '$pickupCode']);
    }

    public function down()
    {
        //
    }
}
