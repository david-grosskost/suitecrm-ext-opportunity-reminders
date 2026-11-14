<?php

// Uninstall script of opportunity-reminders: deletes its scheduler, whatever
// the administrator changed in it, before the job's files are removed (a
// scheduler left behind would call a function that no longer exists). The
// tasks it created and the number of days stay. Reactivated, the extension
// creates the scheduler again.

// Its message is returned, not printed (echo is refused by the scanner of
// the Module Loader): the caller shows it.

declare(strict_types=1);

use SuiteCrmExt\OpportunityReminders\SchedulerRecord;

require_once __DIR__ . '/../legacy/include/opportunity-reminders/SchedulerRecord.php';

$db = DBManagerFactory::getInstance();
$result = $db->query(SchedulerRecord::query($db->quoted(SchedulerRecord::JOB)));
$deleted = 0;
while (is_array($row = $db->fetchByAssoc($result))) {
    $scheduler = BeanFactory::getBean('Schedulers', is_string($row['id'] ?? null) ? $row['id'] : '');
    if ($scheduler instanceof SugarBean && !empty($scheduler->id)) {
        $scheduler->mark_deleted($scheduler->id);
        ++$deleted;
    }
}
return "opportunity-reminders : {$deleted} planificateur(s) supprimé(s).";
