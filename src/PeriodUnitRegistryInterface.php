<?php

declare(strict_types=1);

namespace M1roff\DateTimeHelper;

use M1roff\DateTimeHelper\Enum\PeriodUnitEnum;

interface PeriodUnitRegistryInterface
{
    public function get(PeriodUnitEnum $periodUnit): PeriodUnitHandlerInterface;
}
