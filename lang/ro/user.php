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
        'case_permissions' => 'Permisiuni cazuri',
        'admin_permissions' => 'Permisiuni administrare',
        'last_login_at_date_time' => 'Data și ora ultimei accesări',
        'can_be_case_manager' => 'Poate lua rol de manager de caz',
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
        'active_users' => 'Utilizatori activi',
        'specialist_details' => 'Detalii specialist',
    ],

    'placeholders' => [
        'user_role_without_permissions_for_all_cases' => 'Acest tip de utilizator <span class="italic">are acces doar la cazurile din echipa cărora face parte</span> și nu deține drepturi de administrare ale sistemului. Puteți oferi permisiuni suplimentare din lista de mai jos.',
        'user_role_with_permissions_for_all_cases' => 'Acest tip de rol are acces <span class="italic">la toate cazurile din cadrul Centrului</span>, însă nu deține drepturi de administrare ale sistemului. Puteți oferi permisiuni suplimentare din lista de mai jos.',
        'dashboard_cart' => 'Distribuția pe luni a numărului total de utilizatori activi Sunrise. Un utilizator este considerat activ dacă a avut cel puțin o accesare a platformei în luna calendaristică respectivă.',
    ],

    'actions' => [
        'deactivate' => 'Deactivează cont',
        'reset_password' => 'Resetează parola',
        'resend_invitation' => 'Retrimite invitația',
        'activate' => 'Reactivează cont',
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
        'description' => 'Odată dezactivat contul, utilizatorul nu va mai avea acces în platformă. Toate datele asociate contului vor rămâne în baza de date. Pentru a oferi din nou acces utilizatorului, va trebui să reactivați contul din profilul acestuia.',
    ],

    'action_reactivate_confirm' => [
        'title' => 'Deactivează cont',
        'success' => 'Cont reactivat cu succes',
    ],

    'action_reset_password_confirm' => [
        'title' => 'Resetează parola',
        'success' => 'Email-ul a fost trimis cu succes',
    ],

    'status' => [
        'active' => 'Activ',
        'inactive' => 'Inactiv',
    ],
];
