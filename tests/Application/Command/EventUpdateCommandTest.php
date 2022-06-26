<?php

declare(strict_types=1);

namespace App\Tests\Application\Command;

use App\Application\Command\EventUpdateCommand;
use App\Application\DTO\ClickEventUpdateDTO;
use App\Application\DTO\ViewEventUpdateDTO;
use App\Application\Exception\ValidationException;
use App\Domain\Exception\InvalidDataException;
use App\Domain\Model\PaymentReport;
use App\Domain\Repository\EventRepository;
use App\Domain\Repository\PaymentReportRepository;
use App\Domain\ValueObject\EventType;
use App\Domain\ValueObject\PaymentReportStatus;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class EventUpdateCommandTest extends TestCase
{
    public function testExecuteCommand()
    {
        $timestamp = (int)floor(time() / 3600) * 3600 - 7200;

        $dto = new ViewEventUpdateDTO(
            [
                'time_start' => $timestamp + 12,
                'time_end' => $timestamp + 32,
                'events' => [],
            ]
        );

        $report = new PaymentReport($timestamp, PaymentReportStatus::createIncomplete());

        $eventRepository = $this->createMock(EventRepository::class);
        $eventRepository
            ->expects($this->once())
            ->method('saveAll')
            ->with($dto->getEvents())
            ->willReturn(100);

        $paymentReportRepository = $this->createMock(PaymentReportRepositor