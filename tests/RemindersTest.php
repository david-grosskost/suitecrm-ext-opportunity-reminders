<?php

declare(strict_types=1);

namespace SuiteCrmExt\OpportunityReminders\Tests;

use PHPUnit\Framework\TestCase;
use SuiteCrmExt\OpportunityReminders\Reminders;

final class RemindersTest extends TestCase
{
    private const array LABELS = [
        'name' => 'Relancer : %s',
        'description' => 'Aucune modification de l\'opportunité depuis %d jours.',
    ];

    private static function now(): \DateTimeImmutable
    {
        // 7:00 in Paris, 6:00 in the database (UTC).
        return new \DateTimeImmutable('2026-11-12 07:00:00', new \DateTimeZone('Europe/Paris'));
    }

    public function testTheCutoffIsTheSetNumberOfDaysAgoInUtc(): void
    {
        self::assertSame('2026-10-13 06:00:00', Reminders::cutoff(self::now(), 30));
        self::assertSame('2026-11-11 06:00:00', Reminders::cutoff(self::now(), 1));
    }

    public function testOnlyOpenIdleOpportunitiesWithoutATaskInProgressAreDue(): void
    {
        $query = Reminders::query("'2026-10-13 06:00:00'");

        self::assertStringContainsString('o.deleted = 0', $query);
        self::assertStringContainsString("o.sales_stage NOT IN ('Closed Won', 'Closed Lost')", $query);
        self::assertStringContainsString("o.date_modified < '2026-10-13 06:00:00'", $query);
        // A task not completed, or completed within the delay, holds the reminder back.
        self::assertStringContainsString("t.parent_type = 'Opportunities' AND t.parent_id = o.id", $query);
        self::assertStringContainsString("t.status <> 'Completed' OR t.date_modified >= '2026-10-13 06:00:00'", $query);
    }

    public function testTheTaskGoesToTheSalesRepOfTheOpportunity(): void
    {
        $task = Reminders::task(
            ['id' => 'opp-1', 'name' => 'Devis et planning de pose', 'assigned_user_id' => 'user-7'],
            30,
            self::LABELS,
            self::now(),
        );

        self::assertSame([
            'name' => 'Relancer : Devis et planning de pose',
            'description' => 'Aucune modification de l\'opportunité depuis 30 jours.',
            'status' => 'Not Started',
            'priority' => 'Medium',
            'date_due' => '2026-11-12 06:00:00',
            'parent_type' => 'Opportunities',
            'parent_id' => 'opp-1',
            'assigned_user_id' => 'user-7',
        ], $task);
    }

    public function testALongNameIsCutToTheLengthOfATaskName(): void
    {
        $task = Reminders::task(
            ['id' => 'opp-2', 'name' => 'Pilotage des chantiers de raccordement en fibre optique', 'assigned_user_id' => ''],
            30,
            self::LABELS,
            self::now(),
        );

        self::assertSame(Reminders::NAME_LENGTH, mb_strlen($task['name']));
        self::assertStringEndsWith('…', $task['name']);
        self::assertSame('', $task['assigned_user_id']);
    }

    public function testAnApostropheStaysEncodedAsSuiteCrmStoresIt(): void
    {
        $task = Reminders::task(
            ['id' => 'opp-3', 'name' => 'Suivi des partenaires d&#039;essais', 'assigned_user_id' => '1'],
            30,
            self::LABELS,
            self::now(),
        );

        self::assertSame('Relancer : Suivi des partenaires d&#039;essais', $task['name']);
    }
}
