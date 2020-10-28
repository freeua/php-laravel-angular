<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class EmailDatabaseHelper
{
    public static function insert($key, $subject, array $vars)
    {
        $body = file_get_contents(resource_path("emails/$key.blade.php"));
        DB::table('emails')->updateOrInsert(['key' => $key], [
            'key' => $key,
            'subject' => $subject,
            'body' => $body,
            'vars' => json_encode($vars),
        ]);
    }
}
