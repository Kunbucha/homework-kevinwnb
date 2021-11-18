<?php

declare(strict_types=1);

namespace App\Application\DTO;

use App\Application\Exception\ValidationException;
use App\Domain\Exception\InvalidArgumentException;
use App\Domain\Model\BidStrategy;
use App\Domain\Model\BidStrategyCollection;
use App\Domain\ValueObject\Id;
use TypeError;

final class BidStrategyUpdateDTO
{
    private BidStrategyCollection $bidStrategies;

    public function __construct(array $input)
    {
        $this->validate($input);
        $this->fill($input);
    }

    public function getBidStrategies(): Bi