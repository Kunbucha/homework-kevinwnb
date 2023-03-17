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
        $reportId = 1641286800;
        $nextReportId = $reportId + 3600;
        $campaignId = new Id('f1c567e1396b4cadb52223a51796fd02');
        $this->assertNull($repository->fetch($nextReportId + 3600, $campaignId));

        $result = $repository->saveAll(new CampaignCostCollection());
        $this->assertEquals(0, $result);

        $result = $repository->saveAll(
            new CampaignCostCollection(
                new CampaignCost($reportId, new Id('f1c567e1396b4cadb52223a51796fd01'), 0, 0, 0, 0, 0, 0, 0, 0, 0),
                new CampaignCost($reportId, new Id('f1c567e1396b4cadb52223a51796fd02'), 100, 0, 0, 0, 0, 0, 0, 0, 0)
            )
        );
        $this->assertEquals(2, $result);
        $campaignCost = $repository->fetch($nextReportId, $campaignId);
        $this->assertNotNull($campaignCost);
        $this->assertEquals(100, $campaignCost->getScore());

        $result = $repository->saveAll(
            new CampaignCostCollection(
   