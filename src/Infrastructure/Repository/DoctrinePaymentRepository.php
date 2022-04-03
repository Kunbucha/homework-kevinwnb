<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Exception\DomainRepositoryException;
use App\Domain\Repository\PaymentRepository;
use App\Infrastructure\Mapper\PaymentMapper;
use Doctrine\DBAL\Driver\Exception as DBALDriverException;
use Doctrine\DBAL\Exception as DBALException;

final class DoctrinePaymentRepository extends DoctrineModelUpdater implements PaymentRepository
{
    public function saveAllRaw(int $reportId, array $payments): int
    {
        foreach ($payments as $key => $payment) {
            $payment['report_id'] = $reportId;
            $payments[$key] = PaymentMapper::map($payment);
        }

        try {
            return $this->insertBatch(
                PaymentMapper::table(),
                $payments,
                PaymentMapper::types()
            );
        } catch (DBALException $exception) {
            throw new DomainRepositoryException($exception->getMessage());
      