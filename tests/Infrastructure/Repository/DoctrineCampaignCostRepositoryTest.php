<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Repository;

use App\Domain\Exception\DomainRepositoryException;
use App\Domain\Model\CampaignCost;
use App\Domain\Model\CampaignCostCollection;
use App\Domain\ValueObject\Id;
use App\Infrastructure\Repository\DoctrineCampaignCostRepository;
use DateTime;
use Psr\Log\NullLogger;

final class DoctrineCampaignCostRepositoryTest extends RepositoryTestCase
{
    public function testUpdate(): void
    {
        $repository = new DoctrineCampaignCostRepository($this->connection, new NullLogger());
        $reportId = 164