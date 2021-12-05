<?php

declare(strict_types=1);

namespace App\Domain\Model;

use App\Domain\ValueObject\Context;
use App\Domain\ValueObject\Id;

final class Impression
{
    /** @var Id */
    private $id;

    /** @var Id */
    private $trackingId;

    /** @var Id */
    private $userId;
