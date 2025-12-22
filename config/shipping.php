<?php

return [

    // LT only Prices in CENTS.
    'carriers' => [
        'omniva' => [
            'label' => 'Omniva',
            'prices_cents' => [
         'XS' => 2.50,
        'S'  => 3.00,
        'M'  => 3.50,
        'L'  => 4.50,
            ],
        ],
        'venipak' => [
            'label' => 'Venipak',
            'prices_cents' => [
        'XS' => 2.00,
        'S'  => 2.80,
        'M'  => 3.30,
        'L'  => 4.00,
            ],
        ],
        'lpexpress' => [
            'label' => 'LP Express',
            'prices_cents' => [
                'XS' => 2.00,
                'S' => 249,
                'M' => 349,
                'L' => 449,
            ],
        ],
    ],

    'size_rank' => [
        'XS' => 1,
        'S' => 2,
        'M' => 3,
        'L' => 4,
    ],
];
