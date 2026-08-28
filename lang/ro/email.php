<?php

declare(strict_types=1);

return [

    'greeting' => 'Salut :name,',
    'admin' => [
        'welcome' => [
            'greeting' => 'Salut, :name',
            'intro_line_1' => ':institution_name te invită ca administrator al centrului :center_name.',
            'intro_line_2' => 'Pentru a-ți accesa contul de administrator al centrului, te rugăm să urmezi pașii de mai jos:',
            'intro_line_3' => '1. Acceptă invitația și configurează o parolă pentru contul tău, prin apăsarea butonului de mai jos.',
            'intro_line_4' => '2. După ce ai configurat parola, te poți autentifica în platformă folosind adresa ta de email și noua parolă, pe linkul de mai jos.',
            'intro_line_5' => '<a style="text-align: center" href=":login_url">:login_domain</a>',
            'fallback_url' => 'Dacă nu poți apăsa pe butonul "Acceptă invitația", copiază adresa de mai jos în browser-ul tău: :url',
            'subject' => 'Bine ai venit în platforma Sunrise',
            'accept_invitation' => 'Acceptă invitația',
        ],
    ],

    'salutation' => 'Echipa Sunrise',

    'organization' => [
        'welcome' => [
            'greeting' => 'Salut, :name',
            'intro_line_1' => ':institution_name te invită ca specialist al centrului :center_name.',
            'intro_line_2' => 'Pentru a-ți accesa contul de specialist, te rugăm să urmezi pașii de mai jos:',
            'intro_line_3' => '1. Acceptă invitați și configurează o parolă pentru contul tău, apasă pe butonul de mai jos.',
            'intro_line_4' => '2. După ce ai configurat parola, te poți autentifica în platformă folosind adresa ta de email și noua parolă, pe linkul de mai jos',
            'intro_line_5' => '<a style="text-align: center" href="https://sunrise.stopviolenteidomestice.ro">www.sunrise.stopviolențeidomestice.ro</a>',
            'subject' => 'Bine ai venit în platforma Sunrise',
            'accept_invitation' => 'Acceptă invitația',
        ],

        'welcome_in_anther_tenant' => [
            'greeting' => 'Salut, :name',
            'intro_line_1' => ':institution_name te invită ca specialist al centrului :center_name.',
            'intro_line_2' => 'Pentru a-ți accesa contul de specialist, te rugăm să urmezi pașii de mai jos:',
            'intro_line_3' => '1. Acceptă invitația prin apasăsarea pe butonul de mai jos. Va trebui să te autentifici în platformă folosind adresa ta de email și parola.',
            'intro_line_4' => '2. După ce ai acceptat invitația, de fiecare dată când te autentifici în platformă, pe linkul de mai jos, vei avea acces la toate Centrele în cadrul cărora ești adăugat cu rol de specialist.',
            'intro_line_5' => '<a style="text-align: center" href="https://sunrise.stopviolenteidomestice.ro">www.sunrise.stopviolențeidomestice.ro</a>',
            'subject' => 'Bine ai venit în platforma Sunrise',
            'accept_invitation' => 'Acceptă invitația',
        ],
    ],
    'reset_password' => [
        'intro_line_1' => 'Am primit o cerere de resetare a parolei tale în platforma Sunrise.',
        'intro_line_2' => 'Pentru a configura o parolă nouă te rugăm să apeși butonul de mai jos și să urmezi instrucțiunile.',
        'intro_line_3' => 'Opțiunea de a reseta parola va expira după 60 de minute.',
        'intro_line_4' => 'Dacă nu ai solicitat resetarea parolei, te rugăm să ignori acest mail.',
        'intro_line_5' => 'Poți accesa platforma Sunrise la link-ul de mai jos:',
        'intro_line_6' => '<a style="text-align: center" href="https://sunrise.stopviolenteidomestice.ro">www.sunrise.stopviolențeidomestice.ro</a>',
        'subject' => 'Cerere de resetare a parolei pentru contul tău din platforma Sunrise',
        'action' => 'Resetează parola',
        'reset_password' => 'Resetează parola',
    ],
];
