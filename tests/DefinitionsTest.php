<?php

declare(strict_types=1);

namespace SuiteCrmExt\OpportunityReminders\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use SuiteCrmExt\OpportunityReminders\Page;
use SuiteCrmExt\OpportunityReminders\SchedulerRecord;

/**
 * The definitions the extension gives SuiteCRM, read as SuiteCRM reads them:
 * each file included with the array it fills.
 */
final class DefinitionsTest extends TestCase
{
    private const string LEGACY = __DIR__ . '/../legacy/Extension';
    private const array ADMIN_LABELS = ['LBL_OR_ADMIN_HEADER', 'LBL_OR_ADMIN_HEADER_DESC', 'LBL_OR_ADMIN_DESC'];
    private const array TASK_LABELS = ['LBL_OR_TASK_NAME', 'LBL_OR_TASK_DESCRIPTION'];

    /**
     * The array a definition file fills.
     *
     * @return array<mixed>
     */
    private static function load(string $file, string $variable): array
    {
        $job_strings = $mod_strings = $app_strings = $action_file_map = $admin_group_header = [];
        require self::LEGACY . '/' . $file;
        $values = compact('job_strings', 'mod_strings', 'app_strings', 'action_file_map', 'admin_group_header');

        return is_array($values[$variable] ?? null) ? $values[$variable] : [];
    }

    public function testTheJobIsOfferedToTheSchedulers(): void
    {
        $jobs = self::load('modules/Schedulers/Ext/ScheduledTasks/opportunity-reminders.php', 'job_strings');

        self::assertSame([SchedulerRecord::FUNCTION], $jobs);
        self::assertTrue(function_exists(SchedulerRecord::FUNCTION));
    }

    public function testTheAdminPageIsMappedToItsFile(): void
    {
        $map = self::load('modules/Administration/Ext/ActionFileMap/opportunity-reminders.php', 'action_file_map');

        self::assertSame(['opportunityreminders' => 'custom/include/opportunity-reminders/SettingsPage.php'], $map);
        self::assertSame('opportunityreminders', strtolower(Page::ACTION));
        self::assertFileExists(__DIR__ . '/../legacy/include/opportunity-reminders/SettingsPage.php');
    }

    public function testThePageTemplateIsWhereTheViewLooksForIt(): void
    {
        // PageView::TEMPLATE, not loaded here (it needs SuiteCRM's SugarView).
        $view = (string) file_get_contents(__DIR__ . '/../legacy/include/opportunity-reminders/PageView.php');

        self::assertStringContainsString("TEMPLATE = 'custom/include/opportunity-reminders/page.tpl'", $view);
        self::assertFileExists(__DIR__ . '/../legacy/include/opportunity-reminders/page.tpl');
    }

    public function testTheAdministrationPageLinksToIt(): void
    {
        $groups = self::load('modules/Administration/Ext/Administration/opportunity-reminders.php', 'admin_group_header');

        self::assertSame([[
            'LBL_OR_ADMIN_HEADER',
            '',
            false,
            ['Administration' => ['opportunity_reminders' => [
                'Schedulers',
                'LBL_OR_ADMIN_TITLE',
                'LBL_OR_ADMIN_DESC',
                './index.php?module=Administration&action=' . Page::ACTION,
                'admin-scheduler',
            ]]],
            'LBL_OR_ADMIN_HEADER_DESC',
            'module_settings',
        ]], $groups);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function languages(): iterable
    {
        yield 'français' => ['fr_FR'];
        yield 'anglais' => ['en_us'];
    }

    #[DataProvider('languages')]
    public function testEveryTextIsTranslated(string $language): void
    {
        $file = $language . '.opportunity-reminders.php';
        $expected = [
            'modules/Schedulers/Ext/Language/' => ['mod_strings', ['LBL_' . strtoupper(SchedulerRecord::FUNCTION)]],
            'modules/Administration/Ext/Language/' => ['mod_strings', [...Page::LABELS, ...self::ADMIN_LABELS]],
            'application/Ext/Language/' => ['app_strings', self::TASK_LABELS],
        ];

        foreach ($expected as $dir => [$variable, $keys]) {
            $labels = self::load($dir . $file, $variable);
            foreach ($keys as $key) {
                self::assertNotEmpty($labels[$key] ?? null, "$key manquant dans $dir$file");
            }
        }
    }

    #[DataProvider('languages')]
    public function testTheTaskTextsHaveTheirPlaceholders(string $language): void
    {
        $labels = self::load('application/Ext/Language/' . $language . '.opportunity-reminders.php', 'app_strings');

        $name = $labels['LBL_OR_TASK_NAME'] ?? null;
        $description = $labels['LBL_OR_TASK_DESCRIPTION'] ?? null;
        self::assertIsString($name);
        self::assertIsString($description);
        self::assertStringContainsString('%s', $name);
        self::assertStringContainsString('%d', $description);
    }
}
