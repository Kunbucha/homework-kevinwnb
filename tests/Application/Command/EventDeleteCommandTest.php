<?php

declare(strict_types=1);

namespace App\Tests\Application\Command;

use App\Application\Command\EventDeleteCommand;
use App\Domain\Repository\EventRepository;
use App\Domain\ValueObject\EventType;
use DateTime;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class EventDeleteCommandTest extends TestCase
{
    public function testExecuteCommand()
    {
        $date = new DateTime('2019-01-