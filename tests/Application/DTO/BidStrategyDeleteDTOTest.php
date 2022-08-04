
<?php

declare(strict_types=1);

namespace App\Tests\Application\DTO;

use App\Application\DTO\BidStrategyDeleteDTO;
use App\Application\Exception\ValidationException;
use PHPUnit\Framework\TestCase;

final class BidStrategyDeleteDTOTest extends TestCase
{
    public function testEmptyInputData(): void
    {
        $this->expectException(ValidationException::class);

        new BidStrategyDeleteDTO([]);
    }

    /**
     * @dataProvider validIdDataProvider
     */
    public function testValidIdData(array $data, int $count = 1): void
    {
        $dto = new BidStrategyDeleteDTO(['bid_strategies' => $data]);

        $this->assertCount($count, $dto->getIds());
    }

    /**
     * @dataProvider invalidIdDataProvider
     */
    public function testInvalidIdData($data): void
    {
        $this->expectException(ValidationException::class);

        new BidStrategyDeleteDTO(['bid_strategies' => $data]);
    }

    public function validIdDataProvider(): array
    {
        return [
            [[], 0],
            [['43c567e1396b4cadb52223a51796fd01']],
            [['ffc567e1396b4cadb52223a51796fdff', '43c567e1396b4cadb52223a51796fd01'], 2],
            [
                [
                    'ffc567e1396b4cadb52223a51796fdff',
                    '43c567e1396b4cadb52223a51796fd01',
                    'ffc567e1396b4cadb52223a51796fdff',
                ],
                3,
            ],
        ];
    }

    public function invalidIdDataProvider(): array
    {
        return [
            [123],
            [[123]],
            [['invalid']],
            [['ffc567e1396b4cadb52223a51796fdff', 'xyz']],
        ];
    }
}