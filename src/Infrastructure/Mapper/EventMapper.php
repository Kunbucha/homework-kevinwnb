<?php

declare(strict_types=1);

namespace App\Infrastructure\Mapper;

use App\Domain\Model\Event;
use Doctrine\DBAL\Types\Types;

abstract class EventMapper
{
    abstract public static function table(): string;

    abstract protected static function getEventType(): string;

    public static func