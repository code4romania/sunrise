<?php

declare(strict_types=1);

return [

    'label' => [
        'singular' => 'Serviciu',
        'plural' => 'Servicii',
    ],

    'field' => [
        'name' => 'Numele serviciului',
        'description' => 'Descrierea serviciului',
    ],

    'actions' => [
        'change_status' => [
            'activate' => 'Activează',
            'inactivate' => 'Dezactivează',
            'inactivate_modal' => 'Inactivează serviciu',
        ],
        'create' => 'Adaugă serviciu',
        'delete' => 'Șterge serviciu',
        'change_service' => 'Modică serviciu',
        'view_counseling_sheet' => 'Vezi fișa',
    ],

    'labels' => [
        'name' => 'Nume serviciu',
        'interventions' => 'Intervenții',
        'cases' => 'Cazuri',
        'status' => 'Status',
        'select' => 'Selecție',
    ],

    'headings' => [
        'navigation' => 'Nomenclator servicii',
        'empty_state_table' => 'Niciun serviciu identificat. Adaugă un prim serviciu  ca acesta să fie disponibil pentru a fi inclus în planurile de intervenție ale beneficiarilor.',
        'list_page' => 'Nomenclator servicii specializate',
        'list_table' => 'Toate serviciile',
        'create_page' => 'Adaugă serviciu',
        'edit_page' => 'Modifică serviciu :name',
        'interventions' => 'Intervenții asociate serviciului',
        'inactivate_modal' => 'Inactivează serviciul în nomenclator',
    ],

    'helper_texts' => [
        'list_page_subheading' => 'Definește serviciile pe care le oferă organizația, pentru ca ele să poată fi adăugate la planurile de intervenție ale beneficiarilor. Fiecare serviciu și intervenție vor fi detaliate la momentul adăugării lor în plan. Pentru un exemple specifice puteți consulta manualul utilizatorului.',
        'before_form' => 'Configurează serviciul și intervențiile oferite în cadrul acestuia. Ele vor fi disponibile pentru a fi incluse în planurile de intervenție ale beneficiarilor Centrului, atât timp cât serviciul este menținut cu status activ..',
        'interventions' => 'Selectează toate intervențiile care dorești să fie disponibile în cadrul acestui serviciu.*',
        'inactivate_modal' => 'Prin inactivarea serviciului acesta nu va mai fi disponibil pentru fi adăugat în planurile de intervenție ale benficiarilor, de la momentul inactivării. Pentru cazurile în care s-a folosit deja acest serviciu în fișele de beneficiar, informația nu se va ștearge din istoricul cazurilor în care a fost folosit.',
        'counseling_sheet' => 'Serviciul conține și o fișă de consiliere care poate fi completată pentru fiecare beneficiar, atunci când serviciul este inclus în planul de consiliere.',
    ],
];
