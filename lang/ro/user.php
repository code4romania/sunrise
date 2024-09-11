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
        'success' => 'Invitația a fost trimisata cu succes.',
        'failure_title' => 'Eroare la retrimiterea invitației!',
        'failure_body' => 'A aparut o eroare la retrimiterea invitației',
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
        'failure_title' => 'Eroare la trimiterea email-ului!',
        'failure_body' => 'A aparut o eroare la trimiterea email-ului de resetare a parolei',
    ],

    'status' => [
        'active' => 'Activ',
        'inactive' => 'Inactiv',
    ],
];
