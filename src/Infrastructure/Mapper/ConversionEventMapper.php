<?php

declare(strict_types=1);

namespace App\Infrastructure\Mapper;

use App\Domain\Model\ConversionEvent;
use App\Domain\Model\Event;
use App\Domain\ValueObject\EventType;
use Doctrine\DBAL\Types\Types;

class ConversionEventMapper extends EventMapper
{
    public static function table(): string
    {
        return 'conversion_events';
    }

    public static function map(Event $event): array
    {
        /* @var $event ConversionEvent */
        return array_merge(
            parent::map($event),
            [
                'group_id' => $event->getGroupId()->toBin(),
                'conversion_id' => $event->getConversionId()->toBin(),
                'conversion_value' => $event->getConversionValue(),
                'payment_status' => $event->getPaymentStatus()->getStatus(),
            ]
        );
    }

    public static function types(): array
    {
        return array_merge(
            parent::types(),
            [
                'group_id' => Types::BINARY,
                'conversion_id' => Types::BINARY,
                'conversion_value' => Types::INTEGER,
                'payment_status' =