<?php

declare(strict_types=1);

return [

    'label' => [
        'singular' => 'caz',
        'plural' => 'Cazuri',
    ],

    'page' => [
        'view' => [
            'title' => '#:id :name',
        ],
        'edit_identity' => [
            'title' => 'Editeaza cazul #:id :name',
        ],
        'edit_personal_information' => [
            'title' => 'Editeaza cazul #:id :name',
        ],
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
        'create' => 'Înregistrează caz nou',
        'add_child' => 'Adaugă copil',
    ],
];
