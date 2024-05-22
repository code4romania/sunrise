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

    'action_resend_invitation_confirm' => [
        'title' => 'Retrimite invitația',
        'success' => 'Invitația a fost trimisata cu succes.',
        'failure_title' => 'Eroare la retrimiterea invitației!',
        'failure_body' => 'A aparut o eroare la retrimiterea invitației',
    ],
    'action_deactivate_confirm' => [
        'title' => 'Deactivează cont',
        'success' => 'Cont dezactivat cu succes',
    ],
];
