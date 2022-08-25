<?php

declare(strict_types=1);

namespace App\Tests\Application\DTO;

use App\Application\DTO\CampaignUpdateDTO;
use App\Application\Exception\ValidationException;
use App\Domain\Model\Banner;
use App\Domain\Model\Campaign;
use App\Domain\Model\Conversion;
use DateTime;
use PHPUnit\Framework\TestCase;

final class CampaignUpdateDTOTest extends TestCase
{
    public function testEmptyInputData(): void
    {
        $this->expectException(ValidationException::class);

        new CampaignUpdateDTO([]);
    }

    public function testInvalidInputData(): void
    {
        $this->expectException(ValidationException::class);

        new CampaignUpdateDTO(['invalid' => []]);
    }

    /**
     * @dataProvider validCampaignsDataProvider
     */
    public function testValidCampaignsData(array $data, int $count = 1): void
    {
        $dto = new CampaignUpdateDTO(['campaigns' => $data]);

        $this->assertCount($count, $dto->getCampaigns());
    }

    /**
     * @dataProvider invalidCampaignsDataProvider
     */
    public function testInvalidCampaignsData(array $data): void
    {
        $this->expectException(ValidationException::class);

        new CampaignUpdateDTO(['campaigns' => $data]);
    }

    /**
     * @dataProvider validBannersDataProvider
     */
    public function testValidBannersData(array $data, int $count = 1): void
    {
        $dto = new CampaignUpdateDTO(
            [
                'campaigns' => [
                    self::simpleCampaign(['banners' => $data]),
                ],
            ]
        );

        $this->assertCount($count, $dto->getCampaigns()->first()->getBanners());
    }

    /**
     * @dataProvider invalidBannersDataProvider
     */
    public function testInvalidBannersData($data): void
    {
        $this->expectException(ValidationException::class);

        new CampaignUpdateDTO(
            [
                'campaigns' => [
                    self::simpleCampaign(['banners' => $data]),
                ],
            ]
        );
    }

    /**
     * @dataProvider validFiltersDataProvider
     */
    public function testValidFiltersData($data): void
    {
        $dto = new CampaignUpdateDTO(
            [
                'campaigns' => [
                    self::simpleCampaign(['filters' => $data]),
                ],
            ]
        );

        $this->assertCount(1, $dto->getCampaigns());
    }

    /**
     * @dataProvider invalidFiltersDataProvider
     */
    public function testInvalidFiltersData($data): void
    {
        $this->expectException(ValidationException::class);

        new CampaignUpdateDTO(
            [
                'campaigns' => [
                    self::simpleCampaign(['filters' => $data]),
                ],
            ]
        );
    }

    /**
     * @dataProvider validConversionsDataProvider
     */
    public function testValidConversionsData($data, int $count = 1): void
    {
        $dto = new CampaignUpdateDTO(
            [
                'campaigns' => [
                    self::simpleCampaign(['conversions' => $data]),
                ],
            ]
        );

        $this->assertCount($count, $dto->getCampaigns()->first()->getConversions());
    }

    /**
     * @dataProvider invalidConversionsDataProvider
     */
    public function testInvalidConversionsData($data): void
    {
        $this->expectException(ValidationException::class);

        new CampaignUpdateDTO(
            [
                'campaigns' => [
                    self::simpleCampaign(['conversions' => $data]),
                ],
            ]
        );
    }

    public function testModel(): void
    {
        $bannersInput = self::simpleBanner();
        $conversionInput = self::simpleConversion();

        $input = self::simpleCampaign(
            [
                'time_end' => (new DateTime())->getTimestamp() + 200,
                'max_cpm' => 100,
                'max_cpc' => 200,
                'banners' => [$bannersInput],
                'filters' => ['require' => ['a'], 'exclude' => ['b']],
                'conversions' => [$conversionInput],
                'medium' => 'metaverse',
            ]
        );
        $dto = new CampaignUpdateDTO(['campaigns' => [$input]]);

        /* @var $campaign Campaign */
        $campaign = $dto->getCampaigns()->first();
        /* @var $banner Banner */
        $banner = $campaign->getBanners()->first();
        /* @var $conversion Conversion */
        $conversion = $campaign->getConversions()->first();

        $this->assertEquals($input['id'], $campaign->getId());
        $this->assertEquals($input['advertiser_id'], $campaign->getAdvertiserId());
        $this->assertEquals($input['time_start'], $campaign->getTimeStart()->getTimestamp());
        $this->assertEquals($input['time_end'], $campaign->getTimeEnd()->getTimestamp());
        $this->assertEquals($input['budget'], $campaign->getBudgetValue());
        $this->assertEquals($input['max_cpm'], $campaign->getMaxCpm());
        $this->assertEquals($input['max_cpc'], $campaign->getMaxCpc());
        $this->assertEquals($input['filters'], $campaign->getFilters());
        $this->assertTrue($campaign->isMetaverse());

        $this->assertEquals($input['id'], $banner->getCampaignId());
        $this->assertEquals($bannersInput['id'], $banner->getId());
        $this->assertEquals($bannersInput['size'], $banner->getSize());
        $this->assertEquals($bannersInput['type'], $banner->getType());

        $this->assertEquals($input['id'], $conversion->getCampaignId());
        $this->assertEquals($conversionInput['id'], $conversion->getId());
        $this->assertEquals($conversionInput['limit_type'], $conversion->getLimitType());
        $this->assertEquals($conversionInput['is_repeatable'], $conversion->isRepeatable());
    }

    public function testDefaultMedium(): void
    {
        $input = self::simpleCampaign([], 'medium');
        $dto = new CampaignUpdateDTO(['campaigns' => [$input]]);

        /* @var $campaign Campaign */
        $campaign = $dto->getCampaigns()->first();
        $this->assertTrue($campaign->isWeb());
    }

    public function validCampaignsDataProvider(): array
    {
        return [
            [[], 0],
            [[self::simpleCampaign()]],
            [[self::simpleCampaign(), self::simpleCampaign()], 2],
            [[sel