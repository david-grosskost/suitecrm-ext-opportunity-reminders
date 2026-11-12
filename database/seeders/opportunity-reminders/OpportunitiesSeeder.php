<?php

declare(strict_types=1);

namespace SuiteCrmExt\OpportunityReminders\Seed;

use SuiteCrmCore\Seed\SeedContext;
use SuiteCrmCore\Seed\Seeder;

/**
 * A few open opportunities of core/opportunities left untouched for weeks
 * (IdleOpportunities), so that a demo has reminders to create
 * (Administration → Relances des opportunités → Enregistrer et lancer
 * maintenant) without waiting a month. Their last change is set straight in
 * the database: saving the record would set it to now.
 */
final class OpportunitiesSeeder implements Seeder
{
    public function name(): string
    {
        return 'opportunity-reminders/opportunities';
    }

    public function dependsOn(): array
    {
        return ['core/opportunities'];
    }

    public function run(SeedContext $seed): void
    {
        $db = \DBManagerFactory::getInstance();
        foreach (IdleOpportunities::lastChanges(new \DateTimeImmutable('now', new \DateTimeZone('UTC'))) as $key => $date) {
            $db->query(IdleOpportunities::query($db->quoted(SeedContext::id('Opportunities', $key)), $db->quoted($date)));
        }
    }
}
