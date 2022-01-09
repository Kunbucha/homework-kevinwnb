<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

final class PaymentCalculatorConfig
{
    private float $humanScoreThreshold = 0.5;

    private fl