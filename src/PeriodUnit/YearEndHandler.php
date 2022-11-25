<?php

declare(strict_types=1);

namespace M1roff\DateTimeHelper\PeriodUnit;

use DateTimeImmutable;
use DateTimeInterface;
use M1roff\DateTimeHelper\PeriodUnitHandlerInterface;

class YearEndHandler implements PeriodUnitHandlerInterface
{
    public function handle(DateTimeInterface $date): DateTimeImmutable
    {
        return DateTimeImmutable::createFromInterface($date)
            ->modify(sprintf(
                'last day of december %s',
                $date->format('Y')
            ))
            ->setTime(23, 59, 59);
    }
}
