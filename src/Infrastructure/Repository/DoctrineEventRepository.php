<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Exception\DomainRepositoryException;
use App\Domain\Exception\InvalidDataException;
use App\Domain\Model\Event;
use App\Domain\Model\EventCollection;
use App\Domain\Repository\EventRepository;
use App\Domain\ValueObject\EventType;
use App\Infrastructure\Mapper\ClickEventMapper;
use App\Infrastructure\Mapper\ConversionEventMapper;
use App\Infrastructure\Mapper\EventMapper;
use App\Infrastructure\Mapper\ViewEventMapper;
use DateTimeInterface;
use Doctrine\DBAL\Driver\Exception as DBALDriverException;
use Doctrine\DBAL\Exception as DBALException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\DBAL\Types\Types;

final class DoctrineEventRepository extends DoctrineModelUpdater implements EventRepository
{
    public function saveAll(
        EventCollection $events
    ): int {
        return $this->upsertEvents($events, self::getMapper($events->getType()));
    }

    public function deleteByTime(
        EventType $type,
        ?DateTimeInterface $timeStart = null,
        ?DateTimeInterface $timeEnd = null
    ): int {
        /*  @var $mapper EventMapper */
        $mapper = self::getMapper($type);
        try {
            return $this->clearInterval($mapper::table(), $timeStart, $timeEnd);
        } catch (DBALException $exception) {
            throw new DomainRepositoryException($exception->getMessage());
        }
    }

    public function fetchByTime(
        ?DateTimeInterface $timeStart = null,
        ?DateTimeInterface $timeEnd = null
    ): iterable {
        $params = [];
        $types = [];
        $query = 'SELECT * FROM %s WHERE 1=1';
        $query .= self::timeCondition($timeStart, $timeEnd, $params, $types);

        try {
            $result = $this->db->executeQuery(sprintf($query, ViewEventMapper::table()), $params, $types);
            while ($row = $result->fetchAssociative()) {
                yield ViewEventMapper::fillRaw($row);
            }
            $result = $this->db->executeQuery(sprintf($query, ClickEventMapper::table()), $params, $types);
            while ($row = $result->fetchAssociative()) {
                yield ClickE