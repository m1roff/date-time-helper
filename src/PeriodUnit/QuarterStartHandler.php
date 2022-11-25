<?php

declare(strict_types=1);

namespace M1roff\DateTimeHelper\PeriodUnit;

use DateTimeImmutable;
use DateTimeInterface;
use M1roff\DateTimeHelper\PeriodUnitHandlerInterface;

class QuarterStartHandler implements PeriodUnitHandlerInterface
{
    public function handle(DateTimeInterface $date): DateTimeImmutable
    {
        return DateTimeImmutable::createFromInterface($date)
            ->modify(sprintf(
                'first day of %s %s',
                $this->getQuarterMonth($date),
                $date->format('Y'),
            ))
            ->setTime(0, 0, 0);
    }

    private function getQuarterMonth(DateTimeInterface $date): string
    {
        $monthNumber = (int) $date->format('n');

        return match (true) {
            $monthNumber <= 3 => 'january',
            $monthNumber >= 4 && $monthNumber <= 6 => 'april',
            $monthNumber >= 7 && $monthNumber <= 9 => 'july',
            $monthNumber >= 10 => 'october',
        };
    }
}
