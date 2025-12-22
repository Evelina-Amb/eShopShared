<?php

return [

    // LT only Prices in CENTS.
    'carriers' => [
        'omniva' => [
            'label' => 'Omniva',
            'prices_cents' => [
                'S' => 299,
                'M' => 399,
                'L' => 499,
            ],
        ],
        'venipak' => [
            'label' => 'Venipak',
            'prices_cents' => [
                'S' => 279,
                'M' => 379,
                'L' => 479,
            ],
        ],
        'lpexpress' => [
            'label' => 'LP Express',
            'prices_cents' => [
                'S' => 249,
                'M' => 349,
                'L' => 449,
            ],
        ],
    ],

    'size_rank' => [
        'S' => 1,
        'M' => 2,
        'L' => 3,
    ],
];
