<?php

declare(strict_types=1);

namespace App\Domain\Model;

use App\Domain\Exception\InvalidArgumentException;
use App\Domain\ValueObject\EventType;
use App\Domain\ValueObject\Id;
use App\Domain\ValueObject\PaymentStatus;

final class Payment
{
    /** @var EventType */
    private $eventType;

    /** @var Id */
    private $eventId;

    /** @var PaymentStatus */
    private $status;

    /** @var ?int */
    private $value;

    /** @var int */
    private $reportId;

    public function __construct(
        EventType $eventType,
        Id $eventId,
        PaymentStatus $status,
        ?int $value = null,
        ?int $reportId = null
 