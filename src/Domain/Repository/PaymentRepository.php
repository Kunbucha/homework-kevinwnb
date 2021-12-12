<?php

declare(strict_types=1);

namespace App\Domain\Repository;

interface PaymentRepository
{
    public function fetchByReportId(int $reportId, ?int $limit = null, ?int