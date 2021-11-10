<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Application\DTO\PaymentReportFetchDTO;
use App\Domain\Repository\PaymentReportRepository;
use App\Domain\ValueObject\PaymentReportStatus;
use Psr\Log\LoggerInterface;

final class ReportFetchCommand
{
    public function __construct(
        private readonly PaymentReportRepository $paymentReportRepository,
        private readonly LoggerInterface $logger
    ) {
    }

    public function execute(int ...$ids): PaymentReportFetchD