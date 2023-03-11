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
        $this->assertFalse($status->isR