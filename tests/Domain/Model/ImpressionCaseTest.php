<?php

declare(strict_types=1);

namespace App\Tests\Domain\Model;

use App\Domain\Model\Impression;
use App\Domain\Model\ImpressionCase;
use App\Domain\ValueObject\Context;
use App\Domain\ValueObject\Id;
use App\Lib\DateTimeHelper;
use PHPUnit\Framework\TestCase;

final class ImpressionCaseTest extends TestCase
{
    public function testInstanceOfImpressionCase(): void
    {
        $caseId = '43c567e1396b4cadb52223a51796fd01';
        $caseTime = '2019-01-01T12:00:00+10:00';
        $publisherId = 'ffc567e1396b4cadb52223a51796fd02';
        $zoneId = 'aac567e1396b4cadb52223a51796fdbb';
        $advertiserId = 'bbc567e1396b4cadb52223a51796fdaa';
        $campaignId = 'ccc567e1396b4cadb52