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
            'inactivate_intervention' => 'Inactivează intervenția',
        ],
        'create' => 'Adaugă serviciu',
        'delete' => 'Șterge serviciu',
        'change_service' => 'Modică serviciu',
        'view_counseling_sheet' => 'Vezi fișa',
    ],

    'labels' => [
        'name' => 'Tip serviciu',
        'catalog_service' => 'Tip serviciu (din catalog)',
        'intervention_name' => 'Nume intervenție',
        'interventions' => 'Intervenții',
        'intervention_item' => 'Intervenție',
        'cases' => 'Cazuri',
        'status' => 'Status',
        'select' => 'Selecție',
    ],

    'headings' => [
        'navigation' => 'Lista tipuri de servicii',
        'empty_state_table' => 'Niciun serviciu identificat. Adaugă un prim serviciu ca acesta să fie disponibil pentru a fi inclus în Dosarul servicii VD al beneficiarilor.',
        'list_page' => 'Lista tipuri de servicii specializate',
        'list_table' => 'Toate serviciile',
        'create_page' => 'Adaugă serviciu',
        'edit_page' => 'Modifică serviciu :name',
        'interventions' => 'Intervenții asociate serviciului',
        'inactivate_modal' => 'Inactivează serviciul în nomenclator',
        'inactivate_intervention_modal' => 'Inactivează intervenția în nomenclator',
        'view_service_page' => 'Serviciu :service_name',
    ],

    'helper_texts' => [
        'list_page_subheading' => 'Definește serviciile pe care le oferă organizația, pentru ca ele să poată fi adăugate în Dosarul servicii VD al beneficiarilor. Fiecare serviciu și intervenție vor fi detaliate la momentul adăugării lor. Pentru exemple specifice puteți consulta <a href=":user_manual_url" >
            <span class="font-semibold text-lg text-custom-600 dark:text-custom-400 group-hover/link:underline group-focus-visible/link:underline" style="--c-400:var(--primary-400);--c-600:var(--primary-600);">
                        manualul utilizatorului.
                    </span>
            </a>',
        'before_form' => 'Configurează serviciul și intervențiile oferite în cadrul acestuia. Ele vor fi disponibile pentru a fi incluse în Dosarul servicii VD al beneficiarilor Centrului, atât timp cât serviciul este menținut cu status activ.',
        'interventions' => 'Selectează toate intervențiile care dorești să fie disponibile în cadrul acestui serviciu.*',
        'under_interventions_table' => '*Dacă lista de intervenții nu acoperă toate tipurile de intervenții oferite de organizația ta, te rugăm contactează administratorul platformei Sunrise pentru a face sugestii de extindere a listei.',
        'inactivate_modal' => 'Prin inactivarea serviciului acesta nu va mai fi disponibil pentru fi adăugat în Dosarul servicii VD al beneficiarilor, de la momentul inactivării. Pentru cazurile în care s-a folosit deja acest serviciu în fișele de beneficiar, informația nu se va șterge din istoricul cazurilor în care a fost folosit.',
        'inactivate_intervention_modal' => 'Prin inactivarea intervenției aceasta nu va mai fi disponibilă pentru fi adăugată în Dosarul servicii VD al beneficiarilor, de la momentul inactivării. Pentru cazurile în care s-a folosit deja această intervenție în fișele de beneficiar, informația nu se va șterge din istoricul cazurilor în care a fost folosită.',
        'counseling_sheet' => 'Serviciul conține și o fișă de consiliere care poate fi completată pentru fiecare beneficiar, atunci când serviciul este inclus în planul de consiliere.',
    ],
];
