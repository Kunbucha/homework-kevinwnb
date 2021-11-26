<?php

declare(strict_types=1);

namespace App\Domain\Model;

use App\Domain\ValueObject\BannerType;
use App\Domain\ValueObject\Id;
use DateTimeInterface;

final class Banner
{
    /** @var Id */
    private $id;

    /** @var Id */
    private $campaignId;

    /** @var string */
    private $size;

    /** @var BannerType */
    private $type;

    /** @var DateTimeInterface|null */
    private $deletedAt;

    public function __construct(
        Id $id,
        Id $campaignId,
        string $size,
        BannerType $type,
        DateTimeInterface $deletedAt = null
    ) {
        $this->id = $id;
    