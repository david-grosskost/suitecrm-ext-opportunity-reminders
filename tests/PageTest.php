<?php

declare(strict_types=1);

namespace SuiteCrmExt\OpportunityReminders\Tests;

use PHPUnit\Framework\TestCase;
use SuiteCrmExt\OpportunityReminders\Page;

final class PageTest extends TestCase
{
    private const array LABELS = [
        'LBL_OR_SAVED' => 'Réglage enregistré : relance après %d jours.',
        'LBL_OR_RAN' => 'Relances lancées : %d tâche(s) créée(s).',
        'LBL_OR_DAYS_INVALID' => 'Nombre de jours invalide.',
        'LBL_OR_DAYS' => 'Relancer après',
        'LBL_OR_SAVE' => 'Enregistrer',
        'LBL_OR_RUN' => 'Enregistrer et lancer maintenant',
    ];

    private RecordingActions $actions;

    protected function setUp(): void
    {
        $this->actions = new RecordingActions();
    }

    /**
     * @param array<string, string> $post
     *
     * @return array{days: string, messages: list<array{string, string}>}
     */
    private function handle(array $post): array
    {
        return Page::handle(
            $post,
            30,
            $this->actions,
            self::LABELS,
        );
    }

    public function testDisplayingThePageChangesNothing(): void
    {
        self::assertSame(['days' => '30', 'messages' => []], $this->handle([]));
        self::assertSame([], $this->actions->saved);
        self::assertSame(0, $this->actions->runs);
    }

    public function testSaveKeepsTheDays(): void
    {
        $state = $this->handle(['or_days' => '45', 'or_save' => 'Enregistrer']);

        self::assertSame([45], $this->actions->saved);
        self::assertSame(0, $this->actions->runs);
        self::assertSame(['days' => '45', 'messages' => [['notice', 'Réglage enregistré : relance après 45 jours.']]], $state);
    }

    public function testRunNowSavesThenCreatesTheReminders(): void
    {
        $state = $this->handle(['or_days' => '10', 'or_run' => 'Lancer']);

        self::assertSame([10], $this->actions->saved);
        self::assertSame(1, $this->actions->runs);
        self::assertSame(['notice', 'Relances lancées : 3 tâche(s) créée(s).'], $state['messages'][1] ?? null);
    }

    public function testWrongDaysAreRefusedAndNothingRuns(): void
    {
        $state = $this->handle(['or_days' => '0', 'or_run' => 'Lancer']);

        self::assertSame([], $this->actions->saved);
        self::assertSame(0, $this->actions->runs);
        self::assertSame(['days' => '0', 'messages' => [['error', 'Nombre de jours invalide.']]], $state);
    }

    public function testTheFormPostsToThePageAndEscapesWhatItShows(): void
    {
        $html = Page::html('30"><script>', [['error', '<b>non</b>']], self::LABELS);

        self::assertStringContainsString('action="index.php?module=Administration&amp;action=OpportunityReminders"', $html);
        self::assertStringContainsString('name="or_days" min="1" max="365"', $html);
        self::assertStringContainsString('value="30&quot;&gt;&lt;script&gt;"', $html);
        self::assertStringContainsString('<p class="error">&lt;b&gt;non&lt;/b&gt;</p>', $html);
        self::assertStringContainsString('name="or_run" value="Enregistrer et lancer maintenant"', $html);
        self::assertStringNotContainsString('<script>', $html);
    }

    public function testAMissingLabelShowsItsKey(): void
    {
        self::assertSame('LBL_OR_PAGE_INTRO', Page::label(self::LABELS, 'LBL_OR_PAGE_INTRO'));
    }
}
