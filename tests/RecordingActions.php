<?php

declare(strict_types=1);

namespace SuiteCrmExt\OpportunityReminders\Tests;

use SuiteCrmExt\OpportunityReminders\PageActions;

/** What the admin page asked: the days saved, the runs (3 tasks each). */
final class RecordingActions implements PageActions
{
    /** @var list<int> */
    public array $saved = [];
    public int $runs = 0;

    public function save(int $days): void
    {
        $this->saved[] = $days;
    }

    public function run(): int
    {
        ++$this->runs;

        return 3;
    }
}
