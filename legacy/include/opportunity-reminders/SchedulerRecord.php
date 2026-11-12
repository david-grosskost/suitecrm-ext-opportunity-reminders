<?php

declare(strict_types=1);

namespace SuiteCrmExt\OpportunityReminders;

/**
 * The line of Administration → Planificateur that runs the reminders: created
 * at the first activation (database/install.php), deleted at the deactivation
 * (database/uninstall.php). In between it belongs to the administrator, who
 * may change its hours or make it inactive.
 */
final class SchedulerRecord
{
    /** Function of the job (legacy/Extension/modules/Schedulers/Ext/ScheduledTasks). */
    public const string FUNCTION = 'opportunityReminders';
    public const string JOB = 'function::' . self::FUNCTION;
    /** Minute::hour::day::month::weekday, in the administrator's time zone: each day at 7:00. */
    public const string INTERVAL = '0::7::*::*::*';

    /**
     * Fields of the scheduler, in the database formats.
     *
     * @return array<string, string|null>
     */
    public static function fields(string $name): array
    {
        return [
            'name' => $name,
            'job' => self::JOB,
            'job_interval' => self::INTERVAL,
            'date_time_start' => '2026-01-01 00:00:01',
            'date_time_end' => null,
            'status' => 'Active',
            // A morning missed (server stopped at 7:00): run once when it is back.
            'catch_up' => '1',
            'created_by' => '1',
            'modified_user_id' => '1',
        ];
    }

    /** Query of the ids of the schedulers of the job, not deleted. $job is already quoted. */
    public static function query(string $job): string
    {
        return "SELECT id FROM schedulers WHERE deleted = 0 AND job = {$job}";
    }
}
