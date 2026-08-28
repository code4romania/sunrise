<?php

declare(strict_types=1);

return [

    'label' => [
        'singular' => 'usuario',
        'plural' => 'Usuarios',
    ],

    'specialist_label' => [
        'singular' => 'especialista',
        'plural' => 'Especialistas',
    ],

    'titles' => [
        'create_specialist' => 'Añadir especialista',
    ],

    'labels' => [
        'first_name' => 'Nombre',
        'last_name' => 'Apellido',
        'roles' => 'Roles',
        'account_status' => 'Estado de la cuenta',
        'last_login_at' => 'Último acceso',
        'email' => 'Email',
        'phone_number' => 'Número de teléfono',
        'select_roles' => 'Rol de especialidad',
        'case_permissions' => 'Permisos de casos',
        'admin_permissions' => 'Permisos de administración',
        'last_login_at_date_time' => 'Fecha y hora del último acceso',
        'can_be_case_manager' => 'Puede asumir el rol de gestor de casos',
        'status' => 'Estado de la cuenta',
    ],

    'stats' => [
        'open' => 'Casos abiertos',
        'monitoring' => 'Casos en seguimiento',
        'closed' => 'Casos cerrados',
    ],

    'role' => [
        'admin' => 'Administrador',
        'specialist' => 'Especialista',
        'manager' => 'Gestor',

    ],

    'heading' => [
        'table' => 'Equipo interdisciplinario',
        'active_users' => 'Usuarios activos',
        'specialist_details' => 'Detalles del especialista',
    ],

    'placeholders' => [
        'user_role_without_permissions_for_all_cases' => 'Este tipo de usuario <span class="italic">solo tiene acceso a los casos de los equipos a los que pertenece</span> y no tiene derechos de administración del sistema. Puede otorgar permisos adicionales desde la lista a continuación.',
        'user_role_with_permissions_for_all_cases' => 'Este tipo de rol tiene acceso <span class="italic">a todos los casos dentro del Centro</span>, pero no tiene derechos de administración del sistema. Puede otorgar permisos adicionales desde la lista a continuación.',
        'dashboard_cart' => 'Distribución mensual del número total de usuarios activos de Sunrise. Un usuario se considera activo si ha accedido a la plataforma al menos una vez en el mes calendario respectivo.',
        'table_observations' => '** Usuario tipo administrador del sistema.',
    ],

    'actions' => [
        'deactivate' => 'Desactivar cuenta',
        'reset_password' => 'Restablecer contraseña',
        'resend_invitation' => 'Reenviar invitación',
        'activate' => 'Reactivar cuenta',
        'add_specialist' => 'Añadir especialista',
    ],

    'action_resend_invitation_confirm' => [
        'title' => 'Reenviar invitación',
        'success' => 'La invitación ha sido enviada con éxito.',
        'failure_title' => '¡Error al reenviar la invitación!',
        'failure_body' => 'Ha ocurrido un error al reenviar la invitación',
    ],

    'action_deactivate_confirm' => [
        'title' => 'Desactivar cuenta',
        'success' => 'Cuenta desactivada con éxito',
        'description' => 'Una vez que la cuenta está desactivada, el usuario ya no tendrá acceso a la plataforma. Todos los datos asociados con la cuenta permanecerán en la base de datos. Para dar acceso al usuario nuevamente, deberá reactivar la cuenta desde su perfil.',
    ],

    'action_reactivate_confirm' => [
        'title' => 'Desactivar cuenta',
        'success' => 'Cuenta reactivada con éxito',
    ],

    'action_reset_password_confirm' => [
        'title' => 'Restablecer contraseña',
        'success' => 'El correo electrónico ha sido enviado con éxito',
    ],

    'status' => [
        'active' => 'Activo',
        'inactive' => 'Inactivo',
    ],

    'inactive_error' => [
        'title' => 'Tu cuenta no está activa.',
        'body' => 'Tu cuenta no está activa. Para más detalles, por favor contacta a un administrador.',
    ],
];
