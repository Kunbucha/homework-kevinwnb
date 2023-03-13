<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Repository;

use App\Domain\Exception\DomainRepositoryException;
use App\Domain\Model\BidStrategy;
use App\Domain\Model\BidStrategyCollection;
use App\Domain\ValueObject\Id;
use App\Domain\ValueObject\IdCollection;
use App\Infrastructure\Repository\DoctrineBidStrategyRepository;
use Psr\Log\NullLogger;

final class DoctrineBidStrategyRepositoryTest extends RepositoryTestCase
{
    public function testRepository(): void
    {
        $repository = new DoctrineBidStrategyRepository($this->connection, new NullLogger());

        $this->assertEmpty($repository->fetchAll());

        $result = $repository->saveAll(new BidStrategyCollection());

        $this->assertEquals(0, $result);
        $this->assertEmpty($repository->fetchAll());

        $result = $repository->saveAll(
            new BidStrategyCollection(
                new BidStrategy(new Id('f1c567e1396b4cadb52223a51796fd01'), 'user:country:st', 0.99),
                new BidStrategy(new Id('f1c567e1396b4cadb52223a51796fd02'), 'user:country:us', 0.6)
            )
        );

        $this->assertEquals(2, $result);
        $this->assertCount(2, $repository->fetchAll());

        $result = $repository->saveAll(
            new BidStrategyCollection(
                new BidStrategy(new Id('f1c567e1396b4cadb52223a51796fd02'), 'user:country:us', 0.64),
                new BidStrategy(new Id('f1c567e1396b4cadb52223a51796fd03'), 'user:country:in', 0.4)
            )
        );

        $this->assertEquals(2, $result);
        $this->assertCount(3, $repository->f