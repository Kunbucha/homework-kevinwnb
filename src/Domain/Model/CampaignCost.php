<?php

declare(strict_types=1);

namespace App\Domain\Model;

use App\Domain\ValueObject\Id;

final class CampaignCost
{
    private int $reportId;
    private Id $campaignId;
    private ?float $score;
    private int $maxCpm;
    private float $cpmFactor;
    private int $views;
    private int $viewsCost;
    private int $clicks;
    private int $clicksCost;
    private int $conversions;
    private int $conversionsCost;

    public function __construct(
        int $reportId,
        Id $ca