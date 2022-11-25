<?php

declare(strict_types=1);

namespace M1roff\DateTimeHelper\PeriodUnit;

use DateTimeImmutable;
use DateTimeInterface;
use M1roff\DateTimeHelper\PeriodUnitHandlerInterface;

class WeekEndHandler implements PeriodUnitHandlerInterface
{
    public function handle(DateTimeInterface $date): DateTimeImmutable
    {
        return DateTimeImmutable::createFromInterface($date)
            ->modify($date->format('w') === '0' ? 'now' : 'sunday this week')
            ->setTime(23, 59, 59);
    }
}
