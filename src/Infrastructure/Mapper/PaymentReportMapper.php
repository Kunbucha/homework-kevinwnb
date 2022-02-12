<?php

declare(strict_types=1);

namespace App\Infrastructure\Mapper;

use App\Domain\Model\PaymentReport;
use App\Domain\ValueObject\PaymentReportStatus;
use Doctrine\DBAL\Types\Types;

class PaymentReportMapper
{
    public static function table(): string
    {
        return 'payment_reports';
    }

    public static function map(PaymentReport $report): array
    {
        return [
            'id' => $report->getId(),
            'status' => $report->getStatus()->getStatus(),
            'intervals' => $report->getIntervals(),
  