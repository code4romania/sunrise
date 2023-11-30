<?php

declare(strict_types=1);

return [

    'label' => [
        'singular' => 'caz',
        'plural' => 'Cazuri',
    ],

    'stats' => [
        'open' => 'Cazuri deschise',
        'monitoring' => 'Cazuri în monitorizare',
        'closed' => 'Cazuri închise',
    ],

    'wizard' => [
        'consent' => [
            'label' => 'Consimțământ',
            'fields' => [
                'consent' => 'Confirm că s-a obținut acordul beneficiarului pentru înregistrarea datelor personale și utilizarea datelor rezultate din evaluare și intervenție în scopul oferirii serviciilor de management de caz.',
            ],
        ],
        'beneficiary' => [
            'label' => 'Identitate beneficiar',
        ],
        'children' => [
            'label' => 'Identitate copii',
        ],
        'personal_information' => [
            'label' => 'Informații personale',
        ],
    ],
];
