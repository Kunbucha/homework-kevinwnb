<?php

declare(strict_types=1);

namespace App\Tests\Application\DTO;

use App\Application\DTO\ConversionEventUpdateDTO;
use App\Application\DTO\EventUpdateDTO;
use App\Domain\Model\ConversionEvent;
use App\Domain\ValueObject\EventType;

final class ConversionEventUpdateDTOTest extends EventUpdateDTOTest
{
    protected function getEventType(): EventType
    {
        return EventType::createConversion();
    }

    protected function createDTO(array $data): EventUpdateDTO
    {
        return new ConversionEventUpdateDTO($data);
    }

    public function testConversionModel(): void
    {
        $input = static::simpleEvent(['payment_status' => 1]);
        $dto = $this->createDTO(['time_start' => time() - 500, 'time_end' => time() - 1, 'events' => [$input]]);

        /* @var $event ConversionEvent */
        $event = $dto->getEvents()->first();

        $this->assertEquals($input['conversion_id'], $event->getConversionId());
        $this->assertEquals($input['conversion_value'], $event->getConversionValue());
        $this->assertEquals($input['payment_status'], $event->getPaymentStatus()->getStatus());
    }

    public static function validDataProvider(): array
    {
        return array_merge(
            parent::validDataProvider(),
    