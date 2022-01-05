<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use App\Domain\Exception\InvalidArgumentException;

use function preg_match;

class Id
{
    /** @var string */
    private $id;

    public function __construct(string $id)
    {
        if (!preg_match('/^[0-9a-fA-F]{32}$/', $id)) {
            throw InvalidArgumentException::fromArgument('id', $id);
        }

        $t