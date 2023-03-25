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
        );
    }

    public function testInvalidPeriod(): void
    {
        $this->executeCommand(['--period' => 0], 1, 'Unknown or bad format');
        $this->executeCommand(['--period' => 1000], 1, 'Unknown or bad format');
        $this->executeCommand(['--period' => 'invalid_period'], 1, 'Unknown or bad format');
    }

    public function testBeforeDate(): void
    {
        $this->executeCommand(
            ['--before' => '2019-10-25 11:15:09'],
            0,
            'Clearing payments and events older than 2019-10-25T11:15:09+00:00'
        );
        $this->executeCommand(
            ['--before' => '2019-10-25 11:00:00'],
            0,
            'Clearing payments and events older than 2019-10-25T11:00:00+00:00'
        );
        $this->executeCommand(
            ['--before' => '2019-01-01'],
            0,
            'Clearing payments and events older than 2019-01-01T00:00:00+00:00'
        );
    }

    public function testInvalidBeforeDate(): void
    {
        $this->executeCommand(['--before' => 100], 1, 'Failed to parse time string');
        $this->executeCommand(['--before' => 'invalid_date'], 1, 'Failed to parse time string');
    }

    public function testLock(): void
    {
        $store = SemaphoreStore::isSupported() ? new SemaphoreStore() : new FlockStore();
        $lock = (new LockFactory($store))->createLock(self::$command);
        self::assertTrue($lock->acquire());

        $this->executeCommand([], 1, 'The command is already running in another process.');

        $lock->release();
    }

    private static function periodToDate(int $period): string
    {
        return date('c', (int)floor((time() - $period) / 3600) * 3600);
    }

    private function setUpReports(int $limit): void
    {
        $connection = self::bo