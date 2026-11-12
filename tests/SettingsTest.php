<?php

declare(strict_types=1);

namespace SuiteCrmExt\OpportunityReminders\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use SuiteCrmExt\OpportunityReminders\Settings;

final class SettingsTest extends TestCase
{
    /**
     * @return iterable<string, array{mixed, int|null}>
     */
    public static function typed(): iterable
    {
        yield 'trente jours' => ['30', 30];
        yield 'un jour, le minimum' => ['1', 1];
        yield 'un an, le maximum' => ['365', 365];
        yield 'avec des espaces' => [' 45 ', 45];
        yield 'un entier stocké' => [60, 60];
        yield 'zéro' => ['0', null];
        yield 'plus d\'un an' => ['366', null];
        yield 'négatif' => ['-5', null];
        yield 'décimal' => ['7.5', null];
        yield 'du texte' => ['trente', null];
        yield 'vide' => ['', null];
        yield 'rien' => [null, null];
        yield 'un tableau' => [['30'], null];
    }

    #[DataProvider('typed')]
    public function testOnlyAWholeNumberOfDaysInRangeIsAccepted(mixed $value, ?int $days): void
    {
        self::assertSame($days, Settings::parse($value));
    }

    public function testThirtyDaysWhenNothingValidIsStored(): void
    {
        self::assertSame(30, Settings::days(null));
        self::assertSame(30, Settings::days('n\'importe quoi'));
        self::assertSame(12, Settings::days('12'));
    }
}
