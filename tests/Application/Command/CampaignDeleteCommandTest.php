<?php

declare(strict_types=1);

namespace App\Tests\Application\Command;

use App\Application\Command\CampaignDeleteCommand;
use App\Application\DTO\CampaignDeleteDTO;
use App\Domain\Repository\CampaignRepository;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class CampaignDeleteCommandTest e