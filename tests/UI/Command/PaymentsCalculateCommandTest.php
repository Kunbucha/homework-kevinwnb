<?php

declare(strict_types=1);

namespace App\Tests\UI\Command;

use App\Domain\Model\PaymentReport;
use App\Domain\ValueObject\PaymentReportStatus;
use App\Infrastructure\Repository\DoctrinePaymentReportRepository;
use Psr\Log\NullLogger;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\Store\FlockStore;
use Symfony\Component\Lock\Store\SemaphoreStore;

final class PaymentsCalculateCommandTest extends CommandTestCase
{
    protected static $command = 'ops:payments:calculate';

    public function testExecute(): void
    {
        $this->setUpReports(1571824800);
        $this->executeCommand([], 0, 'Calculating report #1571824800', '0 payments calculated');
    }

    public function testEmptyExecute(): void
    {
        $this->executeCommand([], 0, 'Found 0 complete reports');
    }

    public function testExecuteWithTimestamp(): void
    {
        $this->executeCommand(
            ['date