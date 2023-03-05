<?php

declare(strict_types=1);

namespace App\Tests\Domain\ValueObject;

use App\Domain\ValueObject\Id;
use App\Domain\ValueObject\IdCollection;
use PHPUnit\Framework\TestCase;

final class IdCollectionTest extends TestCase
{
    public function testMultiplyAdding(): void
    {
        $id1 = '00000000000000000000000000000001';
        $id2 = '00000000000000000000000000000002';
        $id3 = '00000000000000000000000000000003';
        $id4 = '00000000000000000000000000000004';

        $collection = new IdCollection(
            new Id($id1),
            new Id($id2),
            new Id($id3),
    