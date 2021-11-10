<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Application\Exception\ReportNotCompleteException;
use App\Domain\Model\PaymentReport;
use App\Domain\Repository\EventRepository;
use App\Domain\Repository\PaymentReportRepository;
use App\Domain\Repository\PaymentRepository;
use App\Domain\Service\PaymentCalculatorFactory;
use DateTimeInterface;
use Psr\Log\LoggerInterface;

final class ReportCalculateCommand
{
    private const BATCH_SIZE = 1000;

    public function __construct(
        private readonly PaymentReportRepository $paymentReportRepository,
        private readonly PaymentRepository $paymentRepository,
        private readonly EventRepository $eventRepository,
        private readonly PaymentCalculatorFactory $paymentCalculatorFactory,
        private readonly LoggerInterface $logger
    ) {
    }

    public function execute(int $timestamp, bool $force = false): int
    {
        $this->logger->debug(sprintf('Running calculate payments command %s', $force ? '[forced]' : ''));

        $reportId = PaymentReport::timestampToId($timestamp);
        $report = $this->paymentReportRepository->fetchOrCreate($reportId);

        if (!$report->isComplete() && !$force) {
            throw new ReportN