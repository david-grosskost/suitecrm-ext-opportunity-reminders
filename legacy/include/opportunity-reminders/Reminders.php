<?php

declare(strict_types=1);

namespace SuiteCrmExt\OpportunityReminders;

// SuiteCRM loads only this file for the scheduled job: the setting comes along.
require_once __DIR__ . '/Settings.php';

/**
 * The reminders: an open opportunity (neither won nor lost) whose record has
 * not changed for the set number of days gets a task for its sales rep.
 * Never two: no new task while one linked to the opportunity is not
 * completed, nor before as many days again once it is.
 */
final class Reminders
{
    /** Sales stages of a closed opportunity (sales_stage_dom). */
    public const array CLOSED = ['Closed Won', 'Closed Lost'];
    /** Length of a task's name (Tasks vardefs, name varchar 50). */
    public const int NAME_LENGTH = 50;

    /** English texts, when the instance's language lacks them. */
    private const array LABELS = [
        'LBL_OR_TASK_NAME' => 'Follow up: %s',
        'LBL_OR_TASK_DESCRIPTION' => 'No change to this opportunity for %d days: task created by the opportunity reminders.',
    ];

    /**
     * Creates the reminders due now. Run by the scheduled job each morning,
     * or by "Lancer maintenant" on the admin page.
     *
     * @return int Tasks created
     */
    public static function run(?\DateTimeImmutable $now = null): int
    {
        $now ??= new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $days = Settings::read();
        $db = \DBManagerFactory::getInstance();
        $result = $db->query(self::query($db->quoted(self::cutoff($now, $days))));
        $labels = self::labels();

        $created = 0;
        // false: values as stored (already encoded by SuiteCRM), saved as such.
        while (is_array($row = $db->fetchByAssoc($result, false))) {
            $task = \BeanFactory::newBean('Tasks');
            if (!$task instanceof \SugarBean) {
                throw new \RuntimeException('opportunity-reminders : module des tâches introuvable');
            }
            foreach (self::task($row, $days, $labels, $now) as $field => $value) {
                $task->{$field} = $value;
            }
            $task->save();
            ++$created;
        }

        return $created;
    }

    /** Last change before which an opportunity is due, in the database format (UTC). */
    public static function cutoff(\DateTimeImmutable $now, int $days): string
    {
        return $now->setTimezone(new \DateTimeZone('UTC'))->modify("-{$days} days")->format('Y-m-d H:i:s');
    }

    /** The open opportunities due, oldest change first. $cutoff is already quoted. */
    public static function query(string $cutoff): string
    {
        $closed = "'" . implode("', '", self::CLOSED) . "'";

        return 'SELECT o.id, o.name, o.assigned_user_id FROM opportunities o'
            . ' WHERE o.deleted = 0'
            . " AND (o.sales_stage IS NULL OR o.sales_stage NOT IN ({$closed}))"
            . " AND o.date_modified < {$cutoff}"
            . ' AND NOT EXISTS (SELECT 1 FROM tasks t'
            . " WHERE t.deleted = 0 AND t.parent_type = 'Opportunities' AND t.parent_id = o.id"
            . " AND (t.status IS NULL OR t.status <> 'Completed' OR t.date_modified >= {$cutoff}))"
            . ' ORDER BY o.date_modified';
    }

    /**
     * Fields of the reminder of an opportunity (a row of query()), in the
     * database formats. Texts with %s (its name) and %d (the days).
     *
     * @param array<array-key, mixed> $opportunity
     * @param array{name: string, description: string} $labels
     *
     * @return array<string, string>
     */
    public static function task(array $opportunity, int $days, array $labels, \DateTimeImmutable $now): array
    {
        // Stored names are HTML-encoded (d&#039;essais): cut the text, not an entity.
        $name = html_entity_decode(self::text($opportunity, 'name'), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $name = mb_strimwidth(sprintf($labels['name'], $name), 0, self::NAME_LENGTH, '…', 'UTF-8');

        return [
            'name' => htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
            'description' => sprintf($labels['description'], $days),
            'status' => 'Not Started',
            'priority' => 'Medium',
            'date_due' => $now->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d H:i:s'),
            'parent_type' => 'Opportunities',
            'parent_id' => self::text($opportunity, 'id'),
            'assigned_user_id' => self::text($opportunity, 'assigned_user_id'),
        ];
    }

    /**
     * Texts of the tasks in the instance's language.
     *
     * @return array{name: string, description: string}
     */
    private static function labels(): array
    {
        /** @var mixed $config */
        $config = $GLOBALS['sugar_config'] ?? [];
        $language = is_array($config) && is_string($config['default_language'] ?? null) ? $config['default_language'] : 'en_us';
        $strings = \return_application_language($language);

        $label = static function (string $key) use ($strings): string {
            $value = $strings[$key] ?? null;

            return is_string($value) && $value !== '' ? $value : self::LABELS[$key];
        };

        return ['name' => $label('LBL_OR_TASK_NAME'), 'description' => $label('LBL_OR_TASK_DESCRIPTION')];
    }

    /** @param array<array-key, mixed> $row */
    private static function text(array $row, string $field): string
    {
        $value = $row[$field] ?? '';

        return is_scalar($value) ? (string) $value : '';
    }
}
