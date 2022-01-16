<?php

declare(strict_types=1);

namespace App\Infrastructure\Mapper;

use App\Domain\Model\CampaignCost;
use App\Domain\ValueObject\Id;
use Doctrine\DBAL\Types\Types;

class CampaignCostMapper
{
    public static function table(): string
    {
        return 'campaign_costs';
    }

    public static function map(CampaignCost $campaignCost): array
    {
        return [
            'report_id' => $campaignCost->getReportId(),
            'campaign_id' => $campaignCost->getCampaignId()->toBin(),
            'score' => $campaignCost->getScore(),
            'max_cpm' => $campaignCost->getMaxCpm(),
            'cpm_factor' => $campaignCost->getCpmFactor(),
            'views' => $campaignCost->getViews(),
            'views_cost' => $campaignCost->getViewsCost(),
            'clicks' => $campaignCost->getClicks(),
            'clicks_cost' => $campaignCost->getClicksCost(),
            'conversions' => $campaignCost->getConversions(),
            'conversions_cost' => $campaignCost->getConversionsCost(),
        ];
    }

    public static