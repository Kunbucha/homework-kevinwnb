<?php

declare(strict_types=1);

namespace App\Tests\Application\DTO;

use App\Application\DTO\ConversionEventUpdateDTO;
use App\Application\DTO\EventUpdateDTO;
use App\Domain\Model\ConversionEvent;
use App\Domain\ValueObject\EventType;

final class ConversionEventUpdateDTOTest extends EventUpdateDTOTest
{
    prot