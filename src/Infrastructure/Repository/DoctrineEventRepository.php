<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Exception\DomainRepositoryException;
use App\Domain\Exception\InvalidDataException;
use App\Domain\Model\Event;
use App\Domain\Model\EventCollection;
use App\Domain\Repository\EventRepository;
use App\Domain\ValueObject\EventType;
use App\Infrastructur