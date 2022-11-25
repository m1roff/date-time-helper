<?php

declare(strict_types=1);

namespace M1roff\DateTimeHelper\PeriodUnit;

use DateTimeImmutable;
use DateTimeInterface;
use M1roff\DateTimeHelper\PeriodUnitHandlerInterface;

class WeekStartHandler implements PeriodUnitHandlerInterface
{
    public function handle(DateTimeInterface $date): DateTimeImmutable
    {
        return DateTimeImmutable::createFromInterface($date)
            ->modify(sprintf(
                'monday %s week',
                $date->format('w') === '0' ? 'last' : 'this'
            ))
            ->setTime(0, 0, 0);
    }
}
