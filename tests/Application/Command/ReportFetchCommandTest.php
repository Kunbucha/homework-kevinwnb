<?php

declare(strict_types=1);

namespace App\Tests\Application\Command;

use App\Application\Command\ReportFetchCommand;
use App\Domain\Model\PaymentReport;
use App\Domain\Model\PaymentReportCollection;
use App\Domain\Repository\PaymentReportRepository;
use App\Domain\ValueObject\PaymentReport