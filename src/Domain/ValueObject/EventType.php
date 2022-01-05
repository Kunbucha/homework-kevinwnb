<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use App\Domain\Exception\InvalidArgumentException;

final class EventType
{
    public const CLICK = 'click';
    public const CONVERSION = 'conversion';
    public const VIEW = 'view';

    /** @var string */
    private $type;

    public function __construct(string $type)
    {
        if ($type !== self::CLICK && $type !== self::CONVERSION && $type !== self::VIEW) {
            throw InvalidArgumentException::fromArgument('type', $type);
        }

        $this->type = $type;
    }

    public static function createClick(): self
    {
        return new self(self::CLICK);
    }

    public static function createConversion(): self
    {
        return new self(self::CONVERSION);
    }

    public static function creat