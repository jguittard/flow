<?php

declare(strict_types=1);

namespace Flow\ETL\Tests\Unit\Row\Entry;

use Flow\ETL\Exception\InvalidArgumentException;
use Flow\ETL\Row\Entry\TimeEntry;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class TimeEntryTest extends TestCase
{
    public static function is_equal_data_provider() : \Generator
    {
        yield 'equal names and values' => [true, new TimeEntry('name', new \DateInterval('PT10S')), new TimeEntry('name', new \DateInterval('PT10S'))];
        yield 'different names and values' => [false, new TimeEntry('name', new \DateInterval('PT10S')), new TimeEntry('different_name', new \DateInterval('PT20S'))];
        yield 'equal names and different values day' => [false, new TimeEntry('name', new \DateInterval('PT1S')), new TimeEntry('name', new \DateInterval('PT2S'))];
        yield 'different names characters and equal values' => [false, new TimeEntry('NAME', new \DateInterval('P1D')), new TimeEntry('name', new \DateInterval('P1D'))];
    }

    public function test_creating_from_days() : void
    {
        self::assertEquals(
            new \DateInterval('P1D'),
            TimeEntry::fromDays('time', 1)->value()
        );
    }

    public function test_creating_from_hours() : void
    {
        self::assertEquals(
            new \DateInterval('PT1H'),
            TimeEntry::fromHours('time', 1)->value()
        );
    }

    public function test_creating_from_microseconds() : void
    {
        self::assertEquals(
            new \DateInterval('PT1S'),
            TimeEntry::fromMicroseconds('time', 1000000)->value()
        );
    }

    public function test_creating_from_milliseconds() : void
    {
        self::assertEquals(
            new \DateInterval('PT1S'),
            TimeEntry::fromMilliseconds('time', 1000)->value()
        );
    }

    public function test_creating_from_minutes() : void
    {
        self::assertEquals(
            new \DateInterval('PT1M'),
            TimeEntry::fromMinutes('time', 1)->value()
        );
    }

    public function test_creating_from_seconds() : void
    {
        self::assertEquals(
            new \DateInterval('PT1S'),
            TimeEntry::fromSeconds('time', 1)->value()
        );
    }

    public function test_creating_from_time_string() : void
    {
        $timeEntry = new TimeEntry('name', '00:01:23');

        self::assertEquals(
            new TimeEntry('name', new \DateInterval('PT1M23S')),
            $timeEntry
        );
    }

    public function test_entry_name_can_be_zero() : void
    {
        self::assertSame('0', (new TimeEntry('0', new \DateInterval('PT10S')))->name());
    }

    #[DataProvider('is_equal_data_provider')]
    public function test_is_equal(bool $equals, TimeEntry $entry, TimeEntry $nextEntry) : void
    {
        self::assertEquals($equals, $entry->isEqual($nextEntry));
    }

    public function test_map() : void
    {
        $entry = new TimeEntry('entry-name', new \DateInterval('PT10S'));

        self::assertEquals(
            $entry,
            $entry->map(fn (\DateInterval $time) => $time)
        );
    }

    public function test_prevents_from_creating_entry_with_empty_entry_name() : void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Entry name cannot be empty');

        new TimeEntry('', new \DateInterval('P1D'));
    }

    public function test_renames_entry() : void
    {
        $entry = new TimeEntry('entry-name', new \DateInterval('P1D'));
        $newEntry = $entry->rename('new-entry-name');

        self::assertEquals('new-entry-name', $newEntry->name());
        self::assertEquals($entry->value(), $newEntry->value());
    }

    public function test_serialization() : void
    {
        $string = new TimeEntry('name', new \DateInterval('P1D'));

        $serialized = \serialize($string);
        /** @var TimeEntry $unserialized */
        $unserialized = \unserialize($serialized);

        self::assertTrue($string->isEqual($unserialized));
    }

    public function test_to_string() : void
    {
        self::assertEquals('00:01:00', TimeEntry::fromMinutes('time', 1)->toString());
        self::assertEquals('00:00:00.001000', TimeEntry::fromMilliseconds('time', 1)->toString());
        self::assertEquals('00:00:00.000001', TimeEntry::fromMicroseconds('time', 1)->toString());
        self::assertEquals('10:00:00', TimeEntry::fromHours('time', 10)->toString());
        self::assertEquals('26:12:31', TimeEntry::fromString('time', 'P1DT2H12M31S')->toString());
    }
}
