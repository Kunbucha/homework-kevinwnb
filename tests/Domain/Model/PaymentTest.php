<?php

declare(strict_types=1);

namespace App\Tests\Domain\Model;

use App\Domain\Exception\InvalidArgumentException;
use App\Domain\Model\Payment;
use App\Domain\ValueObject\EventType;
use App\Domain\ValueObject\Id;
use App\Domain\ValueObject\PaymentStatus;
use PHPUnit\Framework\TestCase;

final class PaymentTest extends TestCase
{
    public function testInstanceOfPayment(): void
    {
        $reportId = 123;
        $eventId = '43c567e1396b4cadb52223a51796fd01';
        $status = PaymentStatus::INVALID_TARGETING;
        $value = 100;

        $payment = new Payment(
            EventType::createView(),
            new Id($eventId),
            new PaymentStatus($status)
        );

        $this->assertInstanceOf(Payment::class, $payment);
        $this->assertEquals(EventType::VIEW, $payment->getEventType());
        $this->assertEquals($eventId, $payment->getEventId());
        $this->assertEquals($status, $payment->getStatus()->getStatus());
        $this->assertEquals($status, $payment->getStatusCode());
        $this->assertFalse($payment->isAccepted());
        $this->assertNull($payment->getValue());

        $payment = new Payment(
      