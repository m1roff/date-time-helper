<?php

declare(strict_types=1);

namespace M1roff\DateTimeHelper\Test;

use BadMethodCallException;
use DateTime;
use DateTimeImmutable;
use M1roff\DateTimeHelper\DateTimeHelper;
use M1roff\DateTimeHelper\Exception\PeriodUnitNotDefinedException;
use M1roff\DateTimeHelper\Registry\PeriodUnitRegistry;
use M1roff\DateTimeHelper\Test\Support\HelperObjects\FakeEndHandler;
use M1roff\DateTimeHelper\Test\Support\HelperObjects\FakeStartHandler;
use M1roff\DateTimeHelper\Test\Support\HelperObjects\OneMorePeriodUnitEnum;
use PHPUnit\Framework\TestCase;

final class DateTimeHelperTest extends TestCase
{
    private const DATE_TIME_FORMAT = 'Y-m-d H:i:s';

    /**
     * @dataProvider startOfMethodsDataProvider
     */
    public function testStartOfMethods(
        mixed $date,
        string $expectedStarOfYear,
        string $expectedStarOfQuarter,
        string $expectedStarOfMonth,
        string $expectedStarOfWeek,
    ): void {
        $dateTimeHelper = new DateTimeHelper($date);

        $this->assertEquals($dateTimeHelper->startOfYear()->format(self::DATE_TIME_FORMAT), $expectedStarOfYear);
        $this->assertEquals($dateTimeHelper->startOfQuarter()->format(self::DATE_TIME_FORMAT), $expectedStarOfQuarter);
        $this->assertEquals($dateTimeHelper->startOfMonth()->format(self::DATE_TIME_FORMAT), $expectedStarOfMonth);
        $this->assertEquals($dateTimeHelper->startOfWeek()->format(self::DATE_TIME_FORMAT), $expectedStarOfWeek);
    }

    /**
     * @dataProvider endOfMethodsDataProvider
     */
    public function testEndOfMethods(
        mixed $date,
        string $expectedEndOfYear,
        string $expectedEndOfQuarter,
        string $expectedEndOfMonth,
        string $expectedEndOfWeek,
    ): void {
        $dateTimeHelper = new DateTimeHelper($date);

        $this->assertEquals($dateTimeHelper->endOfYear()->format(self::DATE_TIME_FORMAT), $expectedEndOfYear);
        $this->assertEquals($dateTimeHelper->endOfQuarter()->format(self::DATE_TIME_FORMAT), $expectedEndOfQuarter);
        $this->assertEquals($dateTimeHelper->endOfMonth()->format(self::DATE_TIME_FORMAT), $expectedEndOfMonth);
        $this->assertEquals($dateTimeHelper->endOfWeek()->format(self::DATE_TIME_FORMAT), $expectedEndOfWeek);
    }

    public function testFormatOutput(): void
    {
        $date = new DateTimeHelper('2022-11-31 15:34:31');

        $this->assertEquals(
            $date->format(sprintf('H:i:s : d.m.Y : %s ; %s | q q\r R', DateTimeHelper::FORMAT_QUARTER, DateTimeHelper::FORMAT_QUARTER_ROMAN)),
            '15:34:31 : 01.12.2022 : 4 ; IV | q qr R'
        );
    }

    /**
     * @dataProvider formatRomanOutputDataProvider
     */
    public function testFormatRomanOutput(string $date, string $romanNumber): void
    {
        $this->assertEquals(
            (new DateTimeHelper($date))->format(DateTimeHelper::FORMAT_QUARTER_ROMAN),
            $romanNumber,
        );
    }

    public function testBadMethodCallException(): void
    {
        $this->expectExceptionObject(new BadMethodCallException('Method "startWithNoExistingMethod" does not exists.'));

        (new DateTimeHelper())->startWithNoExistingMethod();
    }

    public function testSetters(): void
    {
        $helper = (new DateTimeHelper(new DateTime('29.01.2022 - 1 year')))
            ->setPeriodUnitEnumClass(OneMorePeriodUnitEnum::class)
            ->setStartPeriodRegistry(
                (new PeriodUnitRegistry())->add(OneMorePeriodUnitEnum::DAYDAY, new FakeStartHandler())
            )
            ->setEndPeriodRegistry(
                (new PeriodUnitRegistry())->add(OneMorePeriodUnitEnum::DAYDAY, new FakeEndHandler())
            );

        $this->assertInstanceOf(DateTimeImmutable::class, $helper->startOfDayday());
        $this->assertEquals('2022', $helper->startOfDayday()->format('Y'));
        $this->assertEquals('2020', $helper->endOfDayday()->format('Y'));

        $this->expectExceptionObject(new PeriodUnitNotDefinedException(OneMorePeriodUnitEnum::WEEK));
        $helper->startOfWeek();
    }

    protected function formatRomanOutputDataProvider(): array
    {
        return [
            '1st quarter' => ['25.02.2022', 'I'],
            '2st quarter' => ['25.05.2022', 'II'],
            '3st quarter' => ['25.07.2022', 'III'],
            '4st quarter' => ['25.10.2022', 'IV'],
        ];
    }

    protected function endOfMethodsDataProvider(): array
    {
        return [
            'test time from string' => [
                'date' => '2022-05-11 13:30:34',
                'endOfYear' => '2022-12-31 23:59:59',
                'endOfQuarter' => '2022-06-30 23:59:59',
                'endOfMonth' => '2022-05-31 23:59:59',
                'endOfWeek' => '2022-05-15 23:59:59',
            ],
            'test time from object mutable' => [
                'date' => new \DateTime('2022-08-31 13:30:34'),
                'endOfYear' => '2022-12-31 23:59:59',
                'endOfQuarter' => '2022-09-30 23:59:59',
                'endOfMonth' => '2022-08-31 23:59:59',
                'endOfWeek' => '2022-09-04 23:59:59',
            ],
            'test time from object immutable' => [
                'date' => new \DateTimeImmutable('2022-12-31 13:30:34'),
                'endOfYear' => '2022-12-31 23:59:59',
                'endOfQuarter' => '2022-12-31 23:59:59',
                'endOfMonth' => '2022-12-31 23:59:59',
                'endOfWeek' => '2023-01-01 23:59:59',
            ],
        ];
    }

    protected function startOfMethodsDataProvider(): array
    {
        return [
            'test time from string' => [
                'date' => '2022-05-11 13:30:34',
                'startOfYear' => '2022-01-01 00:00:00',
                'startOfQuarter' => '2022-04-01 00:00:00',
                'startOfMonth' => '2022-05-01 00:00:00',
                'startOfWeek' => '2022-05-09 00:00:00',
            ],
            'test time from object mutable' => [
                'date' => new \DateTime('2022-08-31 13:30:34'),
                'startOfYear' => '2022-01-01 00:00:00',
                'startOfQuarter' => '2022-07-01 00:00:00',
                'startOfMonth' => '2022-08-01 00:00:00',
                'startOfWeek' => '2022-08-29 00:00:00',
            ],
            'test time from object immutable' => [
                'date' => new \DateTimeImmutable('2022-12-31 13:30:34'),
                'startOfYear' => '2022-01-01 00:00:00',
                'startOfQuarter' => '2022-10-01 00:00:00',
                'startOfMonth' => '2022-12-01 00:00:00',
                'startOfWeek' => '2022-12-26 00:00:00',
            ],
        ];
    }
}
