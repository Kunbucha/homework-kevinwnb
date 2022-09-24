<?php

declare(strict_types=1);

namespace App\Tests\Application\DTO;

use App\Application\DTO\EventUpdateDTO;
use App\Application\Exception\ValidationException;
use App\Domain\Model\Event;
use App\Domain\ValueObject\EventType;
use PHPUnit\Framework\TestCase;

abstract class EventUpdateDTOTest extends TestCase
{
    abstract protected function getEventType(): EventType;

    abstract protected function createDTO(array $data): EventUpdateDTO;

    public function testEmptyInputData(): void
    {
        $this->expectException(ValidationException::class);

        $this->createDTO([]);
    }

    public function testInvalidInputData(): void
    {
        $this->expectException(ValidationException::class);

        $this->createDTO(['invalid' => []]);
    }

    public function testNoEventsInputData(): void
    {
        $this->expectException(ValidationException::class);

        $this->createDTO(
            [
                'time_start' => time() - 10,
                'time_end' => time() - 1,
            ]
        );
    }

    public function testValidTimespanData(): void
    {
        $time_start = time() - 10;
        $time_end = time() - 1;

        $dto = $this->createDTO(
            [
                'time_start' => $time_start,
                'time_end' => $time_end,
                'events' => [],
            ]
        );

        $this->assertEquals($time_start, $dto->getEvents()->getTimeStart()->getTimestamp());
        $this->assertEquals($time_end, $dto->getEvents()->getTimeEnd()->getTimestamp());

        $dto = $this->createDTO(
            [
                'time_start' => $time_start,
                'time_end' => $time_start,
                'events' => [],
            ]
        );

        $this->assertEquals($time_start, $dto->getEvents()->getTimeStart()->getTimestamp());
        $this->assertEquals($time_start, $dto->getEvents()->getTimeEnd()->getTimestamp());
    }

    /**
     * @dataProvider invalidTimespanDataProvider
     */
    public function testInvalidTimespanData($data): void
    {
        $this->expectException(ValidationException::class);

        $this->createDTO(array_merge(['events' => []], $data));
    }

    public function testEventTimeOutOfRangeLeft(): void
    {
        $this->expectException(ValidationException::class);

        $this->createDTO(
            [
                'time_start' => time() - 10,
                'time_end' => time() - 1,
                'events' => [
                    static::simpleEvent(['time' => time() - 15]),
                ],
            ]
        );
    }

    public function testEventTimeOutOfRangeRight(): void
    {
        $this->expectException(ValidationException::class);

        $this->createDTO(
            [
                'time_start' => time() - 10,
                'time_end' => time() - 5,
                'events' => [
                    static::simpleEvent(['time' => time() - 1]),
                ],
            ]
        );
    }

    /**
     * @dataProvider validDataProvider
     */
    public function testValidData(array $data, int $count = 1): void
    {
        $dto = $this->createDTO(
            [
                'time_start' => time() - 500,
                'time_end' => time() - 1,
                'events' => $data,
            ]
        );

        $this->assertCount($count, $dto->getEvents());
    }

    /**
     * @dataProvider invalidDataProvider
     */
    public function testInvalidData(array $data): void
    {
        $this->expectException(ValidationException::class);

        $this->createDTO(
            [
                'time_start' => time() - 500,
                'time_end' => time() - 1,
                'events' => $data,
            ]
        );
    }

    public function testModel(): void
    {
        $input = static::simpleEvent(
            ['zone_id' => 'aac567e1396b4cadb52223a51796fdbb', 'context' => ['a' => 1]]
        );
        $dto = $this->createDTO(['time_start' => time() - 500, 'time_end' => time() - 1, 'events' => [$input],]);

        /* @var $event Event */
        $event = $dto->getEvents()->first();

        $this->assertEquals($this->getEventType()->toString(), $event->getType()->toString());
        $this->assertEquals($input['id'], $event->getId());
        $this->assertEquals($input['time'], $event->getTime()->getTimestamp());
        $this->assertEquals($input['case_id'], $event->getCaseId());
        $this->assertEquals($input['case_time'], $event->getCaseTime()->getTimestamp());
        $this->assertEquals($input['publisher_id'], $event->getPublisherId());
        $this->assertEquals($input['zone_id'], $event->getZoneId());
        $this->assertEquals($input['advertiser_id'], $event->getAdvertiserId());
        $this->assertEquals($input['campaign_id'], $event->getCampaignId());
        $this->assertEquals($input['banner_id'], $event->getBannerId());
        $this->assertEquals($input['impression_id'], $event->getImpressionId());
        $this->assertEquals($input['tracking_id'], $event->getTrackingId());
        $this->assertEquals($input['user_id'], $event->getUserId());
        $this->assertEquals($input['context'], $event->getContextData());
        $this->assertEquals($input['human_score'], $event->getHumanScore());
    }

    public static function invalidTimespanDataProvider(): array
    {
        return [
            [[]],
            [['time_start' => time() - 1]],
            [['time_end' => time() - 1]],
            [['time_start' => 'invalid', 'time_end' => time() - 1]],
            [['time_start' => time() - 10, 'time_end' => 'invalid']],
            [['time_start' => time() - 10, 'time_end' => time() - 15]],
            [['time_start' => time() - 3000000, 'time_end' => time() - 1]],
            [['time_start' => time() - 10, 'time_end' => time() + 100]],
        ];
    }

    public static function validDataProvider(): array
    {
        return array_merge(
            static::validEventsDataProvider(),
            static::validCaseDataProvider(),
            static::validImpressionDataProvider()
        );
    }

    public static function invalidDataProvider(): array
    {
        return array_merge(
            static::invalidEventsDataProvider(),
            static::invalidCaseDataProvider(),
            static::invalidImpressionDataProvider()
        );
    }

    protected static function validEventsDataProvider(): array
    {
        return [
            [[], 0],
            [[static::simpleEvent()]],
            [[static::simpleEvent(), static::simpleEvent()], 2],
        ];
    }

    protected static function validCaseDataProvider(): array
    {
        return [
            [[static::simpleEvent(['zone_id' => null])]],
            [[static::simpleEvent(['zone_id' => 'aac567e1396b4cadb52223a51796fdbb'])]],
        ];
    }

    protected static function validImpressionDataProvider(): array
    {
        return [
            [[static::simpleEvent(['keywords' => null])]],
            [[static::simpleEvent(['keywords' => []])]],
            [[static::simpleEvent(['keywords' => ['k' => 333]])]],
            [[static::simpleEvent(['context' => null])]],
            [[static::simpleEvent(['context' => []])]],
            [[static::simpleEvent(['context' => ['a' => 123]])]],
            [[static::simpleEvent(['page_rank' => 0.0])]],
            [[static::simpleEvent(['page_rank' => 0.59])]],
            [[static::simpleEvent(['page_rank' => 1.0])]],
            [[static::simpleEvent(['page_rank' => -1.0])]],
            [[static::simpleEvent(['page_rank' => -1])]],
        ];
    }

    protected static function invalidEventsDataProvider(): array
    {
        return [
            [[static::simpleEvent([], 'id')]],
            [[static::simpleEvent(['id' => null])]],
            [[static::simpleEvent(['id' => 0])]],
            [[static::simpleEvent(['id' => 'invalid_value'])]],
            [[static::simpleEvent([], 'time')]],
            [[static::simpleEvent(['time' => null])]],
            [[static::simpleEvent(['time' => 0])]],
            [[static::simpleEvent(['time' => 'invalid_value'])]],
        ];
    }

    protected static function invalidCaseDataProvider(): array
    {
        return [
            [[static::simpleEvent([], 'case_id')]],
            [[static::simpleEvent(['case_id' => null])]],
            [[static::simpleEvent(['case_id' => 0])]],
            [[static::simpleEvent(['case_id' => 'invalid_value'])]],
            [[static::simpleEvent(['case_time' => null])]],
            [[static::simpleEvent(['case_time' => 0])]],
            [[static::simpleEvent(['case_time' => 'invalid_value'])]],
            [[static::simpleEvent(['case_time' => time()])]],
            [[static::simpleEvent([], 'publisher_id')]],
            [[static::simpleEvent(['publisher_id' => null])]],
            [[static::simpleEvent(['publisher_id' => 0])]],
            [[static::simpleEvent(['publisher_id' =>