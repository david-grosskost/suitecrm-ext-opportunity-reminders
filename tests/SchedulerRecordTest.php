<?php

declare(strict_types=1);

namespace SuiteCrmExt\OpportunityReminders\Tests;

use PHPUnit\Framework\TestCase;
use SuiteCrmExt\OpportunityReminders\SchedulerRecord;

final class SchedulerRecordTest extends TestCase
{
    public function testTheSchedulerRunsTheJobEachDayAtSeven(): void
    {
        $fields = SchedulerRecord::fields('Relances des opportunités');

        self::assertSame('Relances des opportunités', $fields['name']);
        self::assertSame('function::opportunityReminders', $fields['job']);
        self::assertSame('0::7::*::*::*', $fields['job_interval']);
        self::assertSame('Active', $fields['status']);
        // Not at once on the first activation: only at 7:00.
        self::assertSame('0', $fields['catch_up']);
        self::assertNull($fields['date_time_end']);
    }

    public function testTheSchedulersOfTheJobAreFoundByIt(): void
    {
        self::assertSame(
            "SELECT id FROM schedulers WHERE deleted = 0 AND job = 'function::opportunityReminders'",
            SchedulerRecord::query("'function::opportunityReminders'"),
        );
    }
}
