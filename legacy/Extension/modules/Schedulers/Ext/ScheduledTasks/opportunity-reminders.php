<?php

// Relances des opportunités (extension opportunity-reminders): the job of
// Administration → Planificateur that creates the reminders. SuiteCRM lists
// the functions of $job_strings in the scheduler's « Tâche » list and calls
// the one of a scheduler with its job (SchedulersJob) as argument.

/** @var list<string> $job_strings Set by SuiteCRM */
$job_strings[] = 'opportunityReminders';

if (!function_exists('opportunityReminders')) {
    function opportunityReminders(mixed $job = null): bool
    {
        // The current directory is public/legacy.
        require_once getcwd() . '/custom/include/opportunity-reminders/Reminders.php';

        $created = SuiteCrmExt\OpportunityReminders\Reminders::run();
        $log = $GLOBALS['log'] ?? null;
        if (is_object($log) && method_exists($log, 'info')) {
            $log->info("opportunity-reminders : {$created} tâche(s) de relance créée(s)");
        }

        return true;
    }
}
