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

    private const CAMPAIGN_CPC = 1500000;

    private const BANNER_ID = '70000000000000000000000000000001';

    private const BANNER_SIZE = '100x200';

    private const USER_ID = 'a0000000000000000000000000000001';

    private const CONVERSION_GROUP_ID = 'b0000000000000000000000000000001';

    private const CONVERSION_ID = 'c0000000000000000000000000000001';

    private const CONVERSION_VALUE = 200;

    private const BID_STRATEGY_ID = 'd0000000000000000000000000000001';

    public function testPaymentList(): void
    {
        $reportId = 0;
        $campaigns = new CampaignCollection(self::campaign([], [self::banner()], [self::conversion()]));
        $bidStrategies = new BidStrategyCollection();
        $payments = (new PaymentCalculator(
            $campaigns,
            $bidStrategies,
            $this->getMockedCampaignCostRepository(),
            new PaymentCalculatorConfig()
        ))->calculate($reportId, [self::viewEvent(), self::clickEvent()]);

        $list = [];
        array_push($list, ...$payments);

        $this->assertCount(2, $list);
    }

    public function testCampaignNotExist(): void
    {
        $this->statusForAll(PaymentStatus::CAMPAIGN_NOT_FOUND, ['campaign_id' => '6000000000000000000000000000000f']);
    }

    public function testCampaignDeleted(): void
    {
        $this->statusForAll(PaymentStatus::ACCEPTED, [], ['deleted_at' => self::TIME + 10]);
        $this->statusForAll(PaymentStatus::ACCEPTED, [], ['deleted_at' => self::TIME - 10]);
        $this->statusForAll(PaymentStatus::CAMPAIGN_NOT_FOUND, [], ['deleted_at' => self::TIME - 110]);
        $this->statusForAll(PaymentStatus::CAMPAIGN_NOT_FOUND, [], ['deleted_at' => self::TIME - 3600 * 24]);
    }

    public function testCampaignOutdated(): void
    {
        $this->statusForAll(PaymentStatus::ACCEPTED, [], ['time_end' => self::TIME + 10]);
        $this->statusForAll(PaymentStatus::ACCEPTED, [], ['time_end' => self::TIME - 10]);
        $this->statusForAll(PaymentStatus::CAMPAIGN_OUTDATED, [], ['time_end' => self::TIME - 110]);
        $this->statusForAll(PaymentStatus::CAMPAIGN_OUTDATED, [], ['time_end' => self::TIME - 3600 * 24]);

        $this->statusForAll(PaymentStatus::ACCEPTED, [], ['time_start' => self::TIME - 110]);
        $this->statusForAll(PaymentStatus::CAMPAIGN_OUTDATED, [], ['time_start' => self::TIME - 10]);
        $this->statusForAll(PaymentStatus::CAMPAIGN_OUTDATED, [], ['time_start' => self::TIME + 3600 * 24]);
    }

    public function testBannerNotExist(): void
    {
        $this->statusForAll(PaymentStatus::BANNER_NOT_FOUND, ['banner_id' => '7000000000000000000000000000000f']);
    }

    public function testBannerDeleted(): void
    {
        $this->statusForAll(PaymentStatus::ACCEPTED, [], [], ['deleted_at' => self::TIME + 10]);
        $this->statusForAll(PaymentStatus::ACCEPTED, [], [], ['deleted_at' => self::TIME - 10]);
        $this->statusForAll(PaymentStatus::BANNER_NOT_FOUND, [], [], ['deleted_at' => self::TIME - 110]);
        $this->statusForAll(PaymentStatus::BANNER_NOT_FOUND, [], [], ['deleted_at' => self::TIME - 3600 * 24]);
    }

    public function testConversionNotExist(): void
    {
        $campaigns = new CampaignCollection(self::campaign([], [self::banner()], [self::conversion()]));

        $payment = $this->single(
            $campaigns,
            self::conversionEvent(
                [
                    'conversion_id' => 'c000000000000000000000000000000f',
                ]
            )
        );
        $this->assertEquals(PaymentStatus::CONVERSION_NOT_FOUND, $payment['status']);
    }

    public function testConversionDeleted(): void
    {
        $campaigns = new CampaignCollection(
            self::campaign([], [self::banner()], [self::conversion(['deleted_at' => self::TIME + 10])])
        );
        $payment = $this->single($campaigns, self::conversionEvent());
        $this->assertEquals(PaymentStatus::ACCEPTED, $payment['status']);

        $campaigns = new CampaignCollection(
            self::campaign([], [self::banner()], [self::conversion(['deleted_at' => self::TIME - 10])])
        );
        $payment = $this->single($campaigns, self::conversionEvent());
        $this->assertEquals(PaymentStatus::ACCEPTED, $payment['status']);

        $campaigns = new CampaignCollection(
            self::campaign([], [self::banner()], [self::conversion(['deleted_at' => self::TIME - 110])])
        );
        $payment = $this->single($campaigns, self::conversionEvent());
        $this->assertEquals(PaymentStatus::CONVERSION_NOT_FOUND, $payment['status']);

        $campaigns = new CampaignCollection(
            self::campaign([], [self::banner()], [self::conversion(['deleted_at' => self::TIME - 3600 * 24])])
        );
        $payment = $this->single($campaigns, self::conversionEvent());
        $this->assertEquals(PaymentStatus::CONVERSION_NOT_FOUND, $payment['status']);
    }

    public function testPreviousState(): void
    {
        $campaigns = new CampaignCollection(
            self::campaign([], [self::banner()], [self::conversion()])
        );

        $payment = $this->single($campaigns, self::conversionEvent(['payment_status' => PaymentStatus::ACCEPTED]));
        $this->assertEquals(PaymentStatus::ACCEPTED, $payment['status']);

        $payment =
            $this->single($campaigns, self::conversionEvent(['payment_status' => PaymentStatus::HUMAN_SCORE_TOO_LOW]));
        $this->assertEquals(PaymentStatus::ACCEPTED, $payment['status']);

        $payment =
            $this->single($campaigns, self::conversionEvent(['payment_status' => PaymentStatus::CAMPAIGN_OUTDATED]));
        $this->assertEquals(PaymentStatus::CAMPAIGN_OUTDATED, $payment['status']);

        $payment =
            $this->single($campaigns, self::conversionEvent(['payment_status' => PaymentStatus::INVALID_TARGETING]));
        $this->assertEquals(PaymentStatus::INVALID_TARGETING, $payment['status']);
    }

    public function testHumanScore(): void
    {
        $this->statusForAll(PaymentStatus::HUMAN_SCORE_TOO_LOW, ['human_score' => 0]);
        $this->statusForAll(PaymentStatus::HUMAN_SCORE_TOO_LOW, ['human_score' => 0.3]);
        $this->statusForAll(PaymentStatus::HUMAN_SCORE_TOO_LOW, ['human_score' => 0.399]);
        $this->statusForAll(PaymentStatus::ACCEPTED, ['human_score' => 0.5]);
        $this->statusForAll(PaymentStatus::ACCEPTED, ['human_score' => 0.501]);
        $this->statusForAll(PaymentStatus::ACCEPTED, ['human_score' => 0.7]);
        $this->statusForAll(PaymentStatus::ACCEPTED, ['human_score' => 1]);

        $campaigns = new CampaignCollection(self::campaign([], [self::banner()], [self::conversion()]));
        $payment = $this->single($campaigns, self::viewEvent(['human_score' => 0.499]));
        $this->assertEquals(PaymentStatus::HUMAN_SCORE_TOO_LOW, $payment['status']);

        $campaigns = new CampaignCollection(self::campaign([], [self::banner()], [self::conversion()]));
        $payment = $this->single($campaigns, self::viewEvent(['human_score' => 0.4]));
        $this->assertEquals(PaymentStatus::HUMAN_SCORE_TOO_LOW, $payment['status']);

        $campaigns = new CampaignCollection(self::campaign([], [self::banner()], [self::conversion()]));
        $payment = $this->single($campaigns, self::clickEvent(['human_score' => 0.499]));
        $this->assertEquals(PaymentStatus::HUMAN_SCORE_TOO_LOW, $payment['status']);

        $campaigns = new CampaignCollection(self::campaign([], [self::banner()], [self::conversion()]));
        $payment = $this->single($campaigns, self::clickEvent(['human_score' => 0.4]));
        $this->assertEquals(PaymentStatus::HUMAN_SCORE_TOO_LOW, $payment['status']);

        $campaigns = new CampaignCollection(self::campaign([], [self::banner()], [self::conversion()]));
        $payment = $this->single($campaigns, self::conversionEvent(['human_score' => 0.499]));
        $this->assertEquals(PaymentStatus::ACCEPTED, $payment['status']);

        $campaigns = new CampaignCollection(self::campaign([], [self::banner()], [self::conversion()]));
        $payment = $this->single($campaigns, self::conversionEvent(['human_score' => 0.4]));
        $this->assertEquals(PaymentStatus::ACCEPTED, $payment['status']);

        $campaigns = new CampaignCollection(self::campaign([], [self::banner()], [self::conversion()]));
        $payment = $this->single($campaigns, self::conversionEvent(['human_score' => 0.39]));
        $this->assertEquals(PaymentStatus::HUMAN_SCORE_TOO_LOW, $payment['status']);
    }

    public function testHumanScoreForMetaverse(): void
    {
        $data = ['medium' => Medium::Metaverse->value];

        $this->statusForAll(PaymentStatus::HUMAN_SCORE_TOO_LOW, ['human_score' => 0], $data);
        $this->statusForAll(PaymentStatus::HUMAN_SCORE_TOO_LOW, ['human_score' => 0.3], $data);
        $this->statusForAll(PaymentStatus::HUMAN_SCORE_TOO_LOW, ['human_score' => 0.399], $data);
        $this->statusForAll(PaymentStatus::ACCEPTED, ['human_score' => 0.4], $data);
        $this->statusForAll(PaymentStatus::ACCEPTED, ['human_score' => 0.51], $data);

        $campaigns = new CampaignCollection(self::campaign($data, [self::banner()], [self::conversion()]));
        $payment = $this->single($campaigns, self::viewEvent(['human_score' => 0.4]));
        $this->assertEquals(PaymentStatus::ACCEPTED, $payment['status']);

        $campaigns = new CampaignCollection(self::campaign($data, [self::banner()], [self::conversion()]));
        $payment = $this->single($campaigns, self::viewEvent(['human_score' => 0.39]));
        $this->assertEquals(PaymentStatus::HUMAN_SCORE_TOO_LOW, $payment['status']);

        $campaigns = new CampaignCollection(self::campaign($data, [self::banner()], [self::conversion()]));
        $payment = $this->single($campaigns, self::clickEvent(['human_score' => 0.4]));
        $this->assertEquals(PaymentStatus::ACCEPTED, $payment['status']);

        $campaigns = new CampaignCollection(self::campaign($data, [self::banner()], [self::conversion()]));
        $payment = $this->single($campaigns, self::clickEvent(['human_score' => 0.39]));
        $this->assertEquals(PaymentStatus::HUMAN_SCORE_TOO_LOW, $payment['status']);

        $campaigns = new CampaignCollection(self::campaign($data, [self::banner()], [self::conversion()]));
        $payment = $this->single($campaigns, self::conversionEvent(['human_score' => 0.4]));
        $this->assertEquals(PaymentStatus::ACCEPTED, $payment['status']);

        $campaigns = new CampaignCollection(self::campaign($data, [self::banner()], [self::conversion()]));
        $payment = $this->single($campaigns, self::conversionEvent(['human_score' => 0.39]));
        $this->assertEquals(PaymentStatus::HUMAN_SCORE_TOO_LOW, $payment['status']);
    }

    public function testHumanScoreThreshold(): void
    {
        $campaigns = new CampaignCollection(self::campaign([], [self::banner()], [self::conversion()]));

        $payment = $this->single($campaigns, self::viewEvent(['human_score' => 0.5]));
        $this->assertEquals(PaymentStatus::ACCEPTED, $payment['status']);
        $payment = $this->single($campaigns, self::conversionEvent(['human_score' => 0.4]));
        $this->assertEquals(PaymentStatus::ACCEPTED, $payment['status']);

        $payment = $this->single($campaigns, self::viewEvent(['human_score' => 0.5]), ['humanScoreThreshold' => 0.55]);
        $this->assertEquals(PaymentStatus::HUMAN_SCORE_TOO_LOW, $payment['status']);
        $payment = $this->single(
            $campaigns,
            self::conversionEvent(['human_score' => 0.5]),
            ['conversionHumanScoreThreshold' => 0.55]
        );
        $this->assertEquals(PaymentStatus::HUMAN_SCORE_TOO_LOW, $payment['status']);

        $payment = $this->single($campaigns, self::viewEvent(['human_score' => 0.3]), ['humanScoreThreshold' => '0.5']);
        $this->assertEquals(PaymentStatus::HUMAN_SCORE_TOO_LOW, $payment['status']);
        $payment =
            $this->single($campaigns, self::conversionEvent(['human_score' => 0.3]), ['humanScoreThreshold' => '0.5']);
        $this->assertEquals(PaymentStatus::HUMAN_SCORE_TOO_LOW, $payment['status']);

        $payment = $this->single($campaigns, self::viewEvent(['human_score' => 0.49]), ['humanScoreThreshold' => null]);
        $this->assertEquals(PaymentStatus::HUMAN_SCORE_TOO_LOW, $payment['status']);
        $payment =
            $this->single($campaigns, self::conversionEvent(['human_score' => 0.39]), ['humanScoreThreshold' => '0.5']);
        $this->assertEquals(PaymentStatus::HUMAN_SCORE_TOO_LOW, $payment['status']);
    }

    public function testKeywords(): void
    {
        $this->statusForAll(PaymentStatus::ACCEPTED);
        $this->statusForAll(PaymentStatus::INVALID_TARGETING, ['keywords' => ['r1' => ['r1_v3']]]);
        $this->statusForAll(PaymentStatus::INVALID_TARGETING, ['keywords' => ['e1' => ['e1_v1']]]);
        $this->statu