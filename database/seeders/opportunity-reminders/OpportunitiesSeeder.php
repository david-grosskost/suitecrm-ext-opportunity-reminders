<?php

declare(strict_types=1);

namespace SuiteCrmExt\OpportunityReminders\Seed;

use SuiteCrmCore\Seed\SeedContext;
use SuiteCrmCore\Seed\Seeder;

/**
 * A few open opportunities of core/opportunities left untouched for weeks
 * (IdleOpportunities), so that a demo has reminders to create
 * (Administration → Relances des opportunités → Enregistrer et lancer
 * maintenant) without waiting a month. Saved with update_date_modified off:
 * SuiteCRM keeps the date given instead of setting it to now, and the seed
 * counts the records.
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
        foreach (IdleOpportunities::lastChanges(new \DateTimeImmutable('now', new \DateTimeZone('UTC'))) as $key => $date) {
            $bean = \BeanFactory::getBean('Opportunities', SeedContext::id('Opportunities', $key));
            if (!$bean instanceof \SugarBean || empty($bean->id)) {
                continue;
            }
            $bean->update_date_modified = false;
            $seed->update($bean, ['date_modified' => $date]);
        }
    }
}
