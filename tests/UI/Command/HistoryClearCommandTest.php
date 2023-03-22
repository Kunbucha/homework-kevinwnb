<?php

declare(strict_types=1);

namespace App\Tests\UI\Command;

use App\Domain\Model\EventCollection;
use App\Domain\Model\Impression;
use App\Domain\Model\ImpressionCase;
use App\Domain\Model\PaymentReport;
use App\Domain\Model\ViewEvent;
use App\Domain\ValueObject\Context;
use App\Domain\ValueObject\EventType;
use App\Domain\ValueObject\Id;
use App\Domain\ValueObject\PaymentReportStatus;
use App\Infrastructure\Repository\DoctrineEventRepository;
use App\Infrastructure\Repository\DoctrinePaymentReportRepository;
use DateTime;
use Psr\Log\NullLogger;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\Store\FlockStore;
use Symfony\Component\Lock\Store\SemaphoreStore;

final class HistoryClearCommandTest extends CommandTestCase
{
    protected static $command = 'ops:history:clear';

    public function testExecute(): void
    {
        $this->setUpReports(3);
        $this->setUpEvents(5);
        $this->executeCommand([], 0, '3 payment reports removed', '5 events removed');
    }

    public function testEmptyExecute(): void
    {
        $this->executeCommand([], 0, '0 payment reports removed', '0 events removed');
    }

    public function testPeriod(): void
    {
        $this->executeCommand([], 0, 'Clearing payments and events older than ' . self::periodToDate(48 * 3600));
        $this->executeCommand(
            ['--period' => 'PT0H'],
            0,
            'Clearing payments and events older than ' . self::periodToDate(0)
        );
        $this->executeCommand(
            ['--period' => 'P1D'],
            0,
            'Clearing payments and events older than ' . self::periodToDate(24 * 3600)
        )