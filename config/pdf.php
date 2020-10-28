<?php

return [
    'mode'              => 'UTF-8',
    'format'            => 'A4',
    'default_font_size' => '13',
    'default_font'      => 'sans-serif',
    'margin_left'       => 20,
    'margin_right'      => 12,
    'margin_top'        => 10,
    'margin_bottom'     => 18,
    'margin_header'     => 0,
    'margin_footer'     => 5,
    'orientation'       => 'P',
    'font_path'         => base_path('resources/fonts/'),
    'tempDir'           => storage_path('tmp'),
    'font_data'         => [
        'roboto' => [
            'R'  => 'Roboto-Regular.ttf',
            'B'  => 'Roboto-Bold.ttf'
        ],
        'pt-sans' => [
            'R'  => 'PTSans-Regular.ttf',
            'B'  => 'PTSans-Bold.ttf'
        ],
        'open-sans' => [
            'R'  => 'OpenSans-Regular.ttf',
            'B'  => 'OpenSans-Bold.ttf'
        ]
    ]
];
