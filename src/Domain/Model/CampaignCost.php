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
        Id $campaignId,
        ?float $score = null,
        int $maxCpm = 0,
        float $cpmFactor = 1.0,
        int $views = 0,
        int $viewsCost = 0,
        int $clicks = 0,
        int $clicksCost = 0,
        int $conversions = 0,
        int $conversionsCost = 0
    ) {
        $this->reportId = $reportId;
        $this->campaignId = $campaignId;
        $this->score = $score;
        $this->maxCpm = $maxCpm;
        $this->cpmFactor = $cpmFactor;
        $this->views = $views;
        $this->viewsCost = $viewsCost;
        $this->clicks = $clicks;
        $this->clicksCost = $clicksCost;
        $this->conversions = $conversions;
        $this->conversionsCost = $conversionsCost;
    }

    public function getReportId(): int
    {
        return $this->reportId;
    }

    public function getCampaignId(): Id
    {
        return $this->campaignId;
    }

   