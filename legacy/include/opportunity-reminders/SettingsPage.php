<?php

// Admin page of the opportunity reminders: action OpportunityReminders of the
// Administration module, mapped to this file by
// legacy/Extension/modules/Administration/Ext/ActionFileMap/opportunity-reminders.php.
// Run by SuiteCRM's controller, with the Administration labels in
// $mod_strings; administrators only.

declare(strict_types=1);

use SuiteCrmExt\OpportunityReminders\Page;
use SuiteCrmExt\OpportunityReminders\PageActions;
use SuiteCrmExt\OpportunityReminders\PageView;
use SuiteCrmExt\OpportunityReminders\Reminders;
use SuiteCrmExt\OpportunityReminders\Settings;

require_once __DIR__ . '/Settings.php';
require_once __DIR__ . '/Reminders.php';
require_once __DIR__ . '/PageActions.php';
require_once __DIR__ . '/Page.php';
// SuiteCRM's own view first (the current directory is public/legacy), then ours.
require_once getcwd() . '/include/MVC/View/SugarView.php';
require_once __DIR__ . '/PageView.php';

/** @var mixed $user */
$user = $GLOBALS['current_user'] ?? null;
if (!$user instanceof User || !is_admin($user)) {
    $denied = translate('ERR_NOT_ADMIN');
    sugar_die(is_string($denied) ? $denied : 'Unauthorized access to administration.');
}

/** @var mixed $labels */
$labels = $GLOBALS['mod_strings'] ?? [];
$labels = is_array($labels) ? $labels : [];

$state = Page::handle(
    $_SERVER['REQUEST_METHOD'] === 'POST' ? $_POST : [],
    Settings::read(),
    new class implements PageActions {
        public function save(int $days): void
        {
            Settings::save($days);
        }

        public function run(): int
        {
            return Reminders::run();
        }
    },
    $labels,
);

$view = new PageView(Page::label($labels, 'LBL_OR_ADMIN_TITLE'), Page::html($state['days'], $state['messages'], $labels));
$view->init();
$view->process();
