<?php

// Seeders of opportunity-reminders, found by the seed runner of suitecrm-core
// when the extension is active (make db@seed), after those of the core.

declare(strict_types=1);

use SuiteCrmExt\OpportunityReminders\Seed\OpportunitiesSeeder;

require_once __DIR__ . '/IdleOpportunities.php';
require_once __DIR__ . '/OpportunitiesSeeder.php';

return [
    new OpportunitiesSeeder(),
];
