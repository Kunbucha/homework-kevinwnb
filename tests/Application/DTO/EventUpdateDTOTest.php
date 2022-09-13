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
    