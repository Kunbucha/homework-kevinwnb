<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Exception\DomainRepositoryException;
use App\Domain\Model\Banner;
use App\Domain\Model\BannerCollection;
use App\Domain\Model\Campaign;
use App\Domain\Model\CampaignCollection;
use App\Domain\Model\Conversion;
use App\Domain\Model\ConversionCollection;
use App\Domain\Repository\CampaignRepository;
use App\Domain\ValueObject\IdCollection;
use App\Infrastructure\Mapper\BannerMapper;
use App\Infrastructure\Mapper\CampaignMapper;
use App\Infrastructure\Mapper\ConversionMapper;
use Doctrine\DBAL\Exception as DBALException;

final class DoctrineCampaignRepository extends DoctrineModelUpdater implements CampaignRepository
{
    public function saveAll(CampaignCollection $campaigns): int
    {
        $count = 0;
        try {
            $ids = new IdCollection();
            foreach ($campaigns as $campaign) {
             