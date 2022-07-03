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

        $paymentReportRepository = $this->createMock(PaymentReportRepository::class);
        $paymentReportRepository
            ->expects($this->once())
            ->method('fetchOrCreate')
            ->with($timestamp)
            ->willReturn($report);
        $paymentReportRepository
            ->expects($this->once())
            ->method('save')
            ->with($report);

        /** @var EventRepository $eventRepository */
        /** @var PaymentReportRepository $paymentReportRepository */
        $command = new EventUpdateCommand($eventRepository, $paymentReportRepository, new NullLogger());
        $this->assertEquals(100, $command->execute($dto));
        $this->assertEquals([[12, 32]], $report->getTypedIntervals($dto->getEvents()->getType()));
    }

    public function testExecuteCrossCommand()
    {
        $timestamp = (int)floor(time() / 3600) * 3600 - 7200;
        $report = new PaymentReport($timestamp, PaymentReportStatus::createIncomplete());

        $viewDto = new ViewEventUpdateDTO(
            [
                'time_start' => $timestamp + 12,
                'time_end' => $timestamp + 32,
                'events' => [],
            ]
        );

        $viewDto2 = new ViewEventUpdateDTO(
            [
                'time_start' => $timestamp + 2000,
                'time_end' => $timestamp + 2055,
                'events' => [],
            ]
        );

        $clickDto = new ClickEventUpdateDTO(
            [
                'time_start' => $timestamp + 30,
                'time_end' => $timestamp + 31,
                'events' => [],
            ]
        );

        $eventRepository = $this->createMock(EventRepository::class);
        $eventRepository
            ->expects($this->exactly(3))
            ->method('saveAll')
            ->withConsecutive([$viewDto->getEvents()], [$viewDto2->getEvents()], [$clickDto->getEvents()])
            ->willReturn(100, 200, 300);

        $paymentReportRepository = $