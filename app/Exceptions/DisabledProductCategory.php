<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 13.06.2019
 * Time: 16:48
 */

namespace App\Exceptions;

class DisabledProductCategory extends ControlledException
{
    public function __construct()
    {
        $message = 'Die ausgewählte Produktkategorie ist für diesen Mitarbeiter nicht verfügbar.';
        parent::__construct($message, 422, 'disableProductCategory');
    }
}
