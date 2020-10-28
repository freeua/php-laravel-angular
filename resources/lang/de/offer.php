<?php

return [
    'create'   => [
        'failed' => 'Angebot wurde nicht erstellt'
    ],
    'accept'   => [
        'failed'         => 'Angebot wurde nicht angenommen',
        'invalid'        => 'Angebot kann nicht angenommen werden',
        'contract_limit' => 'Vertragslimit erreicht',
        'leasing_budget' => 'Leasing-Budget erreicht, wenden Sie sich bitte an Ihren Portaladministrator'
    ],
    'reject'   => [
        'failed'  => 'Angebot wurde nicht abgelehnt',
        'invalid' => 'Das Angebot kann nicht abgelehnt werden'
    ],
    'contract' => [
        'invalid'  => 'Vertrag kann dem Angebot nicht hinzugefügt werden',
        'generate' => [
            'failed'  => 'Vertrag wurde nicht generiert',
            'invalid' => 'Kann keinen Vertrag für das Angebot generieren'
        ]
    ],
];
