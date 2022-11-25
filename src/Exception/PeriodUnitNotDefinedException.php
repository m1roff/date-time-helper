<?php

declare(strict_types=1);

namespace M1roff\DateTimeHelper\Exception;

use UnitEnum;

class PeriodUnitNotDefinedException extends DateTimeHelperException
{
    public function __construct(UnitEnum $periodUnit)
    {
        parent::__construct(sprintf('Period Unit "%s" handler not found (or not defined).', $periodUnit->value));
    }
}
