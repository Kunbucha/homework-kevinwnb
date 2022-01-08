<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use App\Domain\Exception\InvalidArgumentException;

final class LimitType
{
    public const IN_BUDGET = 'in_budget';

    public const OUT_OF_BUDGET = 'out_of_budget';

    /** @var string */
    private $type;

    public function __construct(string $type)
    {
        if ($type !== self::IN_BUDGET && $type !== self::OUT_OF_B