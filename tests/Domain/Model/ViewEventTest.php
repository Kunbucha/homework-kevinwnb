<?php

declare(strict_types=1);

namespace App\Tests\Domain\Model;

use App\Domain\Model\Impression;
use App\Domain\Model\ImpressionCase;
use App\Domain\Model\ViewEvent;
use App\Domain\ValueObject\Context;
use App\Domain\ValueObject\EventType;
use App\Domain\ValueObject\Id;
use App\Domain\ValueObject\PaymentStatus;
use App\Lib\DateTimeHelper;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;

final class ViewEventTest extends TestCase
{
    public function testInstanceOfViewEvent(): void
    {
        $eventId = '43c567e1396b4cadb52223a51796fd01';
        $time = '2019-01-01T12:00:00+00:00';

        $caseId = '43c567e1396b4cadb52223a51796fd01';
        $caseTime = '2019-01-01T12:00:00+10:00';
        $publisherId = 'ffc567e1396b4cadb52223a51796fd02';
        $zone