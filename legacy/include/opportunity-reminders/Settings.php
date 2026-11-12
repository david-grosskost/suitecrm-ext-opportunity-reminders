<?php

declare(strict_types=1);

namespace SuiteCrmExt\OpportunityReminders;

/**
 * The one setting of the extension: after how many days without a change an
 * open opportunity gets a reminder. Kept in SuiteCRM's settings table
 * (Administration, category opportunity_reminders), set on the admin page.
 */
final class Settings
{
    public const string CATEGORY = 'opportunity_reminders';
    public const string KEY = 'days';
    public const int DEFAULT_DAYS = 30;
    public const int MIN_DAYS = 1;
    public const int MAX_DAYS = 365;

    /** Days from what is stored: the default when nothing valid is. */
    public static function days(mixed $stored): int
    {
        return self::parse($stored) ?? self::DEFAULT_DAYS;
    }

    /** Days typed on the admin page, null when not a whole number in range. */
    public static function parse(mixed $value): ?int
    {
        if (is_int($value)) {
            $value = (string) $value;
        }
        if (!is_string($value) || preg_match('/^\d{1,3}$/', trim($value)) !== 1) {
            return null;
        }
        $days = (int) trim($value);

        return $days >= self::MIN_DAYS && $days <= self::MAX_DAYS ? $days : null;
    }

    /** The days set for this instance. */
    public static function read(): int
    {
        $admin = \BeanFactory::newBean('Administration');
        if (!$admin instanceof \Administration) {
            return self::DEFAULT_DAYS;
        }
        // true: read again from the database, not from the cache.
        $admin->retrieveSettings(self::CATEGORY, true);

        return self::days($admin->settings[self::CATEGORY . '_' . self::KEY] ?? null);
    }

    public static function save(int $days): void
    {
        $admin = \BeanFactory::newBean('Administration');
        if (!$admin instanceof \Administration) {
            throw new \RuntimeException('opportunity-reminders : réglages de SuiteCRM introuvables');
        }
        $admin->saveSetting(self::CATEGORY, self::KEY, $days);
    }
}
