<?php

declare(strict_types=1);

return [

    'label' => [
        'singular' => 'caz',
        'plural' => 'Cazuri',
    ],

    'page' => [
        'view' => [
            'title' => '#:id :name',
        ],
        'edit_identity' => [
            'title' => 'Editeaza cazul #:id :name',
        ],
        'edit_personal_information' => [
            'title' => 'Editeaza cazul #:id :name',
        ],
        'create_detailed_evaluation' => [
            'title' => 'Evaluare detaliata',
        ],
    ],

    'stats' => [
        'open' => 'Cazuri deschise',
        'monitoring' => 'Cazuri în monitorizare',
        'closed' => 'Cazuri închise',
    ],

    'wizard' => [
        'consent' => [
            'label' => 'Consimțământ',
        ],
        'beneficiary' => [
            'label' => 'Identitate beneficiar',
        ],
        'children' => [
            'label' => 'Identitate copii',
        ],
        'personal_information' => [
            'label' => 'Informații personale',
        ],
        'detailed_evaluation' => [
            'label' => 'Detalii evaluare',
        ],
        'partner' => [
            'label' => 'Partener',
        ],
        'multidisciplinary_evaluation' => [
            'label' => 'Evaluare multidisciplinara',
        ],
        'results' => [
            'label' => 'Rezultate',
        ],
    ],

    'section' => [
        'identity' => [
            'title' => 'Date identitate',
            'tab' => [
                'beneficiary' => 'Identitate beneficiar',
                'children' => 'Identitate copii',
            ],
        ],

        'personal_information' => [
            'title' => 'Informații personale',
            'section' => [
                'beneficiary' => 'Beneficiar',
                'aggressor' => 'Informații despre agresor',
                'antecedents' => 'Antecedente caz',
                'flow' => 'Flux prezentare victimă',
            ],
        ],
        'detailed_evaluation' => [
            'labels' => [
                'specialists' => 'Specialisti care au colaborat la realizarea evaluarii',
                'full_name' => 'Nume si prenume',
                'institution' => 'Institutia',
                'relationship' => 'Relatia cu copilul/ familia',
                'contact_date' => 'Data contactarii',
                'meetings' => 'Inrevederi (sau convorbiri telefonice) pentru colectarea datelor',
                'specialist' => 'Specialist',
                'date' => 'Data',
                'location' => 'Locatia',
                'observations' => 'Observatii',
                'applicant' => 'Solicitant',
                'reporting_by' => 'Semnalare de caz de catre',
                'date_interval' => 'Data sau perioada raportata',
                'significant_events' => 'Evenimente semnificative de la respectiva data',
                'medical_need' => 'Nevoi din punct de vedere medical',
                'professional_need' => 'Nevoi din punct de vedere profesional',
                'emotional_and_psychological_need' => 'Nevoi din punct de vedere emotional si psihologic',
                'social_economic_need' => 'Nevoi din punct de vedere socio-economic',
                'legal_needs' => 'Nevoi din punct de vedere juridic',
                'extended_family' => 'Familia largita',
                'family_social_integration' => 'Integrarea sociala a familiei',
                'income' => 'Venit',
                'community_resources' => 'Resurse comunitare',
                'house' => 'Locuinta',
                'risk' => 'Riscuri pentru sitatii de criza cu interventie imediata',
                'psychological_advice' => 'Consiliere psihologica',
                'legal_advice' => 'Consiliere juridica',
                'legal_assistance' => 'Asistenta juridica',
                '' => 'Consiliere familiala',
                'prenatal_advice' => 'Consiliere prenatala',
                'social_advice' => 'Consiliere sociala',
                'medical_services' => 'Servicii medicale de diagnostic medico-legal in cazurile de traumatisme fizice
                        sau violenta sexuala si viol',
                'medical_payment' => 'Asigurarea costurilor privind serviciile medicale/ certificate medico-legale',
                'securing_residential_spaces' => 'Masuri de securizare a spatiilor locative',
                'occupational_program_services' => 'Servicii si programe ocupationale (programe de informare, consiliere
                            profesionala, dezvoltare profesionala, mediere pe piata muncii, fromare in sistem formal si
                            informal, inclusiv componenta de dezvoltare antreprenoriala',
                'educational_services_for_children' => 'Servicii educationale adresate copiilor victime din cadrul
                        cuplului mama-copil victime',
                'temporary_shelter_services' => 'Servicii de adapost temporar',
                'protection_order' => 'Ordin de protectie provizoriu/ Ordin de protectie',
                'crisis_assistance' => 'Asistenata si suport material in situatii de criza prin alocare de pachete de criza',
                'safety_plan' => 'Elaborarea planului de siguranta',
                'other_services' => 'Alte servicii',
                'recommendations_for_intervention_plan' => 'Listati toate eventualele recomandari pentru planul de interventie',
            ],
            'heading' => [
                'reasons_for_start_evaluation' => 'Motive pentru intierea evaluarii multidisciplinare',
                'historic_violence' => 'Istoricul violentei',
                'beneficiary_needs' => 'Nevoile beneficiarului',
                'family' => 'Factori de mediu si specifici familiei',
                'risk' => 'Riscuri',
                'partner' => 'Sotul/ partenerul',
                'recommendations_for_intervention_plan' => 'Recomandari pentru planul de interventie',
            ],
        ],
    ],

    'status' => [
        'active' => 'Activ',
        'reactivated' => 'Reactivat',
        'monitored' => 'În monitorizare',
        'closed' => 'Închis',
    ],

    'action' => [
        'create' => 'Înregistrează caz nou',
        'add_child' => 'Adaugă copil',
        'add_row' => 'Adauga inca un rand',
        'add_meet_row' => 'Adauga inca o intrevedere',
    ],

    'breadcrumb' => [
        'wizard_detailed_evaluation' => 'Evaluare detaliata',
    ],

    'placeholder' => [
        'full_name' => 'Introdu nume si prenume',
        'first_name' => 'Nume de familie',
        'last_name' => 'Numele mic',
        'age' => 'Varsta in ani impliniti',
        'date' => 'ZZ/LL/AN',
        'meet_location' => 'Locatia intrevederii',
        'relevant_details' => 'Detalii relevante pe scurt',
        'partner_relevant_observations' => 'Adauga orice observatie relevanta despre partener',
        'occupation' => 'Alege tip ocupatie',
        'applicant' => 'Alege sursa solicitarii',
        'reporting_by' => 'Introdu persoana si/ sau institutia',
        'date_interval' => 'Data sau perioada la care s-au inregistrat evenimentele de violenta',
        'significant_events' => 'Descrieti pe scurt evenimentele',
        'need_description' => 'Descrie nevoile',
        'crisis_risk' => 'Se vor mentiona acele evenimente/ contexte ce pot conduce la situatii de criza cu interventie imediata',
        'other_services' => 'Descrieti alte servicii recomandate',
    ],
    'helper_text' => [
        'recommendations_for_intervention_plan' => 'Exemple de recomandări posibile conform standardelor:
                * elaborarea planului individualizat de servicii pentru (numele și prenumele victimei) în vederea stabilirii obiectivelor și activităților;
                * înscrierea (numele și prenumele victimei) în evidența Centrului (nume centru) și semnarea contractului de furnizare servicii sociale;
                * admiterea (numele și prenumele victimei) în Centrul (nume centru) pentru acordarea de servicii de specialitate.
            ',
    ],
];
