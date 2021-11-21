<?php

declare(strict_types=1);

namespace App\Domain\Exception;

use InvalidArgumentException as BaseInvalidArgumentException;

class InvalidArgumentException extends BaseInvalidArgumentException
{
    public static function fromArgument(string $name, string $value = '', string $restrictions = ''): self
    {
        $message = sprintf('Give