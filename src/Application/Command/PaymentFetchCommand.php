<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Application\DTO\PaymentFetchDTO;
use App\Application\Exception\FetchingException;
use App\Application\Exception\ReportNotFoundException;
use App\Application\Exception\ReportNotCompleteException;
use App\Domain\Model\PaymentReport;
use App\Domain\Repository\PaymentReportRepository;
use App\Domain\Repository\PaymentRepository;
use Psr\Log\LoggerInterface;

final class PaymentFetchCommand
{
    public function __construct(
        private readonly PaymentReportRepository $paymentReportRepository,
        private readonly PaymentRepository $paymentRepository,
        private readonly LoggerInterface $logger
    ) {
    }

    public function execute(int $timestamp, ?int $limit = null, ?int $offset = null): PaymentFetchDTO
    {
        $this->logger->debug('Runn