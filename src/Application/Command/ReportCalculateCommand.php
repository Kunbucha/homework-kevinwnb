<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Application\Exception\ReportNotCompleteException;
use App\Domain\Model\PaymentReport;
use App\Domain\Repository\EventRepository;
use App\Domain\Repository\PaymentReportRepository;
use App\Domain\Repository\PaymentRepository;
use App\Domain\Servi