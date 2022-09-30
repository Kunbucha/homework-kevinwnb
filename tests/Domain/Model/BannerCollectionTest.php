<?php

declare(strict_types=1);

namespace App\Tests\Domain\Model;

use App\Domain\Model\Banner;
use App\Domain\Model\BannerCollection;
use App\Domain\ValueObject\BannerType;
use App\Domain\ValueObject\Id;
use PHPUnit\Framework\TestCase;

final class BannerCollectionTest extends TestCase
{
    public function testMultiplyAdding(