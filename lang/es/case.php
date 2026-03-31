<?php

declare(strict_types=1);

return [

    'headings' => [
        'list_page' => 'Casos',
        'all_cases' => 'Todos los casos',
        'register_new' => 'Registrar nuevo caso',
        'register_first' => 'Registrar un primer caso',
    ],

    'table' => [
        'file_number' => 'Nº Ficha',
        'beneficiary' => 'Beneficiario',
        'opened_at' => 'Abierto el',
        'monitored_at' => 'Monitorizado el',
        'case_manager' => 'Gestor de caso',
        'status' => 'Estado',
    ],

    'empty_state' => [
        'heading' => 'Ningún caso identificado',
        'description' => 'Añade ahora un nuevo caso para iniciar la gestión del caso de un beneficiario.',
        'coming_soon' => 'El flujo de registro de nuevos casos (con verificación de NIF en la base de datos del centro y de la institución) está en desarrollo y estará disponible próximamente.',
    ],

    'count' => ':count casos',

    'aggressors_documented' => 'agresores documentados',

    'view' => [
        'breadcrumb_all' => 'Todos los casos',
        'modification_history' => 'Historial de modificaciones',
        'modification_history_download' => 'Descargar Excel',
        'modification_history_download_csv' => 'Descargar CSV',
        'case_actions' => 'Acciones del caso',
        'see_details' => 'Ver detalles',
        'see_plan_details' => 'Ver detalles del plan',
        'view_full_plan' => 'Ver plan completo',
        'view_full_beneficiary' => 'Ver detalles completos',
        'case_created_at' => 'Fecha de creación del caso',
        'identity' => 'Datos de identidad',
        'identity_page' => [
            'download_sheet' => 'Descargar ficha',
            'fab_beneficiary_details' => 'Detalles del beneficiario',
        ],
        'case_info' => 'Información del caso',
        'aggressor' => 'Agresor',
        'initial_evaluation' => 'Evaluación inicial',
        'initial_eval' => [
            'violence_type' => 'Tipo de violencia',
            'violence_means' => 'Medios utilizados',
        ],
        'detailed_evaluation' => 'Evaluación detallada',
        'intervention_plan' => 'Plan de intervención',
        'case_monitoring' => 'Seguimiento del caso',
        'case_closure' => 'Cierre del caso',
        'case_team' => 'Equipo del caso',
        'manage_case_team' => 'Gestionar equipo',
        'documents' => 'Documentos',
        'manage_documents' => 'Gestionar documentos',
        'manage_monitoring' => 'Gestionar seguimiento',
        'related_files' => 'Fichas relacionadas (historial del caso)',
        'empty_initial_eval' => 'Identificación de necesidades iniciales para la prestación de servicios inmediatos',
        'empty_detailed_eval' => 'Evaluación multidisciplinar para informar el plan de intervención',
        'start_evaluation' => 'Iniciar evaluación',
        'empty_intervention_plan' => 'La beneficiaria no tiene un plan de intervención. Cree uno ahora y añada los servicios que debe recibir.',
        'create_plan' => 'Crear plan',
        'empty_monitoring' => 'Añadir fichas de seguimiento periódico del caso',
        'complete_monitoring_sheet' => 'Completar ficha de seguimiento',
        'empty_closure' => 'Cuando el caso pase a estado Cerrado, podrá completar la Ficha de Cierre',
        'complete_closure_sheet' => 'Completar ficha de cierre',
        'empty_documents' => 'Ningún documento cargado. ¡Cargue un primer documento en la ficha de la beneficiaria!',
        'upload_document' => 'Cargar documento',
        'role' => 'Rol',
        'specialist' => 'Especialista',
        'last_monitoring' => 'Último seguimiento',
        'total_monitorings' => 'Total de seguimientos realizados',
        'closed_at' => 'Cerrado el',
        'closure_method' => 'Método de cierre',
    ],

];
