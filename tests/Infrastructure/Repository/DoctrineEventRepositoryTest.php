
<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Repository;

use App\Domain\Exception\DomainRepositoryException;
use App\Domain\Exception\InvalidDataException;
use App\Domain\Model\ClickEvent;
use App\Domain\Model\ConversionEvent;
use App\Domain\Model\EventCollection;
use App\Domain\Model\Impression;
use App\Domain\Model\ImpressionCase;
use App\Domain\Model\ViewEvent;
use App\Domain\ValueObject\Context;
use App\Domain\ValueObject\EventType;
use App\Domain\ValueObject\Id;
use App\Domain\ValueObject\PaymentStatus;
use App\Infrastructure\Repository\DoctrineEventRepository;
use App\Lib\DateTimeHelper;
use DateTime;
use Psr\Log\NullLogger;

final class DoctrineEventRepositoryTest extends RepositoryTestCase
{
    public function testRepository(): void
    {
        $timestamp = 1571838426;

        $events1 = new EventCollection(EventType::createView());
        $events1->add(self::viewEvent($timestamp - 100, 1));
        $events1->add(self::viewEvent($timestamp - 90, 2));
        $events1->add(self::viewEvent($timestamp - 80, 3));
        $events1->add(self::viewEvent($timestamp - 60, 4));
        $events1->add(self::viewEvent($timestamp - 50, 5));

        $events2 = new EventCollection(EventType::createClick());
        $events2->add(self::clickEvent($timestamp - 50, 1));
        $events2->add(self::clickEvent($timestamp - 40, 2));
        $events2->add(self::clickEvent($timestamp - 20, 3));

        $events3 = new EventCollection(EventType::createConversion());
        $events3->add(self::conversionEvent($timestamp - 20, 1));

        $repository = new DoctrineEventRepository($this->connection, new NullLogger());
        $repository->saveAll($events1);
        $repository->saveAll($events2);
        $repository->saveAll($events3);

        $this->assertCount(9, self::iterableToArray($repository->fetchByTime()));
        $this->assertCount(
            5,
            self::iterableToArray($repository->fetchByTime(DateTimeHelper::fromTimestamp($timestamp - 50)))
        );
        $this->assertCount(
            4,
            self::iterableToArray($repository->fetchByTime(null, DateTimeHelper::fromTimestamp($timestamp - 60)))
        );
        $this->assertCount(
            4,
            $repository->fetchByTime(
                DateTimeHelper::fromTimestamp($timestamp - 70),
                DateTimeHelper::fromTimestamp($timestamp - 30)
            )
        );
        $this->assertEmpty(
            self::iterableToArray($repository->fetchByTime(DateTimeHelper::fromTimestamp($timestamp - 10)))
        );
        $this->assertEmpty(
            self::iterableToArray($repository->fetchByTime(null, DateTimeHelper::fromTimestamp($timestamp - 110)))
        );
        $this->assertEmpty(
            self::iterableToArray(
                $repository->fetchByTime(
                    DateTimeHelper::fromTimestamp($timestamp - 75),
                    DateTimeHelper::fromTimestamp($timestamp - 70)
                )
            )
        );
    }

    public function testViewEvent(): void
    {
        $timestamp = 1571838426;
        $events = new EventCollection(EventType::createView());
        $events->add(self::viewEvent($timestamp, 1));
        $repository = new DoctrineEventRepository($this->connection, new NullLogger());
        $repository->saveAll($events);

        $events = self::iterableToArray($repository->fetchByTime());
        $event = array_pop($events);

        $this->assertEquals(EventType::VIEW, $event['type']);
        $this->assertEquals('f1c567e1396b4cadb52223a51796fd01', $event['id']);
        $this->assertEquals('2019-10-23 13:47:06', $event['time']);
        $this->assertEquals('13c567e1396b4cadb52223a51796fd01', $event['case_id']);
        $this->assertEquals('2019-10-23 13:47:06', $event['case_time']);
        $this->assertEquals('23c567e1396b4cadb52223a51796fd01', $event['publisher_id']);
        $this->assertEquals('33c567e1396b4cadb52223a51796fd01', $event['zone_id']);
        $this->assertEquals('43c567e1396b4cadb52223a51796fd01', $event['advertiser_id']);
        $this->assertEquals('53c567e1396b4cadb52223a51796fd01', $event['campaign_id']);
        $this->assertEquals('63c567e1396b4cadb52223a51796fd01', $event['banner_id']);
        $this->assertEquals('73c567e1396b4cadb52223a51796fd01', $event['impression_id']);
        $this->assertEquals('83c567e1396b4cadb52223a51796fd01', $event['tracking_id']);
        $this->assertEquals('93c567e1396b4cadb52223a51796fd01', $event['user_id']);
        $this->assertEquals(0.98, $event['human_score']);
        $this->assertEquals(0.74, $event['page_rank']);
        $this->assertEquals(['a' => 'aaa'], $event['keywords']);
        $this->assertEquals(['b' => 'bbb'], $event['context']);
    }

    public function testClickEvent(): void
    {
        $timestamp = 1571838426;
        $events = new EventCollection(EventType::createClick());
        $events->add(self::clickEvent($timestamp, 1));
        $repository = new DoctrineEventRepository($this->connection, new NullLogger());
        $repository->saveAll($events);

        $events = self::iterableToArray($repository->fetchByTime());
        $event = array_pop($events);

        $this->assertEquals(EventType::CLICK, $event['type']);
        $this->assertEquals('f1c567e1396b4cadb52223a51796fd01', $event['id']);
        $this->assertEquals('2019-10-23 13:47:06', $event['time']);
        $this->assertEquals('13c567e1396b4cadb52223a51796fd01', $event['case_id']);
        $this->assertEquals('2019-10-23 13:47:05', $event['case_time']);
        $this->assertEquals('23c567e1396b4cadb52223a51796fd01', $event['publisher_id']);
        $this->assertEquals('33c567e1396b4cadb52223a51796fd01', $event['zone_id']);
        $this->assertEquals('43c567e1396b4cadb52223a51796fd01', $event['advertiser_id']);
        $this->assertEquals('53c567e1396b4cadb52223a51796fd01', $event['campaign_id']);
        $this->assertEquals('63c567e1396b4cadb52223a51796fd01', $event['banner_id']);
        $this->assertEquals('73c567e1396b4cadb52223a51796fd01', $event['impression_id']);
        $this->assertEquals('83c567e1396b4cadb52223a51796fd01', $event['tracking_id']);
        $this->assertEquals('93c567e1396b4cadb52223a51796fd01', $event['user_id']);
        $this->assertEquals(0.98, $event['human_score']);
        $this->assertEquals(0.74, $event['page_rank']);
        $this->assertEquals(['a' => 'aaa'], $event['keywords']);
        $this->assertEquals(['b' => 'bbb'], $event['context']);
    }

    public function testConversionEvent(): void
    {
        $timestamp = 1571838426;
        $events = new EventCollection(EventType::createConversion());
        $events->add(self::conversionEvent($timestamp, 1));
        $repository = new DoctrineEventRepository($this->connection, new NullLogger());
        $repository->saveAll($events);

        $events = self::iterableToArray($repository->fetchByTime());
        $event = array_pop($events);

        $this->assertEquals(EventType::CONVERSION, $event['type']);
        $this->assertEquals('f1c567e1396b4cadb52223a51796fd01', $event['id']);
        $this->assertEquals('2019-10-23 13:47:06', $event['time']);
        $this->assertEquals('13c567e1396b4cadb52223a51796fd01', $event['case_id']);
        $this->assertEquals('2019-10-23 13:46:56', $event['case_time']);
        $this->assertEquals('23c567e1396b4cadb52223a51796fd01', $event['publisher_id']);
        $this->assertEquals('33c567e1396b4cadb52223a51796fd01', $event['zone_id']);
        $this->assertEquals('43c567e1396b4cadb52223a51796fd01', $event['advertiser_id']);
        $this->assertEquals('53c567e1396b4cadb52223a51796fd01', $event['campaign_id']);
        $this->assertEquals('63c567e1396b4cadb52223a51796fd01', $event['banner_id']);
        $this->assertEquals('73c567e1396b4cadb52223a51796fd01', $event['impression_id']);
        $this->assertEquals('83c567e1396b4cadb52223a51796fd01', $event['tracking_id']);
        $this->assertEquals('93c567e1396b4cadb52223a51796fd01', $event['user_id']);
        $this->assertEquals(0.98, $event['human_score']);
        $this->assertEquals(0.74, $event['page_rank']);
        $this->assertEquals(['a' => 'aaa'], $event['keywords']);
        $this->assertEquals(['b' => 'bbb'], $event['context']);
        $this->assertEquals('f2c567e1396b4cadb52223a51796fd01', $event['group_id']);
        $this->assertEquals('f3c567e1396b4cadb52223a51796fd01', $event['conversion_id']);
        $this->assertEquals(100, $event['conversion_value']);
        $this->assertEquals(PaymentStatus::HUMAN_SCORE_TOO_LOW, $event['payment_status']);
    }

    public function testDuplicateKey(): void
    {
        $this->expectException(InvalidDataException::class);
