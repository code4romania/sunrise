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
        'active' => 'Activ',
        'inactive' => 'Inactiv',
        'service_name' => 'Nume serviciu',
        'empty_state_service_table' => 'Niciun serviciu identificat. Adaugă un prim serviciu pentru ca acesta să fie disponibil organizațiilor Sunrise',
        'empty_state_role_table' => 'Niciun rol de specialitate identificat. Adaugă un prim rol pentru ca acesta să fie disponibil organizațiilor Sunrise',
        'role_name' => 'Nume rol specialist',
        'case_permissions' => 'Permisiuni suplimentare cazuri',
        'ngo_admin_permissions' => 'Permisiuni suplimentare administrare',
        'users' => 'Utilizatori',
        'benefit_name' => 'Categorie beneficiu social',
        'benefit_type_name' => 'Nume beneficiu',
        'benefit' => 'Beneficiu',
    ],

    'headings' => [
        'service' => 'Servicii',
        'service_table' => 'Toate serviciile',
        'service_intervention' => 'Intervenții asociate serviciului',
        'roles' => 'Specialiști',
        'roles_table' => 'Toate rolurile de specialiști',

        'inactivate_service_modal' => 'Inactivează serviciul pentru toate nomenclatoarele',
        'inactivate_role_modal' => 'Inactivează rol pentru toate nomenclatoarele',

        'empty_state_benefit_table' => 'Niciun beneficiu identificat. Adaugă un prim beneficiu pentru ca acesta să fie disponibil organizațiilor Sunrise',
        'benefits' => 'Beneficii',
        'benefit_table' => 'Toate beneficiile sociale',
        'benefit_types' => 'Tipuri de beneficiu social',
        'inactivate_benefit_modal' => 'Inactivează beneficiu pentru toate nomenclatoarele',
    ],

    'helper_texts' => [
        'inactivate_service_modal' => 'Prin inactivarea serviciului acesta nu va mai fi disponibil pentru fi adăugat în nomenclatorul organizațiilor Sunrise. Pentru organizațiile care au folosit deja acest serviciu, acesta va fi retras din nomenclatoarele acestora, fără să le șteargă din istoricul cazurilor în care a fost folosit. ',
        'role_page_description' => 'Definește un rol de specialist care va deveni disponibil pentru a fi inclus în nomenclatoarele organizațiilor Sunrise',
        'role_page_default_permissions' => 'Permisiuni default asociate rolului (nu se pot modifica de către organizație)',
        'inactivate_role_modal' => 'Prin inactivarea rolului, acesta nu va mai fi disponibil pentru fi adăugat în nomenclatoarele de specialiști ale organizațiilor Sunrise. Pentru organizațiile care au folosit deja acest rol, acesta va fi retras din nomenclatoare, fără să se șteargă din istoricul utilizatorilor pentru care a fost folosit. ',
        'inactivate_benefit_modal' => 'Prin inactivarea beneficiului, acesta nu va mai fi disponibil pentru fi adăugat în nomenclatoarele de beneficii ale organizațiilor Sunrise. Pentru organizațiile care au folosit deja acest beneficiu, acesta va fi retras din nomenclatoare, fără să se șteargă din istoricul planului de interventie pentru care a fost folosit.',
        'delete_benefit' => 'Beneficiile sociale utilizate deja în fișe de beneficiar nu mai pot fi șterse. Aveți doar opțiunea de a le deactiva.',
    ],

    'actions' => [
        'change_status' => [
            'activate' => 'Activează',
            'inactivate' => 'Inactivează',
            'inactivate_service_modal' => 'Inactivează serviciu',
            'inactivate_role_modal' => 'Inactivează rol',
            'inactivate_benefit_modal' => 'Inactivează beneificu',
        ],
        'add_service' => 'Adaugă serviciu',
        'add_intervention' => 'Adaugă încă o intervenție',
        'edit_service' => 'Modifică serviciu',
        'add_role' => 'Adaugă rol specialist',
        'edit_role' => 'Modifică rol specialist',
        'delete_role' => 'Șterge rol',
        'edit' => 'Modifică',
        'add_benefit' => 'Adaugă beneficiu social',
        'add_benefit_type' => 'Adaugă încă un tip',
        'edit_benefit' => 'Modifică beneficiu',
        'delete_benefit' => 'Șterge beneficiu',
    ],

    'placeholders' => [
        'role_name' => 'Adaugă o denumire pentru rolul de specialist',
    ],
];
