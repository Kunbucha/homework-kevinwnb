<?php

declare(strict_types=1);

namespace App\Tests\Domain\Service;

use App\Domain\Model\Banner;
use App\Domain\Model\BannerCollection;
use App\Domain\Model\BidStrategy;
use App\Domain\Model\BidStrategyCollection;
use App\Domain\Model\Campaign;
use App\Domain\Model\CampaignCollection;
use App\Domain\Model\CampaignCost;
use App\Domain\Model\CampaignCostCollection;
use App\Domain\Model\Conversion;
use App\Domain\Model\ConversionCollection;
use App\Domain\Repository\CampaignCostRepository;
use App\Domain\Service\PaymentCalculator;
use App\Domain\ValueObject\BannerType;
use App\Domain\ValueObject\Budget;
use App\Domain\ValueObject\EventType;
use App\Domain\ValueObject\Id;
use App\Domain\ValueObject\LimitType;
use App\Domain\ValueObject\Medium;
use App\Domain\ValueObject\PaymentCalculatorConfig;
use App\Domain\ValueObject\PaymentStatus;
use App\Lib\DateTimeHelper;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;

final class PaymentCalculatorTest extends TestCase
{
    private const TIME = 1571231623;

    private const ADVERTISER_ID = '50000000000000000000000000000001';

    private const CAMPAIGN_ID = '60000000000000000000000000000001';

    private const CAMPAIGN_BUDGET = 10000000000;

    private const CAMPAIGN_CPV = 100;

    