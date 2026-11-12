<?php

declare(strict_types=1);

namespace SuiteCrmExt\OpportunityReminders\Tests;

use PHPUnit\Framework\TestCase;
use SuiteCrmExt\OpportunityReminders\Reminders;
use SuiteCrmExt\OpportunityReminders\Seed\IdleOpportunities;
use SuiteCrmExt\OpportunityReminders\Settings;

final class IdleOpportunitiesTest extends TestCase
{
    public function testEveryIdleOpportunityIsDueWithTheDefaultDelay(): void
    {
        $now = new \DateTimeImmutable('2026-11-12 06:00:00', new \DateTimeZone('UTC'));
        $cutoff = Reminders::cutoff($now, Settings::DEFAULT_DAYS);
        $changes = IdleOpportunities::lastChanges($now);

        self::assertSame(array_keys(IdleOpportunities::DAYS), array_keys($changes));
        self::assertSame('2026-09-28 06:00:00', $changes['garnier-flotte']);
        foreach ($changes as $key => $date) {
            self::assertLessThan($cutoff, $date, "$key pas assez ancienne pour une relance");
        }
    }
}
