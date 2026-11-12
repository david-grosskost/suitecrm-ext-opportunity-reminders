<?php

// Relances des opportunités (extension opportunity-reminders): its section on
// the Administration page, with a link to its settings page.

/** @var array<array-key, array<int, mixed>> $admin_group_header Set by SuiteCRM */
$admin_option_defs = [
    'Administration' => [
        'opportunity_reminders' => [
            'Schedulers',
            'LBL_OR_ADMIN_TITLE',
            'LBL_OR_ADMIN_DESC',
            './index.php?module=Administration&action=OpportunityReminders',
            'admin-scheduler',
        ],
    ],
];
$admin_group_header[] = [
    'LBL_OR_ADMIN_HEADER',
    '',
    false,
    $admin_option_defs,
    'LBL_OR_ADMIN_HEADER_DESC',
    'module_settings',
];
