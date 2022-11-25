<?php

declare(strict_types=1);

namespace M1roff\DateTimeHelper\PeriodUnit;

use DateTimeImmutable;
use DateTimeInterface;
use M1roff\DateTimeHelper\PeriodUnitHandlerInterface;

class MonthStartHandler implements PeriodUnitHandlerInterface
{
    public function handle(DateTimeInterface $date): DateTimeImmutable
    {
        return DateTimeImmutable::createFromInterface($date)
            ->modify('first day of this month')
            ->setTime(0, 0, 0);
    }
}
