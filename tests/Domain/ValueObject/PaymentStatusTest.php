<?php

declare(strict_types=1);

namespace App\Tests\Domain\ValueObject;

use App\Domain\Exception\InvalidArgumentException;
use App\Domain\ValueObject\PaymentStatus;
use PHPUnit\Framework\TestCase;

final class PaymentStatusTest extends TestCase
{
    public function testDefaultStatus(): void
    {
        $status = new PaymentStatus();

        $this->assertNull($status->getStatus());
        $this->assertFalse($status->isProcessed());
        $this->assertFalse($status->isAccepted());
        $this->assertFalse($status->isRejected());
        $this->assertEquals('unprocessed', $status->toString());
        $this->assertEquals('unprocessed', (string)$status);
    }

    public function testAcceptedStatus(): void
    {
        $status = new PaymentStatus(PaymentStatus::ACCEPTED);

        $this->assertEquals(PaymentStatus::ACCEPTED, $status->getStatus());
        $this->assertTrue($status->isProcessed());
        $this->assertTrue($status->isAccepted());
        $this->assertFalse($status->isRejected());
        $this->assertEquals('accepted', $status->toString());
    }

    public function testUnknownStatus(): void
    {
        $status = new PaymentStatus(PHP_INT_MAX);

        $this->assertTrue($status->isProcessed());
        $this->assertFalse($status->isAccepted());
        $this->assertTrue($status->isRejected());
        $this->assertEquals('rejected:unknown', $status->toString());
