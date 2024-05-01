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
        'create_initial_evaluation' => [
            'title' => 'Evaluare initiala',
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
        'details' => [
            'label' => 'Detalii evaluare',
        ],
        'violence' => [
            'label' => 'Violență',
        ],
        'risk_factors' => [
            'label' => 'Factori de risc',
        ],
        'requested_services' => [
            'label' => 'Servicii solicitate',
        ],
        'beneficiary_situation' => [
            'label' => 'Situația beneficiarului',
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
        'initial_evaluation' => [
            'heading' => [
                'violence_history' => 'I. Istoricul violenței',
                'violences_types' => 'II. Formele și tipurile violenței',
                'heading_3' => 'III. Heading 3',
                'heading_4' => 'IV. Heading 4',
                'heading_5' => 'V. Heading 5',
                'heading_6' => 'VI. Heading 6',
                'types_of_requested_services' => 'Tipuri de servicii solicitate',
            ],
            'labels' => [
                'registered_date' => 'Data înregistrării',
                'file_number' => 'Număr fișă',
                'specialist' => 'Specialist',
                'method_of_identifying_the_service' => 'Modalitatea de identificare a serviciului de către solicitant',
                'violence_type' => 'Tipurile violenței domestice',
                'violence_primary_type' => 'Dintre care tipul primar',
                'frequency_violence' => 'Frecvența violenței',
                'description' => 'Descrierea succintă a problemei de violență domestică cu care se confruntă persoana',
                'previous_acts_of_violence' => '1. Au existat acte de violență domestică anterioare?',
                'violence_against_children_or_family_members' => '2. Au existat acte de violență asupra copiilor/altor membrii ai familiei/animalelor de companie?',
                'abuser_exhibited_generalized_violent' => '3. Agresorul a manifestat comportament violent generalizat față de terți (în afara familiei)?',
                'protection_order_in_past' => '4. În trecut a existat emis un ordin de protecție provizoriu/ordin de protecție?',
                'abuser_violated_protection_order' => '5. Agresorul a încălcat, în trecut, un ordin de protecție provizoriu/ ordin de protecție?',

                'frequency_of_violence_acts' => '6. Care este frecvența actelelor de violență domestică?',
                'use_weapons_in_act_of_violence' => '7.  În manifestarea actelor de violență domestică agresorul a folosit arme/ amenințarea cu arme?',
                'controlling_and_isolating' => '8. Agresorul a manifestat comportament de control și de izolare a victimei?',
                'stalked_or_harassed' => '9. Victima a fost urmărită/hărțuită?',
                'sexual_violence' => '10. Au existat manifestări de violență sexuală (viol sau viol marital) asupra victimei?',
                'death_threats' => '11. Victima a fost amenințată cu moartea/ șantaj/constrângeri?',
                'strangulation_attempt' => '12. Au existat tentative de strangulare, sugrumare asupra victimei?',

                'FR_S3Q1' => '13. Agresorul prezintă riscuri legate de consumul de alcool, droguri, medicamente, jocuri de noroc/video?',
                'FR_S3Q2' => '14. Agresorul manifestă comportament de posesivitate, gelozie extremă, alte atitudini dăunătoare?',
                'FR_S3Q3' => '15. Agresorul are probleme legate de sănătatea mentală, au existat amenințări cu din partea agresorului?',
                'FR_S3Q4' => '16. Agresorul prezintă manifestări de stres economic?',

                'FR_S4Q1' => '17. Victimei îi este frică pentru propria persoană și pentru alții?',
                'FR_S4Q2' => '18. Victima are o atitudine de acceptare și resemnare, considerând că nu i se va întâmpla niciodată ceva foarte rău (lovituri grave, pierderea unui simț/organ, deces)',

                'FR_S5Q1' => '19. A intervenit separarea/divorțul sau după caz au existat discuții prealabile cu privire la acestea?',
                'FR_S5Q2' => '20. Părintele agresor are contact cu copiii/ se mențin relațiile personale ale copilului cu părintele agresor?',
                'FR_S5Q3' => '21. Părintele agresor folosește programul de vizită pentru a hărțui și amenința în mod constant victima?',
                'FR_S5Q4' => '22. Există integrați în familie copil/copii proveniți din alte relații/căsătorii?',
                'FR_S5Q5' => '23. Au existat acte de violență domestică în timpul sarcinii?',

                'FR_S6Q1' => '24. Familia extinsă poate oferi',
                'FR_S6Q2' => '25. Vecinii/ prietenii pot oferi',

                'moment_of_evaluation' => 'Momentul evaluării situației',
                'description_of_situation' => 'A se include și situația juridică - acțiuni în instanță, situația socio-familială, situația medicală actuală):',
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
                'family_counseling' => 'Consiliere familiala',
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
                'recommendation_services' => 'Servicii recomandate',
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
        'wizard_initial_evaluation' => 'Evaluare initiala',
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
        'file_number' => 'Introdu un număr document',
        'specialist' => 'Alege un specialist',
        'method_of_identifying_the_service' => 'Descrie modalitatea de identificare și solicitare a serviciului',
        'violence_type' => 'Alege un tip de violență',
        'violence_primary_type' => 'Alege un tip de violență',
        'frequency_violence' => 'Alege o frecvență',
        'description' => 'Descrieți pe scurt problema și contextul',
        'observations' => 'Observații',
        'moment_of_evaluation' => 'Situația la data realizării evaluării inițiale',
        'description_of_situation' => 'Descrieți pe scurt situația',
    ],
    'helper_text' => [
        'recommendations_for_intervention_plan' => 'Exemple de recomandări posibile conform standardelor:
                * elaborarea planului individualizat de servicii pentru (numele și prenumele victimei) în vederea stabilirii obiectivelor și activităților;
                * înscrierea (numele și prenumele victimei) în evidența Centrului (nume centru) și semnarea contractului de furnizare servicii sociale;
                * admiterea (numele și prenumele victimei) în Centrul (nume centru) pentru acordarea de servicii de specialitate.
            ',
        'violence_description' => 'A se include și istoricul socio- familial, istoricul agresiunilor, istoricul juridic al solicitantei-ordin de protecție, plângeri penale',
    ],
];
