<?php

declare(strict_types=1);

namespace SuiteCrmExt\OpportunityReminders\Seed;

/**
 * Which open opportunities of core/opportunities the demo leaves untouched,
 * and since when: what OpportunitiesSeeder writes, without SuiteCRM.
 */
final class IdleOpportunities
{
    /** Key of the opportunity (core/opportunities) => days since its last change. */
    public const array DAYS = [
        'garnier-flotte' => 45,
        'roches-export' => 62,
        'cap-azur-groupes' => 35,
        'perrin-devis' => 90,
    ];

    /**
     * Last change of each idle opportunity, in the database format (UTC).
     *
     * @return array<string, string>
     */
    public static function lastChanges(\DateTimeImmutable $now): array
    {
        $dates = [];
        foreach (self::DAYS as $key => $days) {
            $dates[$key] = $now->setTimezone(new \DateTimeZone('UTC'))->modify("-{$days} days")->format('Y-m-d H:i:s');
        }

        return $dates;
    }
}
