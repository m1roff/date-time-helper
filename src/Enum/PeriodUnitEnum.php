<?php

declare(strict_types=1);

namespace M1roff\DateTimeHelper\Enum;

enum PeriodUnitEnum: string
{
    case YEAR = 'year';
    case QUARTER = 'quarter';
    case MONTH = 'month';
    case WEEK = 'week';
}
