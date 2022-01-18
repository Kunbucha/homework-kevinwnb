<?php

declare(strict_types=1);

namespace App\Infrastructure\Mapper;

use App\Domain\Model\ConversionEvent;
use App\Domain\Model\Event;
use App\Domain\ValueObject\EventType;
use Doctrine\DBAL\Types\Types;

class ConversionEventMapper e