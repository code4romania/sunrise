<?php

declare(strict_types=1);

return [
    'headings' => [
        'institution_name' => 'Nume instituție',
        'registered_office' => 'Sediu social',
        'centers' => 'Centre',
        'cases' => 'Cazuri',
        'specialists' => 'Specialiști',
        'status' => 'Status',
        'list_title' => 'Utilizatori instituționali',
        'empty_state' => 'Niciun utilizator instituțional identificat.',
        'institution_details' => 'Detalii organizație',
        'center_details' => 'Centre',
        'ngo_admin' => 'Administrator',
        'inactivate' => 'Dezactivează organizație',
        'admin_users' => 'Utilizatori de tip administrator',
    ],

    'labels' => [
        'organization_status' => 'Statut organizație sau hotărâre de înființare',
        'social_service_provider_certificate' => 'Certificat furnizor de servicii sociale',
        'center_name' => 'Nume centru',
        'social_service_licensing_certificate' => 'Certificat de licențiere serviciu social',
        'logo_center' => 'Logo centru',
        'organization_header' => 'Antet centru',
        'first_name' => 'Nume',
        'last_name' => 'Prenume',
        'email' => 'Email',
        'phone' => 'Telefon',
        'inactivate' => 'Odată dezactivată o organizație, utilizatorii acesteia nu vor mai ave acces în platformă. Toate datele asociate organizației vor rămâne în baza de date. Pentru a oferi din nou acces utilizatorilor, organizația va trebui Reactivată din profilul acesteia.',
        'roles' => 'Roluri',
        'account_status' => 'Cont',
        'last_login_at' => 'Ultima accesare',
    ],

    'actions' => [
        'create' => 'Adaugă o instituție',
        'add_organization' => 'Adaugă încă un centru',
        'add_admin' => 'Adaugă încă un administrator',
        'activate' => 'Reactivează organizație',
        'inactivate' => 'Deactivează organizație',
        'add_ngo_admin' => 'Adaugă administrator',
    ],

    'placeholders' => [
        'center_details' => 'Dacă instituția are multiple centre acreditate pentru servicii diferite și necesită menținearea unor baze de date diferite de beneficiari, se pot crea tenants (profile) diferite pentru fiecare dintre acestea.',
        'ngo_admins' => 'Adăugați cel puțin un rol de administrator în sistem. Această persoană are drepturi depline asupra întregii aplicații Sunrise pentru toate centrele instituției (ale organizației). Un email de invitație va fi transmis administratorului odată cu finalizarea adăugării instituției.',
    ],

    'helper_texts' => [
        'organization_status' => 'Încarcă statutul în format .pdf, .jpg sau .png',
        'social_service_provider_certificate' => 'Încarcă certificatul de furnizor de servicii sociale în format .pdf, .jpg sau .png',
        'social_service_licensing_certificate' => 'Încarcă certificatul de licențiere pentru serviciul social în format .pdf, .jpg sau .png',
        'logo' => 'Încarcă un logo pentru centru, care să fie folosit în interfață',
        'organization_header' => 'Încarcă un antet pentru centru, care să fie folosit pentru fișiele exportate',
    ],
];
