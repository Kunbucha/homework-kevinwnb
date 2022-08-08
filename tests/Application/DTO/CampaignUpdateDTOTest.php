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

   