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

    'section' => [
        'identity' => [
            'title' => 'Date identitate',
            'tab' => [
                'beneficiary' => 'Identitate beneficiar',
                'children' => 'Identitate copii',
            ],
        ],

        'personal_information' => [
            'title' => 'Informații personale',
            'section' => [
                'beneficiary' => 'Beneficiar',
                'aggressor' => 'Informații despre agresor',
                'antecedents' => 'Antecedente caz',
                'flow' => 'Flux prezentare victimă',
            ],
        ],
    ],

    'status' => [
        'active' => 'Activ',
        'reactivated' => 'Reactivat',
        'monitored' => 'În monitorizare',
        'closed' => 'Închis',
    ],

    'action' => [
        'add_child' => 'Adaugă copil',
    ],
];
