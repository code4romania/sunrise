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
        'empty_state_role_table' => 'Niciun rol de specialitate identificat. Adaugă un prim rol pentru ca acesta să fie disponibil organizațiilor Sunrise',
        'role_name' => 'Nume rol specialist',
        'case_permissions' => 'Permisiuni suplimentare cazuri',
        'ngo_admin_permissions' => 'Permisiuni suplimentare administrare',
        'users' => 'Utilizatori',
    ],

    'headings' => [
        'service' => 'Servicii',
        'service_table' => 'Toate serviciile',
        'service_intervention' => 'Intervenții asociate serviciului',
        'roles' => 'Specialiști',
        'roles_table' => 'Toate rolurile de specialiști',

        'inactivate_service_modal' => 'Inactivează serviciul pentru toate nomenclatoarele',
        'inactivate_role_modal' => 'Inactivează rol pentru toate nomenclatoarele',
    ],

    'helper_texts' => [
        'inactivate_service_modal' => 'Prin inactivarea serviciului acesta nu va mai fi disponibil pentru fi adăugat în nomenclatorul organizațiilor Sunrise. Pentru organizațiile care au folosit deja acest serviciu, acesta va fi retras din nomenclatoarele acestora, fără să le șteargă din istoricul cazurilor în care a fost folosit. ',
        'role_page_description' => 'Definește un rol de specialist care va deveni disponibil pentru a fi inclus în nomenclatoarele organizațiilor Sunrise',
        'role_page_default_permissions' => 'Permisiuni default asociate rolului (nu se pot modifica de către organizație)',
        'inactivate_role_modal' => 'Prin inactivarea rolului, acesta nu va mai fi disponibil pentru fi adăugat în nomenclatoarele de specialiști ale organizațiilor Sunrise. Pentru organizațiile care au folosit deja acest rol, acesta va fi retras din nomenclatoare, fără să se șteargă din istoricul utilizatorilor pentru care a fost folosit. ',
    ],

    'actions' => [
        'change_status' => [
            'activate' => 'Activează',
            'inactivate' => 'Dezactivează',
            'inactivate_service_modal' => 'Inactivează serviciu',
            'inactivate_role_modal' => 'Inactivează rol',
        ],
        'add_service' => 'Adaugă serviciu',
        'add_intervention' => 'Adaugă încă o intervenție',
        'edit_service' => 'Modifică serviciu',
        'add_role' => 'Adaugă rol specialist',
        'edit_role' => 'Modifică rol specialist',
        'delete_role' => 'Șterge rol',
    ],

];
