<?php

declare(strict_types=1);

namespace SuiteCrmExt\OpportunityReminders;

/**
 * The admin page (Administration → Relances des opportunités): the number of
 * days, and a button that runs the reminders at once (useful in a demo, not
 * to wait for 7:00). What it does and shows, without SuiteCRM: SettingsPage.php
 * plugs it in.
 */
final class Page
{
    /** Legacy action of the page, in the Administration module (ActionFileMap). */
    public const string ACTION = 'OpportunityReminders';

    /** Texts of the page, in the Administration labels. */
    public const array LABELS = [
        'LBL_OR_ADMIN_TITLE',
        'LBL_OR_PAGE_INTRO',
        'LBL_OR_DAYS',
        'LBL_OR_DAYS_HELP',
        'LBL_OR_SAVE',
        'LBL_OR_RUN',
        'LBL_OR_SAVED',
        'LBL_OR_RAN',
        'LBL_OR_DAYS_INVALID',
    ];

    /**
     * What a request does: nothing on display, the days saved on "Enregistrer",
     * saved then the reminders run on "Lancer maintenant".
     *
     * @param array<array-key, mixed> $post
     * @param array<array-key, mixed> $labels
     *
     * @return array{days: string, messages: list<array{string, string}>} Days to show (as typed when wrong), messages (type, text)
     */
    public static function handle(array $post, int $stored, PageActions $actions, array $labels): array
    {
        $saving = isset($post['or_save']);
        $running = isset($post['or_run']);
        if (!$saving && !$running) {
            return ['days' => (string) $stored, 'messages' => []];
        }

        $typed = $post['or_days'] ?? null;
        $days = Settings::parse($typed);
        if ($days === null) {
            return [
                'days' => is_scalar($typed) ? (string) $typed : '',
                'messages' => [['error', self::label($labels, 'LBL_OR_DAYS_INVALID')]],
            ];
        }

        $actions->save($days);
        $messages = [['notice', sprintf(self::label($labels, 'LBL_OR_SAVED'), $days)]];
        if ($running) {
            $messages[] = ['notice', sprintf(self::label($labels, 'LBL_OR_RAN'), $actions->run())];
        }

        return ['days' => (string) $days, 'messages' => $messages];
    }

    /**
     * The form, in SuiteCRM's legacy styles (edit view, buttons).
     *
     * @param list<array{string, string}> $messages
     * @param array<array-key, mixed> $labels
     */
    public static function html(string $days, array $messages, array $labels): string
    {
        $html = '';
        foreach ($messages as [$type, $text]) {
            $class = $type === 'error' ? 'error' : 'msg';
            $html .= "<p class=\"{$class}\">" . self::escape($text) . "</p>\n";
        }

        $action = self::ACTION;
        $min = Settings::MIN_DAYS;
        $max = Settings::MAX_DAYS;
        // Texts escaped beforehand, not by closures in the heredoc: the scanner
        // of the Module Loader refuses the calls of a variable ($l()).
        $value = self::escape($days);
        $intro = self::text($labels, 'LBL_OR_PAGE_INTRO');
        $daysLabel = self::text($labels, 'LBL_OR_DAYS');
        $help = self::text($labels, 'LBL_OR_DAYS_HELP');
        $saveLabel = self::text($labels, 'LBL_OR_SAVE');
        $runLabel = self::text($labels, 'LBL_OR_RUN');

        return $html . <<<HTML
            <p>{$intro}</p>
            <form method="post" action="index.php?module=Administration&amp;action={$action}">
                <input type="hidden" name="module" value="Administration">
                <input type="hidden" name="action" value="{$action}">
                <table class="edit view" width="100%" cellspacing="1" cellpadding="0" border="0">
                    <tr>
                        <td scope="row" width="25%"><label for="or_days">{$daysLabel}</label></td>
                        <td>
                            <input type="number" id="or_days" name="or_days" min="{$min}" max="{$max}" step="1" required value="{$value}">
                            <span>{$help}</span>
                        </td>
                    </tr>
                </table>
                <input type="submit" class="button primary" name="or_save" value="{$saveLabel}">
                <input type="submit" class="button" name="or_run" value="{$runLabel}">
            </form>
            HTML;
    }

    private static function escape(string $text): string
    {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }

    /** @param array<array-key, mixed> $labels */
    private static function text(array $labels, string $key): string
    {
        return self::escape(self::label($labels, $key));
    }

    /** @param array<array-key, mixed> $labels */
    public static function label(array $labels, string $key): string
    {
        $value = $labels[$key] ?? null;

        return is_string($value) && $value !== '' ? $value : $key;
    }
}
