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
        'empty_state_service_table' => 'Niciun serviciu identificat. Adaugă un prim serviciu pentru ca acesta să fie disponibil organizațiilor Sunrise',
        'benefit_name' => 'Categorie beneficiu social',
        'benefit_type_name' => 'Nume beneficiu',
    ],

    'headings' => [
        'service' => 'Servicii',
        'service_table' => 'Toate serviciile',
        'service_intervention' => 'Intervenții asociate serviciului',
        //        'service' => '',

        'inactivate_modal' => 'Inactivează serviciul pentru toate nomenclatoarele',
        'empty_state_benefit_table' => 'Niciun beneficiu identificat. Adaugă un prim beneficiu pentru ca acesta să fie disponibil organizațiilor Sunrise',
        'benefit_table' => 'Toate beneficiile sociale',
        'benefit_types' => 'Tipuri de beneficiu social',
        'inactivate_benefit' => 'Inactivează rol pentru toate nomenclatoarele',
    ],

    'helper_texts' => [
        'inactivate_modal' => 'Prin inactivarea serviciului acesta nu va mai fi disponibil pentru fi adăugat în nomenclatorul organizațiilor Sunrise. Pentru organizațiile care au folosit deja acest serviciu, acesta va fi retras din nomenclatoarele acestora, fără să le șteargă din istoricul cazurilor în care a fost folosit. ',
        'inactivate_benefit' => 'Prin inactivarea beneficiului, acesta nu va mai fi disponibil pentru fi adăugat în nomenclatoarele de beneficii ale organizațiilor Sunrise. Pentru organizațiile care au folosit deja acest beneficiu, acesta va fi retras din nomenclatoare, fără să se șteargă din istoricul planului de interventie pentru care a fost folosit.',
        'delete_benefit' => 'Beneficiile sociale utilizate deja în fișe de beneficiar nu mai pot fi șterse. Aveți doar opțiunea de a le deactiva.',
    ],

    'actions' => [
        'change_status' => [
            'activate' => 'Activează',
            'inactivate' => 'Dezactivează',
            'inactivate_modal' => 'Inactivează serviciu',
            'inactivate_benefit' => 'Inactivează beneficiu',
        ],
        'add_service' => 'Adaugă serviciu',
        'add_intervention' => 'Adaugă încă o intervenție',
        'edit_service' => 'Modifică serviciu',
        'add_benefit' => 'Adaugă beneficiu social',
        'add_benefit_type' => 'Adaugă încă un tip',
        'edit_benefit' => 'Modifică beneficiu',
        'delete_benefit' => 'Șterge beneficiu',
    ],

];
