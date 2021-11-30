<?php

declare(strict_types=1);

namespace App\Domain\Model;

use App\Domain\Exception\InvalidArgumentException;
use App\Domain\ValueObject\EventType;
use App\Domain\ValueObject\Id;
use App\Domain\ValueObject\PaymentStatus;
use DateTimeInterface;

final class ConversionEvent extends Event
{
    /** @var Id */
    private $groupId;

    /** @var Id */
    private $