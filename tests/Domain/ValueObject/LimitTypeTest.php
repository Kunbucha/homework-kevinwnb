<?php

declare(strict_types=1);

namespace App\Tests\Domain\ValueObject;

use App\Domain\Exception\InvalidArgumentException;
use App\Domain\ValueObject\LimitType;
use PHPUnit\Framework\TestCase;

final class LimitTypeTest extends TestCase
{
    public function testInBudgetType(): void
    {
        $type = LimitType::createInBudget();

        $this->assertEquals('in_budget', $type->toString());
        $this->assertEquals('in_budget', (string)$type);
        $this->assertTrue($type->isInBudget());
        $this->assertFalse($type->isOutOfBudget());
    }

    public function testOutOfBudgetType(): void
    {
        $type = LimitType::createOutOfBudget();

        $thi