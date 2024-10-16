<?php

declare(strict_types=1);

return [
    'titles' => [
        'list' => 'Nomenclator',
    ],

    'labels' => [
        'navigation' => 'Nomenclator',
        'name' => 'Categorie serviciu',
        'institutions' => 'Instituții',
        'centers' => 'Centre',
        'status' => 'Status',
        'counseling_sheet' => 'Fișă consiliere serviciu (opțional)',
        'nr' => 'Nr.',
        'intervention_name' => 'Nume intervenție',
        'service_name' => 'Nume serviciu',
        'empty_state_service_table' => 'Niciun serviciu identificat. Adaugă un prim serviciu pentru ca acesta să fie disponibil organizațiilor Sunrise'
    ],

    'headings' => [
        'service' => 'Servicii',
        'service_table' => 'Toate serviciile',
        'service_intervention' => 'Intervenții asociate serviciului',
        //        'service' => '',

        'inactivate_modal' => 'Inactivează serviciul pentru toate nomenclatoarele',
    ],

    'helper_texts' => [
        'inactivate_modal' => 'Prin inactivarea serviciului acesta nu va mai fi disponibil pentru fi adăugat în nomenclatorul organizațiilor Sunrise. Pentru organizațiile care au folosit deja acest serviciu, acesta va fi retras din nomenclatoarele acestora, fără să le șteargă din istoricul cazurilor în care a fost folosit. ',
    ],

    'actions' => [
        'change_status' => [
            'activate' => 'Activează',
            'inactivate' => 'Dezactivează',
            'inactivate_modal' => 'Inactivează serviciu',
        ],
        'add_service' => 'Adaugă serviciu',
        'add_intervention' => 'Adaugă încă o intervenție',
        'edit_service' => 'Modifică serviciu',
    ],

];
