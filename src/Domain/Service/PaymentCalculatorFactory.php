<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Repository\BidStrategyRepository;
use App\Domain\Repository\CampaignRepository;
use App\Domain\Repository\CampaignCostRepository;
use App\Domain\ValueObject\PaymentCalculatorConfig;

class PaymentCalculatorFactory
{
    private CampaignRepository $campaignRepository;

    pri