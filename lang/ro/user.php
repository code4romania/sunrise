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

    'titles' => [
        'create_specialist' => 'Adaugă specialist',
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
        'case_permissions' => 'Permisiuni suplimentare cazuri',
        'admin_permissions' => 'Permisiuni suplimentare administrare',
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
        'specialist_section' => 'Detalii specialist',
    ],

    'placeholders' => [
        'obs' => 'Acest tip de utilizator __are acces doar la cazurile din echipa cărora face parte__ și nu deține drepturi de administrare ale sistemului. Puteți oferi permisiuni suplimentare din lista de mai jos.',
    ],

    'actions' => [
        'deactivate' => 'Deactivează cont',
        'reactivate' => 'Reactivează cont',
        'reset_password' => 'Resetează parola',
        'resend_invitation' => 'Retrimite invitația',
    ],

    'action_resend_invitation_confirm' => [
        'title' => 'Retrimite invitația',
        'success' => 'Invitația a fost trimisă cu succes.',
        'failure' => 'Te rugăm să aștepți înainte de a încerca din nou.',
    ],

    'action_deactivate_confirm' => [
        'title' => 'Deactivează cont',
        'success' => 'Cont dezactivat cu succes',
    ],

    'action_reactivate_confirm' => [
        'title' => 'Reactivează cont',
        'success' => 'Contul a fost reactivat cu succes',
    ],

    'action_reset_password_confirm' => [
        'title' => 'Resetează parola',
        'success' => 'Email-ul a fost trimis cu succes',
    ],

    'status' => [
        'active' => 'Activ',
        'inactive' => 'Inactiv',
    ],

    'inactive_error' => 'Contul tău nu este activ. Pentru mai multe detalii te rugăm să contactezi un administrator.',
];
