
<?php

declare(strict_types=1);

namespace App\Tests\Application\DTO;

use App\Application\DTO\CampaignDeleteDTO;
use App\Application\Exception\ValidationException;
use PHPUnit\Framework\TestCase;

final class CampaignDeleteDTOTest extends TestCase
{
    public function testEmptyInputData(): void
    {
        $this->expectException(ValidationException::class);

        new CampaignDeleteDTO([]);
    }

    /**
     * @dataProvider validIdDataProvider
     */
    public function testValidIdData(array $data, int $count = 1): void
    {
        $dto = new CampaignDeleteDTO(['campaigns' => $data]);

        $this->assertCount($count, $dto->getIds());
    }

    /**
     * @dataProvider invalidIdDataProvider
     */
    public function testInvalidIdData($data): void