<?php

declare(strict_types=1);

namespace App\Tests\UI\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class BidStrategyControllerTest extends WebTestCase
{
    public function testUpdateBidStrategy(): void
    {
        $parameters = [
            'bid_strategies' => [
                [
                    'id' => 'fff567e1396b4cadb52223a51796f