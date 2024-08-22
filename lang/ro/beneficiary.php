<?php

declare(strict_types=1);

return [

    'label' => [
        'singular' => 'caz',
        'plural' => 'Cazuri',
    ],

    'labels' => [
        'registered_date' => 'Data înregistrării',
    ],

    'page' => [
        'create' => [
            'title' => 'Înregistrează caz nou',
        ],
        'view' => [
            'title' => '#:id :name',
        ],
        'identity' => [
            'title' => 'Date identitate',
        ],
        'edit_identity' => [
            'title' => 'Editează identitate beneficiar',
        ],

        'edit_children' => [
            'title' => 'Editează identitate copii',
        ],
        'personal_information' => [
            'title' => 'Informații caz',
        ],
        'edit_personal_information' => [
            'title' => 'Editează informații beneficiar',
        ],
        'edit_aggressor' => [
            'title' => 'Editează informații agresor',
        ],
        'edit_antecedents' => [
            'title' => 'Editează antecedente de caz',
        ],
        'edit_flow_presentation' => [
            'title' => 'Editează flux prezentare victimă',
        ],
        'initial_evaluation' => [
            'title' => 'Evaluare inițială',
        ],
        'create_initial_evaluation' => [
            'title' => 'Evaluare inițială',
        ],
        'edit_evaluation_details' => [
            'title' => 'Editează detaliile evaluării',
        ],
        'edit_violence' => [
            'title' => 'Editează violența',
        ],
        'edit_risk_factors' => [
            'title' => 'Editează factorii de risc',
        ],
        'edit_requested_services' => [
            'title' => 'Editează serviciile solicitate',
        ],
        'edit_beneficiary_situation' => [
            'title' => 'Editează situația beneficiarului',
        ],
        'create_detailed_evaluation' => [
            'title' => 'Evaluare detaliată',
        ],
        'view_detailed_evaluation' => [
            'title' => 'Evaluare detaliată',
        ],
        'edit_beneficiary_partner' => [
            'title' => 'Editează partener',
        ],
        'edit_multidisciplinary_evaluation' => [
            'title' => 'Editează evaluarea multidisciplinară',
        ],
        'edit_detailed_evaluation_result' => [
            'title' => 'Editează rezultate',
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
            'label' => 'Informații caz',
        ],
        'detailed_evaluation' => [
            'label' => 'Detalii evaluare',
        ],
        'partner' => [
            'label' => 'Partener',
        ],
        'multidisciplinary_evaluation' => [
            'label' => 'Evaluare multidisciplinară',
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
            'heading_description' => 'Pentru a modifica informațiile din această pagină, vă rugăm accesați secțiunea',
            'tab' => [
                'beneficiary' => 'Identitate beneficiar',
                'children' => 'Identitate copii',
            ],
            'labels' => [
                'email' => 'Email beneficiar',
            ],
        ],

        'personal_information' => [
            'section' => [
                'beneficiary' => 'Beneficiar',
                'aggressor' => 'Informații despre agresor',
                'antecedents' => 'Antecedente caz',
                'flow' => 'Flux prezentare victimă',
            ],
            'heading' => [
                'aggressor' => 'Agresor #:number',
                'delete_aggressor' => 'Șterge informația despre agresor',
            ],
            'actions' => [
                'add_aggressor' => 'Adaugă încă un agresor',
                'delete_aggressor' => 'Șterge agresor',
            ],
            'label' => [
                'delete_aggressor_description' => 'Acțiunea va șterge din această pagină întreaga secțiune cu informațiile introduse despre acest agresor. Datele șterse nu vor mai putea fi recuperate.',
            ],
        ],

        'initial_evaluation' => [
            'heading' => [
                'violence_history' => 'I. Istoricul violenței',
                'violences_types' => 'II. Formele și tipurile violenței',
                'risk_factors' => 'III. Factori de risc legați de comportamentul agresorului',
                'victim_perception_of_the_risk' => 'IV. Percepția victimei asupra riscului',
                'aggravating_factors' => 'V. Factori agravanți',
                'social_support' => 'VI. Suport social',
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

                'aggressor_present_risk_related_to_vices' => '13. Agresorul prezintă riscuri legate de consumul de alcool, droguri, medicamente, jocuri de noroc/video?',
                'aggressor_is_possessive_or_jealous' => '14. Agresorul manifestă comportament de posesivitate, gelozie extremă, alte atitudini dăunătoare?',
                'aggressor_have_mental_problems' => '15. Agresorul are probleme legate de sănătatea mentală, au existat amenințări cu din partea agresorului?',
                'aggressor_present_manifestations_of_economic_stress' => '16. Agresorul prezintă manifestări de stres economic?',

                'victim_afraid_for_himself' => '17. Victimei îi este frică pentru propria persoană și pentru alții?',
                'victim_has_an_attitude_of_acceptance' => '18. Victima are o atitudine de acceptare și resemnare, considerând că nu i se va întâmpla niciodată ceva foarte rău (lovituri grave, pierderea unui simț/organ, deces)',

                'separation' => '19. A intervenit separarea/divorțul sau după caz au existat discuții prealabile cu privire la acestea?',
                'aggressor_parent_has_contact_with_children' => '20. Părintele agresor are contact cu copiii/ se mențin relațiile personale ale copilului cu părintele agresor?',
                'aggressor_parent_threaten_the_victim_in_the_visitation_program' => '21. Părintele agresor folosește programul de vizită pentru a hărțui și amenința în mod constant victima?',
                'children_from_other_marriage_are_integrated_into_family' => '22. Există integrați în familie copil/copii proveniți din alte relații/căsătorii?',
                'domestic_violence_during_pregnancy' => '23. Au existat acte de violență domestică în timpul sarcinii?',

                'extended_family_can_provide' => '24. Familia extinsă poate oferi',
                'friends_can_provide' => '25. Vecinii/ prietenii pot oferi',

                'moment_of_evaluation' => 'Momentul evaluării situației',
                'description_of_situation' => 'A se include și situația juridică - acțiuni în instanță, situația socio-familială, situația medicală actuală):',
            ],
        ],

        'detailed_evaluation' => [
            'labels' => [
                'specialists' => 'Specialiști care au colaborat la realizarea evaluării',
                'full_name' => 'Nume si prenume',
                'institution' => 'Institutia',
                'relationship' => 'Relatia cu copilul/ familia',
                'contact_date' => 'Data contactarii',
                'meetings' => 'Inrevederi (sau convorbiri telefonice) pentru colectarea datelor',
                'specialist' => 'Specialist',
                'date' => 'Data',
                'location' => 'Locatia',
                'observations' => 'Observații',
                'applicant' => 'Solicitant',
                'reporting_by' => 'Semnalare de caz de către',
                'date_interval' => 'Data sau perioada raportată',
                'significant_events' => 'Evenimente semnificative de la respectiva data',
                'medical_need' => 'Nevoi din punct de vedere medical',
                'professional_need' => 'Nevoi din punct de vedere profesional',
                'emotional_and_psychological_need' => 'Nevoi din punct de vedere emoțional și psihologic',
                'social_economic_need' => 'Nevoi din punct de vedere socio-economic',
                'legal_needs' => 'Nevoi din punct de vedere juridic',
                'extended_family' => 'Familia lărgită',
                'family_social_integration' => 'Integrarea socială a familiei',
                'income' => 'Venit',
                'community_resources' => 'Resurse comunitare',
                'house' => 'Locuință',
                'risk' => 'Riscuri pentru situații de criză cu intervenție imediată',
                'psychological_advice' => 'Consiliere psihologică',
                'legal_advice' => 'Consiliere juridică',
                'legal_assistance' => 'Asistență juridică',
                'family_counseling' => 'Consiliere familială',
                'prenatal_advice' => 'Consiliere parentală',
                'social_advice' => 'Consiliere socială',
                'medical_services' => 'Servicii medicale de diagnostic medico-legal în cazurile de traumatisme fizice
                            sau violență sexuală și viol',
                'medical_payment' => 'Asigurarea costurilor privind serviciile medicale/certificate medico-legale',
                'securing_residential_spaces' => 'Măsuri de securizare a spațiilor locative',
                'occupational_program_services' => 'Servicii si programe ocupaționale (programe de informare, consiliere
                            profesională, dezvoltare profesională, mediere pe piața muncii, formare în sistem formal și
                            informai, inclusiv componenta de dezvoltare antreprenorială)',
                'educational_services_for_children' => 'Servicii educaționale adresate copiilor victime din cadrul
                            cuplului mama-copil victime',
                'temporary_shelter_services' => 'Servicii de adăpost temporar',
                'protection_order' => 'Ordin de protecție provizoriu/Ordin de protecție',
                'crisis_assistance' => 'Asistență și suport material în situații de criză prin alocare de pachete de criză',
                'safety_plan' => 'Elaborarea Planului de siguranță',
                'other_services' => 'Alte servicii',
                'recommendations_for_intervention_plan' => 'Listați toate eventualele recomandări pentru planul de intervenție',
            ],
            'heading' => [
                'reasons_for_start_evaluation' => 'Motive pentru inițierea evaluării multidisciplinare',
                'historic_violence' => 'Istoricul violenței',
                'beneficiary_needs' => 'Nevoile beneficiarului',
                'family' => 'Factori de mediu și specifici familiei',
                'risk' => 'Riscuri',
                'partner' => 'Soțul/ partenerul',
                'recommendations_for_intervention_plan' => 'Recomandari pentru planul de interventie',
                'recommendation_services' => 'Servicii recomandate',
            ],
        ],

        'specialists' => [
            'title' => 'Echipa de caz',
            'add_action' => 'Adaugă specialist',
            'change_action' => 'Modifică',
            'heading' => [
                'add_modal' => 'Adaugă specialist în echipă',
                'edit_modal' => 'Modifică  specialist în echipă',
                'delete_modal' => 'Elimină membrul din echipa de caz',
            ],
            'labels' => [
                'name' => 'Nume specialist',
                'role' => 'Rol',
                'status' => 'Status',
                'roles' => 'Rol în echipa de caz',
                'summarize' => '{1} +:count alt specialist|[2,19] +:count alți specialiști|[20,*] +:count de alți specialiști',
            ],
            'action' => [
                'delete' => 'Elimină din echipa de caz',
            ],
        ],

        'documents' => [
            'actions' => [
                'add' => 'Încarcă document',
                'create' => 'Adaugă document',
                'delete' => 'Șterge document',
                'download' => 'Descarcă document',
            ],
            'title' => [
                'page' => 'Documente',
                'table' => 'Arhivă documente',
                'add_modal' => 'Adaugă document',
                'edit_modal' => 'Actualizează detalii document',
                'delete_modal' => 'Șterge document',
            ],
            'labels' => [
                'type' => 'Tip document',
                'name' => 'Denumire document',
                'observations' => 'Observații',
                'date' => 'Data',
                'document_file' => 'Încarcă document',
                'summarize' => '{1} +:count alt document|[2,19] +:count alte documente|[20,*] +:count de alte documente',
                'delete_description' => 'Odată șters un document, acesta nu mai poate fi recuperat. Te rugăm să te asiguri că nu mai este nevoie de acest document în dosarul de caz.',
                'empty_state_header' => 'Formatul documentului nu permite previzualizarea lui',
                'empty_state_description' => 'Descărcați documentul pentru a-l putea vizualiza.',
            ],
        ],

        'history' => [
            'titles' => [
                'list' => 'Istoric',
            ],

            'headings' => [
                'table' => 'Istoric modificări & accesare caz',
            ],

            'labels' => [
                'date' => 'Data',
                'time' => 'Ora',
                'user' => 'Utilizator',
                'description' => 'Acțiune',
                'section' => 'Secțiune',
                'subsection' => 'Sub-secțiune',
                'view_action' => 'Detalii',
                'beneficiary' => 'Beneficiar',
                'meeting' => 'Întâlniri',
                'multidisciplinaryEvaluation' => 'Evaluare multidisciplinară',
                'riskFactors' => 'Factori de risc',
                'violence' => 'Violență',
                'violenceHistory' => 'Istoric violență',
                'document' => 'Documente',
                'aggressor' => 'Agresor',
                'evaluateDetails' => 'Detalii evaluare',
                'detailedEvaluationResult' => 'Rezultate evalaure detaliată',
                'team' => 'Echipa de caz',
                'beneficiarySituation' => 'Situatie beneficiar',
                'beneficiaryPartner' => 'Partener',
            ],

            'actions' => [
                'view' => 'Istoric modificări',
            ],

            'breadcrumbs' => [
                'list' => 'Istoric modificări & accesare caz',
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
        'add_violence_history' => 'Adaugă încă o dată sau perioadă',
        'start_evaluation' => 'Începe evaluarea',
    ],

    'breadcrumb' => [
        'wizard_detailed_evaluation' => 'Evaluare detaliată',
        'wizard_initial_evaluation' => 'Evaluare initială',
        'personal_information' => 'Informații caz',
    ],

    'placeholder' => [
        'full_name' => 'Introdu nume si prenume',
        'first_name' => 'Nume de familie',
        'last_name' => 'Numele mic',
        'age' => 'Varsta in ani impliniti',
        'date' => 'ZZ/LL/AN',
        'meet_location' => 'Locatia intrevederii',
        'relevant_details' => 'Detalii relevante pe scurt',
        'partner_relevant_observations' => 'Adaugă orice observație relevantă despre partener',
        'occupation' => 'Alege tip ocupatie',
        'applicant' => 'Alege solicitantul',
        'reporting_by' => 'Introdu persoana și/sau instituția',
        'date_interval' => 'Data sau perioada la care s-au înregistrat evenimente de violență',
        'significant_events' => 'Descrieți pe scurt evenimentele',
        'need_description' => 'Descrieți nevoile',
        'crisis_risk' => 'Se vor menționa acele evenimente/contexte/ ce pot conduce la situații de criză cu intervenție imediată',
        'other_services' => 'Descrieți alte servicii',
        'recommendations_for_intervention_plan' => 'Descrieți pe scurt recomandările',
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
        'email' => 'Introdu un email',
        'consent' => 'Odată înregistrat cazul în sistem, aceste formulare de obținere a consimțământului vor putea fi încarcate în sistem, în secțiunea Documente Beneficiar.',
        'check_beneficiary_exists' => 'Verifică dacă beneficiarul există în baza de date (Opțional)',
        'beneficiary_exists' => 'CNP-ul a fost identificat în această bază de date, asociat cazului Maria Popescu. <a href="#">Vezi detalii</a>',
        'beneficiary_not_exists' => '<i class="heroicon-check"></i>CNP-ul nu a fost identificat în această bază de date și nici în cea a altor centre ale instituției.',
        'file_name' => 'Nume document',

    ],
    'helper_text' => [
        'recommendations_for_intervention_plan' => 'Exemple de recomandări posibile conform standardelor:
                * elaborarea planului individualizat de servicii pentru (numele și prenumele victimei) în vederea stabilirii obiectivelor și activităților;
                * înscrierea (numele și prenumele victimei) în evidența Centrului (nume centru) și semnarea contractului de furnizare servicii sociale;
                * admiterea (numele și prenumele victimei) în Centrul (nume centru) pentru acordarea de servicii de specialitate.
            ',
        'violence_description' => 'A se include și istoricul socio- familial, istoricul agresiunilor, istoricul juridic al solicitantei-ordin de protecție, plângeri penale',
        'initial_evaluation' => 'Identificarea nevoilor inițiale',
        'initial_evaluation_2' => 'Pentru oferirea de servicii imediate',
        'detailed_evaluation' => 'Evaluarea multidiciplinară',
        'detailed_evaluation_2' => 'Pentru informarea planului de intervenție',
        'document_file' => 'Fișierele acceptate sunt de tip .pdf, .doc/docx, .xls/xlsx, .csv, .png, .tiff, .jpg. Dimensiunea maximă nu poate depăși :size',
        'documents' => 'Niciun document încărcat',
        'documents_2' => 'Încarcă un prim document în fișa beneficiarei',
    ],
];
