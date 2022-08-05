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

    public function testInvalidBidStrategiesInputData(): void
    {
        $this->expectException(ValidationException::class);

        new BidStrategyUpdateDTO(['bid_strategies' => 'invalid']);
    }

    /**
     * @dataProvider validBidStrategiesDataProvider
     */
    public function testValidBidStrategyData(array $data, int $count = 1): void
    {
        $dto = new BidStrategyUpdateDTO(['bid_strategies' => $data]);

        $this->assertCount($count, $dto->getBidStrategies());
    }

    /**
     * @dataProvider invalidBidStrategiesDataProvider
     */
    public function testInvalidBidStrategyData(array $data): void
    {
        $this->expectException(ValidationException::class);

        new BidStrategyUpdateDTO(['bid_strategies' => $data]);
    }

    public function testModel(): void
    {
        $input = self::simpleBidStrategy();
        $dto = new BidStrategyUpdateDTO(['bid_strategies' => [$input]]);

        /* @var $bidStrategy BidStrategy */
        $bidStrategy = $dt