<?php

declare(strict_types=1);

namespace M1roff\DateTimeHelper;

use DateTimeImmutable;
use DateTimeInterface;

interface PeriodUnitHandlerInterface
{
    public function handle(DateTimeInterface $date): DateTimeImmutable;
}
