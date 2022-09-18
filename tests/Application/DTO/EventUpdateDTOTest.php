<?php

declare(strict_types=1);

namespace App\Tests\Application\DTO;

use App\Application\DTO\EventUpdateDTO;
use App\Application\Exception\ValidationException;
use App\Domain\Model\Event;
use App\Domain\ValueObject\EventType;
use PHPUnit\Framework\TestCase;

abstract class EventUpdateDTOTest extends TestCase
{
    abstract protected function getEventType(): EventType;

    abstract protected function createDTO(array $data): EventUpdateDTO;

    public function testEmptyInputData(): void
    {
        $this->expectException(ValidationException::class);

        $this->createDTO([]);
    }

    public function testInvalidInputData(): void
    {
        $this->expectException(ValidationException::class);

        $this->createDTO(['invalid' => []]);
    }

    public function testNoEventsInputData(): void
    {
        $this->expectException(ValidationException::class);

        $this->createDTO(
            [
                'time_start' => time() - 10,
                'time_end' => time() - 1,
            ]
        );
    }

    public function testValidTimespanData(): void
    {
        $time_start = time() - 10;
        $time_end = time() - 1;

        $dto = $this->createDTO(
            [
                'time_start' => $time_start,
                'time_end' => $time_end,
                'events' => [],
            ]
        );

        $this->assertEquals($time_start, $dto->getEvents()->getTimeStart()->getTimestamp());
        $this->assertEquals($time_end, $dto->getEvents()->getTimeEnd()->getTimestamp());

        $dto = $this->createDTO(
            [
                'time_start' => $time_start,
                'time_end' => $time_start,
                'events' => [],
            ]
        );

        $this->assertEquals($time_start, $dto->getEvents()->getTimeStart()->getTimestamp());
        $this->assertEquals($time_start, $dto->getEvents()->getTimeEnd()->getTimestamp());
    }

    /**
     * @dataProvider invalidTimespanDataProvider
     */
    public function testInvalidTimespanData($data): void
    {
        $this->expectException(ValidationException::class);

        $this->createDTO(array_merge(['events' => []], $data));
    }

    public function testEventTimeOutOfRangeLeft(): void
    {
        $this->expectException(ValidationException::class);

        $this->createDTO(
            [
                'time_start' => time() - 10,
                'time_end' => time() - 1,
                'events' => [
                    static::simpleEvent(['time' => time() - 15]),
                ],
            ]
        );
    }

    public function testEventTimeOutOfRangeRight(): void
    {
        $this->expectException(ValidationException::class);

        $this->createDTO(
            [
                'time_start' => time() - 10,
                'time_end' => time() - 5,
                'events' => [
                    static::simpleEvent(['time' => time() - 1]),
                ],
            ]
        );
    }

    /**
     * @dataProvider validDataProvider
     */
    public function testValidData(array $data, int $count = 1): void
    {
        $dto = $this->createDTO(
            [
                'time_start' => time() - 500,
                'time_end' => time() - 1,
                'events' => $data,
            ]
        );

        $this->assertCou