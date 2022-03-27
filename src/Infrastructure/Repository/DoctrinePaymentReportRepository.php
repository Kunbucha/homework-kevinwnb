<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Exception\DomainRepositoryException;
use App\Domain\Model\PaymentReport;
use App\Domain\Model\PaymentReportCollection;
use App\Domain\Repository\PaymentReportRepository;
use App\Domain\ValueObject\PaymentReportStatus;
use App\Infrastructure\Mapper\PaymentReportMapper;
use DateTimeInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception as DBALException;

final class DoctrinePaymentReportRepository extends DoctrineModelUpdater implements PaymentReportRepository
{
    public function fetch(int $id): ?PaymentReport
    {
        try {
            $result =
                $this->db->fetchAssociative(
                    sprintf('SELECT * FROM %s WHERE id = ?', PaymentReportMapper::table()),
                    [$id]
                );
        } catch (DBALException $exception) {
            throw new DomainRepositoryException($exception->getMessage());
        }

        return $result !== false ? PaymentReportMapper::fill($result) : null;
    }

    public function fetchOrCreate(int $id): PaymentReport
    {
        if (null === ($report = $this->fetch($id))) {
            $report = new PaymentReport($id, PaymentReportStatus::createIncomplete());
            $this->save($report);
        }
        return $report;
    }

    public function fetchAll(): PaymentReportCollection
    {
        return $this->fetchQuery();
    }

    public function fetchById(int ...$ids): PaymentReportCollection
    {
        $conditions = [];
        foreach ($ids as $id) {
            $conditions[] = $id;
        }
        return $this->fetchQuery('id IN (?)', [$conditions], [Connection::PARAM_INT_ARRAY]);
    }

    public function fetchByStatus(PaymentReportStatus ...$statuses): PaymentReportCollection
    {
        $conditions = [];
        foreach ($statuses as $status) {
            $conditions[] = $status->getStatus();
        }
        return $this->fetchQuery('status IN (?)', [$conditions], [Connection::PARAM_INT_ARRAY]);
    }

    public function save(PaymentReport $report): void
    {
        try {
            $this->upsert(
                PaymentReportMapper::table(),
                $report->getId(),
                PaymentReportMapper::map($report),
                PaymentReportMapper::types()
            );
        } catch (DBALException $exception) {
            throw new DomainRepositoryExc