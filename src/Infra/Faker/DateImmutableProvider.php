<?php

namespace App\Infra\Faker;

use Faker\Provider\Base as BaseProvider;

final class DateImmutableProvider extends BaseProvider
{
    public static function dateTimeImmutableThisYear(\DateTimeImmutable|int|string $max = 'now'): \DateTimeImmutable
    {
        return self::dateTimeBetween('first day of january this year', $max);
    }

    public static function dateTimeBetween(\DateTimeImmutable|string $startDate = '-30 years', \DateTimeImmutable|int|string $endDate = 'now'): \DateTimeImmutable
    {
        $startTimestamp = $startDate instanceof \DateTimeImmutable ? $startDate->getTimestamp() : (int) strtotime($startDate);
        $endTimestamp = self::getMaxTimestamp($endDate);

        if ($startTimestamp > $endTimestamp) {
            throw new \InvalidArgumentException('Start date must be anterior to end date.');
        }

        $timestamp = self::numberBetween($startTimestamp, $endTimestamp);

        return new \DateTimeImmutable("@{$timestamp}");
    }

    private static function getMaxTimestamp(\DateTimeImmutable|float|int|string $max = 'now'): int
    {
        if (is_numeric($max)) {
            return (int) $max;
        }

        if ($max instanceof \DateTimeImmutable) {
            return $max->getTimestamp();
        }

        return (int) strtotime('' === $max || '0' === $max ? 'now' : $max);
    }
}
