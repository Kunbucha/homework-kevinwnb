<?php

declare(strict_types=1);

namespace App\Tests\Domain\ValueObject;

use App\Domain\Exception\InvalidArgumentException;
use App\Domain\ValueObject\Id;
use PHPUnit\Framework\TestCase;

final class IdTest extends TestCase
{
    public function testInstanceOfId(): void
    {
        $value = '43c567e1396b4cadb52223a51796fd01';
        $id = new Id($value);

        $this->assertEquals($value, $id->toString());
        $this->assertEquals($value, (string)$id);
        $this->assertEquals(hex2bin($value), $id->toBin());
    }

    public fun