<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Domain\Repository\EventRepository;
use App\Domain\ValueObject\EventType;
use DateTimeInterface;
use Psr\Log\LoggerInterface;

final class EventDeleteComman