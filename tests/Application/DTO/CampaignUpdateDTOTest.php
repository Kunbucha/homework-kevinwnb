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
        $bannersI