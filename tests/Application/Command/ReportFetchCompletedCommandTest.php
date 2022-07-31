<?php

declare(strict_types=1);

namespace App\Tests\Application\Command;

use App\Application\Command\ReportFetchCompletedCommand;
use App\Domain\Model\PaymentReport;
use App\Domain\Model\PaymentReportCollection;
use App\Domain\Repository\PaymentReportRepository;
use App\Domain\ValueObject\PaymentReportStatus;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class ReportFetchCompletedCommandTest extends Te