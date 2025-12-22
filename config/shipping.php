<?php

return [

    // Lithuania only  ALL VALUES IN CENTS
    'carriers' => [
        'omniva' => [
            'label' => 'Omniva',
            'prices_cents' => [
                'XS' => 250,
                'S'  => 300,
                'M'  => 350,
                'L'  => 450,
            ],
        ],

        'venipak' => [
            'label' => 'Venipak',
            'prices_cents' => [
                'XS' => 200,
                'S'  => 280,
                'M'  => 330,
                'L'  => 400,
            ],
        ],
    ],

    'size_rank' => [
        'XS' => 1,
        'S'  => 2,
        'M'  => 3,
        'L'  => 4,
    ],
];
