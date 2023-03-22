<?php

declare(strict_types=1);

namespace App\Tests\UI\Command;

use App\Domain\Model\EventCollection;
use App\Domain\Model\Impression;
use App\Domain\Model\ImpressionCase;
use App\Domain\Model\PaymentReport;
use App\Domain\Model\ViewEvent;
use App\Domain\ValueObject\Context;
use App\Domain\ValueObject\EventType;
use App\Domain\ValueObject\Id;
use App\Domain\ValueObject\PaymentReportStatus;
use App\Infrastructure\Repository\DoctrineEventRepository;
use App\Infrastructure\Repository\DoctrinePaymentReportRepository;
use DateTime;
use Psr\Log\NullLogger;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\Store\FlockStore;
use Symfony\Component\Lock\Store\SemaphoreStore;

final class HistoryClearCommandTest exten