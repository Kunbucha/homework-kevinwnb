<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use App\Domain\Exception\InvalidArgumentException;

class Context
{
    /** @var float */
    private $humanScore;

    /** @var float */
    private $pageRank;

    /** @var array */
    private $keywords;

    /* @var array */
    private $data;

    public function __construct(float $humanScore, float $pageRank, array $keywords = [], array $data = [])
    {
        if ($humanScore < 0 || $humanScore > 1) {
            throw InvalidArgumentException::fromArgument(
                'human score',
           