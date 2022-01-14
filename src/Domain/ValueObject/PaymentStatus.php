<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use App\Domain\Exception\InvalidArgumentException;

final class PaymentStatus
{
    public const ACCEPTED = 0;

    public const CAMPAIGN_NOT_FOUND = 1;

    public const HUMAN_SCORE_TOO_LOW = 2;

    public const INVALID_TARGETING = 3;

    public const BANNE