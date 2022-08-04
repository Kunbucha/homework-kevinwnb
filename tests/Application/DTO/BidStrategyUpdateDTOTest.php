<?php

declare(strict_types=1);

namespace App\Tests\Application\DTO;

use App\Application\DTO\BidStrategyUpdateDTO;
use App\Application\Exception\ValidationException;
use App\Domain\Model\BidStrategy;
use PHPUnit\Framework\TestCase;

final class BidStrategyUpdateDTOTest extends TestCase
{
    public function testEmptyInputData(): void
    {
        $this->expectException(ValidationException::class);

        new BidStrategyUpdateDTO([]);
    }

    public function testInvalidInputData(): void
    {
        $this->expectException(ValidationException::class);

        new BidStrategyUpdateDTO(['invalid' => []]);
    }

    public function testInvalidBidStrategie