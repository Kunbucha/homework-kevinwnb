<?php

declare(strict_types=1);

namespace App\Infrastructure\Mapper;

use App\Domain\Model\Event;
use Doctrine\DBAL\Types\Types;

abstract class EventMapper
{
    abstract public static function table(): string;

    abstract protected static function getEventType(): string;

    public static function map(Event $event): array
    {
        return [
            'id' => $event->getId()->toBin(),
            'time' => $event->getTime(),
            'case_id' => $event->getCaseId()->toBin(),
            'case_time' => $event->getCaseTime(),
            'publisher_id' => $event->getPublisherId()->toBin(),
            'zone_id' => $event->getZoneId() !== null ? $event->getZoneId()->toBin() : null,
            'advertiser_id' => $event->getAdvertiserId()->toBin(),
            'campaign_id' => $event->getCampaignId()->toBin(),
            'banner_id' => $event->getBannerId()->toBin(),
            'impression_id' => $event->getImpressionId()->toBin(),
            'tracking_id' => $event->getTrackingId()->toBin(),
            'user_id' => $event->getUserId()->toBin(),
            'human_score' => $event->getHumanScore(),
            'page_rank' => $event->getPageRank(),
            'keywords' => $event->getKeywords(),
            'context' => $event->getContextData(),
        ];
    }

    public static function types(): array
    {
        return [
            'id' => Types::BINARY,
            'time' => Types::DATETIME_MUTABLE,
            'case_id' => Types::BINARY,
            'case_time' => Types::DATETIME_MUTABLE,
            'publisher_id' => Types::BINARY,
            'zone_id' => Types::BINARY,
            'advertiser_id' => Types::BINARY,
            'campaign_id' => Types::BINARY,
            'banner_id' => Types::BINARY,
            'impression_id' => Types::BINARY,
            'tracking_id' => Types::BINARY,
            'user_id' => Types::BINARY,
            'human_score' => Types::FLOAT,
            'page_rank' => Types::FLOAT,
            'keywords' => Types::JSON,
            'context' => Types::JSON,
        ];
    }

    public static function fillRaw(array $row): array
    {
        return [
            'type' => static::getEventType(),
            'id