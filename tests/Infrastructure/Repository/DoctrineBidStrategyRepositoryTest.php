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

        $result = $repository->saveAll(new BidStrategyCollection