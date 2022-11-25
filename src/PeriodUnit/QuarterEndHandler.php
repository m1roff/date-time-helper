<?php

declare(strict_types=1);

namespace M1roff\DateTimeHelper\PeriodUnit;

use DateTimeImmutable;
use DateTimeInterface;
use M1roff\DateTimeHelper\PeriodUnitHandlerInterface;

class QuarterEndHandler implements PeriodUnitHandlerInterface
{
    public function handle(DateTimeInterface $date): DateTimeImmutable
    {
        return DateTimeImmutable::createFromInterface($date)
            ->modify(sprintf(
                'last day of %s %s',
                $this->getQuarterMonth($date),
                $date->format('Y'),
            ))
            ->setTime(23, 59, 59);
    }

    private function getQuarterMonth(DateTimeInterface $date): string
    {
        $monthNumber = (int) $date->format('n');

        return match (true) {
            $monthNumber <= 3 => 'march',
            $monthNumber >= 4 && $monthNumber <= 6 => 'june',
            $monthNumber >= 7 && $monthNumber <= 9 => 'september',
            $monthNumber >= 10 => 'december',
        };
    }
}
