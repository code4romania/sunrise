<?php

declare(strict_types=1);

return [

    'label' => [
        'singular' => 'utilizator',
        'plural' => 'Utilizatori',
    ],

    'specialist_label' => [
        'singular' => 'specialist',
        'plural' => 'Specialiști',
    ],

    'labels' => [
        'first_name' => 'Nume',
        'last_name' => 'Prenume',
        'roles' => 'Roluri',
        'account_status' => 'Cont',
        'last_login_at' => 'Ultima accesare',
        'email' => 'Email',
        'phone_number' => 'Număr telefon',
        'select_roles' => 'Rol specialitate',
        'can_be_case_manager' => 'Poate lua rol de manager de caz',
        'case_permissions' => 'Permisiuni cazuri',
        'admin_permissions' => 'Permisiuni administrare',
        'has_access_to_all_cases' => 'Are acces la toate cazurile din Centru',
        'can_search_cases_in_all_centers' => 'Poate căuta cazuri (după CNP) în baza de date a tuturor centrelor instituției',
        'can_copy_cases_in_all_centers' => 'Poate copia date identificare beneficiar dintr-o bază de date în alta  a instituției',
        'has_access_to_statistics' => 'Are acces la rapoarte statistice',
        'can_change_nomenclature' => 'Are drepturi de modificare nomenclator',
        'can_change_staff' => 'Are drepturi de modificare Echipă Specialiști (Staff)',
        'can_change_organisation_profile' => 'Are drepturi de modificare Profilul Organizației în Rețeaua Sunrise',
    ],

    'stats' => [
        'open' => 'Cazuri deschise',
        'monitoring' => 'Cazuri în monitorizare',
        'closed' => 'Cazuri închise',
    ],

    'role' => [
        'admin' => 'Administrator',
        'specialist' => 'Specialist',
        'manager' => 'Manager',

    ],

    'heading' => [
        'table' => 'Echipa interdisciplinară',
    ],

    'placeholders' => [
        'obs' => 'Acest tip de utilizator are acces doar la cazurile din echipa cărora face parte și nu deține drepturi
        de administrare ale sistemului. Puteți oferi permisiuni suplimentare din lista de mai jos.',
    ],

    'actions' => [
        'deactivate' => 'Deactivează cont',
        'reset_password' => 'Resetează parola',
        'resend_invitation' => 'Retrimite invitația',
    ],
];
