<?php

declare(strict_types=1);

namespace M1roff\DateTimeHelper;

use BadMethodCallException;
use DateTimeImmutable;
use DateTimeInterface;
use M1roff\DateTimeHelper\Enum\PeriodUnitEnum;
use M1roff\DateTimeHelper\PeriodUnit\MonthEndHandler;
use M1roff\DateTimeHelper\PeriodUnit\MonthStartHandler;
use M1roff\DateTimeHelper\PeriodUnit\QuarterEndHandler;
use M1roff\DateTimeHelper\PeriodUnit\QuarterStartHandler;
use M1roff\DateTimeHelper\PeriodUnit\WeekEndHandler;
use M1roff\DateTimeHelper\PeriodUnit\WeekStartHandler;
use M1roff\DateTimeHelper\PeriodUnit\YearEndHandler;
use M1roff\DateTimeHelper\PeriodUnit\YearStartHandler;
use M1roff\DateTimeHelper\Registry\PeriodUnitRegistry;
use UnitEnum;

/**
 * @method DateTimeImmutable startOfYear
 * @method DateTimeImmutable startOfMonth
 * @method DateTimeImmutable startOfWeek
 * @method DateTimeImmutable startOfQuarter
 * @method DateTimeImmutable endOfYear
 * @method DateTimeImmutable endOfMonth
 * @method DateTimeImmutable endOfWeek
 * @method DateTimeImmutable endOfQuarter
 */
class DateTimeHelper
{
    public const FORMAT_QUARTER = 'Q';

    public const FORMAT_QUARTER_ROMAN = 'QR';

    private const START_OF = 'startOf';

    private const END_OF = 'endOf';

    private ?PeriodUnitRegistryInterface $start = null;

    private ?PeriodUnitRegistryInterface $end = null;

    private string $periodUnitEnumClass = PeriodUnitEnum::class;

    private DateTimeInterface $date;

    public function __construct(DateTimeInterface|string $date = 'now')
    {
        if (!$date instanceof DateTimeInterface) {
            $this->date = new DateTimeImmutable($date);
        } else {
            $this->date = $date;
        }
    }

    public function __call(string $name, array $args): mixed
    {
        [$type, $unit] = $this->initializePeriodTypeAndUnit($name);

        if (null !== $type && null !== $unit) {
            $this->initializeRegistries();
            $unitEnum = call_user_func([$this->periodUnitEnumClass, 'from'], $unit);

            return match ($type) {
                self::START_OF => $this->startOf($unitEnum, ...$args),
                self::END_OF => $this->endOf($unitEnum, ...$args),
            };
        }

        throw new BadMethodCallException(sprintf('Method "%s" does not exists.', $name));
    }

    public function format(string $format): string
    {
        return $this->date->format($this->preFormatWithQuarter($format));
    }

    public function setPeriodUnitEnumClass(string $periodUnitEnumClass): DateTimeHelper
    {
        $this->periodUnitEnumClass = $periodUnitEnumClass;

        return $this;
    }

    public function setStartPeriodRegistry(PeriodUnitRegistryInterface $start): DateTimeHelper
    {
        $this->start = $start;

        return $this;
    }

    public function setEndPeriodRegistry(PeriodUnitRegistryInterface $end): DateTimeHelper
    {
        $this->end = $end;

        return $this;
    }

    protected function startOf(UnitEnum $periodUnit): DateTimeImmutable
    {
        return $this->start->get($periodUnit)->handle($this->date);
    }

    protected function endOf(UnitEnum $periodUnit): DateTimeImmutable
    {
        return $this->end->get($periodUnit)->handle($this->date);
    }

    private function initializePeriodTypeAndUnit(string $methodName): array
    {
        if (str_starts_with($methodName, self::START_OF)) {
            return $this->pullOutUnitForType(self::START_OF, $methodName);
        }

        if (str_starts_with($methodName, self::END_OF)) {
            return $this->pullOutUnitForType(self::END_OF, $methodName);
        }

        return [null, null];
    }

    private function pullOutUnitForType(string $type, string $methodName): array
    {
        return [
            $type,
            strtolower(substr($methodName, strlen($type))),
        ];
    }

    /**
     * @todo Get rid of. Use DI.
     */
    private function initializeRegistries(): void
    {
        if (null === $this->start) {
            $this->start = (new PeriodUnitRegistry())
                ->add(PeriodUnitEnum::YEAR, new YearStartHandler())
                ->add(PeriodUnitEnum::MONTH, new MonthStartHandler())
                ->add(PeriodUnitEnum::WEEK, new WeekStartHandler())
                ->add(PeriodUnitEnum::QUARTER, new QuarterStartHandler());
        }
        if (null === $this->end) {
            $this->end = (new PeriodUnitRegistry())
                ->add(PeriodUnitEnum::YEAR, new YearEndHandler())
                ->add(PeriodUnitEnum::MONTH, new MonthEndHandler())
                ->add(PeriodUnitEnum::WEEK, new WeekEndHandler())
                ->add(PeriodUnitEnum::QUARTER, new QuarterEndHandler());
        }
    }

    private function preFormatWithQuarter(string $format)
    {
        if (str_contains($format, self::FORMAT_QUARTER) || str_contains($format, self::FORMAT_QUARTER_ROMAN)) {
            $format = strtr($format, [
                self::FORMAT_QUARTER => $this->getFormattedQuarter(),
                self::FORMAT_QUARTER_ROMAN => $this->getFormattedQuarterRoman(),
            ]);
        }

        return $format;
    }

    private function getFormattedQuarter(): int
    {
        return (int) ceil(((int) $this->date->format('n')) / 3);
    }

    private function getFormattedQuarterRoman(): string
    {
        return match ($this->getFormattedQuarter()) {
            1 => '\I',
            2 => '\I\I',
            3 => '\I\I\I',
            4 => '\I\V',
        };
    }
}
