<?php

declare(strict_types=1);

return [

    'headings' => [
        'list_page' => 'Cazuri',
        'all_cases' => 'Toate cazurile',
        'register_new' => 'Înregistrează caz nou',
        'register_first' => 'Înregistrează un prim caz',
    ],

    'table' => [
        'file_number' => 'Nr Fișă',
        'beneficiary' => 'Beneficiar',
        'opened_at' => 'Deschis la',
        'monitored_at' => 'Monitorizat la',
        'case_manager' => 'Manager caz',
        'status' => 'Status',
    ],

    'empty_state' => [
        'heading' => 'Niciun caz identificat',
        'description' => 'Adaugă chiar acum un caz nou, pentru a iniția managementul de caz al unui beneficiar.',
        'coming_soon' => 'Fluxul de înregistrare caz nou (cu verificare CNP în baza de date a centrului și a instituției) este în dezvoltare și va fi disponibil în curând.',
    ],

    'create' => [
        'title' => 'Înregistrează caz nou',
        'wizard' => [
            'consent' => 'Consimțământ',
            'cnp' => 'CNP',
            'identity_beneficiary' => 'Identitate beneficiar',
            'identity_children' => 'Identitate copii',
            'case_info' => 'Informații caz',
            'aggressor' => 'Informații despre agresor',
            'flow_presentation' => 'Flux prezentare victimă',
            'case_team' => 'Defineste rol în echipa de caz',
        ],
        'cnp_no_access' => 'Nu aveți acces la acest caz. CNP-ul aparține unui beneficiar înregistrat în acest centru.',
        'copy_from_center' => 'Datele vor fi completate automat din centrul :center.',
    ],

    'count' => ':count cazuri',

    'view' => [
        'breadcrumb_all' => 'Toate cazurile',
        'modification_history' => 'Istoric modificări',
        'modification_history_download' => 'Descarcă Excel',
        'modification_history_download_csv' => 'Descarcă CSV',
        'case_actions' => 'Acțiuni caz',
        'see_details' => 'Vezi detalii',
        'see_plan_details' => 'Vezi detalii plan',
        'identity' => 'Date identitate',
        'identity_page' => [
            'download_sheet' => 'Descarcă fișă',
            'fab_beneficiary_details' => 'Detalii beneficiar',
        ],
        'case_info' => 'Informații caz',
        'initial_evaluation' => 'Evaluare inițială',
        'detailed_evaluation' => 'Evaluare detaliată',
        'intervention_plan' => 'Plan de intervenție',
        'case_monitoring' => 'Monitorizare caz',
        'case_closure' => 'Închidere caz',
        'case_team' => 'Echipa de caz',
        'manage_case_team' => 'Gestionează echipa',
        'documents' => 'Documente',
        'manage_documents' => 'Gestionează documente',
        'manage_monitoring' => 'Gestionează monitorizare',
        'related_files' => 'Fișe conectate cazului (istoric caz)',
        'empty_initial_eval' => 'Identificarea nevoilor inițiale pentru oferirea de servicii imediate',
        'empty_detailed_eval' => 'Evaluarea multidisciplinară pentru informarea planului de intervenție',
        'start_evaluation' => 'Începe evaluarea',
        'empty_intervention_plan' => 'Beneficiara nu are un plan de intervenție creat. Creează chiar acum un plan și adaugă serviciile de care ar trebui să beneficieze.',
        'create_plan' => 'Creează plan',
        'empty_monitoring' => 'Adaugă fișe de monitorizare periodică cazului',
        'complete_monitoring_sheet' => 'Completează fișa de monitorizare',
        'empty_closure' => 'După ce cazul este mutat în status Închis, vei putea completa Fișa de Închidere',
        'complete_closure_sheet' => 'Completează fișa de închidere',
        'empty_documents' => 'Niciun document încărcat. Încarcă un prim document în fișa beneficiara!',
        'upload_document' => 'Încarcă document',
        'role' => 'Rol',
        'specialist' => 'Specialist',
        'last_monitoring' => 'Ultima monitorizare',
        'total_monitorings' => 'Total monitorizări efectuate',
        'closed_at' => 'Închis la data',
        'closure_method' => 'Modalitate închidere',
    ],

];
