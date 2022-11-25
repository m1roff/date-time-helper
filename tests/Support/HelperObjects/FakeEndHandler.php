<?php

declare(strict_types=1);

namespace M1roff\DateTimeHelper\Test\Support\HelperObjects;

use DateTimeImmutable;
use DateTimeInterface;
use M1roff\DateTimeHelper\PeriodUnitHandlerInterface;

class FakeEndHandler implements PeriodUnitHandlerInterface
{
    public function handle(DateTimeInterface $date): DateTimeImmutable
    {
        return DateTimeImmutable::createFromInterface($date)->modify('- 1 year');
    }
}
