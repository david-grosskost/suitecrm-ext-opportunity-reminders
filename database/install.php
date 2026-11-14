<?php

// Install script of opportunity-reminders: creates the line « Relances des
// opportunités » of Administration → Planificateur (each day at 7:00). Run
// once, at the first activation, after Quick Repair has merged the job
// (suitecrm-core, scripts/extension-script.php), in SuiteCRM, as the
// administrator. From then on the scheduler belongs to the administrator:
// hours changed or made inactive, it is left as is.

// Its message is returned, not printed (echo is refused by the scanner of
// the Module Loader): the caller shows it.

declare(strict_types=1);

use SuiteCrmExt\OpportunityReminders\SchedulerRecord;

require_once __DIR__ . '/../legacy/include/opportunity-reminders/SchedulerRecord.php';

$db = DBManagerFactory::getInstance();
if (is_array($db->fetchByAssoc($db->query(SchedulerRecord::query($db->quoted(SchedulerRecord::JOB)))))) {
    return 'opportunity-reminders : planificateur déjà là, rien à faire.';
}

/** @var mixed $config */
$config = $GLOBALS['sugar_config'] ?? [];
$language = is_array($config) && is_string($config['default_language'] ?? null) ? $config['default_language'] : 'en_us';
$labels = return_module_language($language, 'Schedulers');
$name = is_string($labels['LBL_OPPORTUNITYREMINDERS'] ?? null) ? $labels['LBL_OPPORTUNITYREMINDERS'] : 'Relances des opportunités';

$scheduler = BeanFactory::newBean('Schedulers');
if (!$scheduler instanceof SugarBean) {
    throw new RuntimeException('module du planificateur introuvable');
}
foreach (SchedulerRecord::fields($name) as $field => $value) {
    $scheduler->{$field} = $value;
}
$scheduler->save();
return "opportunity-reminders : planificateur « {$name} » créé (chaque jour à 7 h).";
