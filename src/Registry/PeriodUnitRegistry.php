<?php

declare(strict_types=1);

namespace M1roff\DateTimeHelper\Registry;

use M1roff\DateTimeHelper\Exception\PeriodUnitNotDefinedException;
use M1roff\DateTimeHelper\PeriodUnitHandlerInterface;
use M1roff\DateTimeHelper\PeriodUnitRegistryInterface;
use UnitEnum;

class PeriodUnitRegistry implements PeriodUnitRegistryInterface
{
    private array $units = [];

    public function add(UnitEnum $periodUnit, PeriodUnitHandlerInterface $handler): self
    {
        $this->units[$periodUnit->value] = $handler;

        return $this;
    }

    public function get(UnitEnum $periodUnit): PeriodUnitHandlerInterface
    {
        if (!array_key_exists($periodUnit->value, $this->units)) {
            throw new PeriodUnitNotDefinedException($periodUnit);
        }

        return $this->units[$periodUnit->value];
    }
}
