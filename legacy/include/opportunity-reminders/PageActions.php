<?php

declare(strict_types=1);

namespace SuiteCrmExt\OpportunityReminders;

/**
 * What the admin page asks SuiteCRM to do (SettingsPage.php), or a test.
 * An object, not callables: the scanner of the Module Loader refuses the
 * calls of a variable ($save()).
 */
interface PageActions
{
    /** Keeps the number of days. */
    public function save(int $days): void;

    /** Runs the reminders now; returns the tasks created. */
    public function run(): int;
}
