<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Exception\DomainRepositoryException;
use App\Domain\Model\BidStrategy;
use App\Domain\Model\BidStrategyCollection;
use App\Domain\Repository\BidStrategyRepository;
use App\Domain\ValueObject\IdCollection;
use App\Infrastructure\Mapper\BidStrategyMapper;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception as DBALException;

final class DoctrineBidStrategyRepository extends DoctrineModelUpdater implements BidStrategyRepository
{
    public function saveAll(BidStrategyCollection $bidStrategies): int
    {
        if (0 === $bidStrategies->count()) {
            return 0;
        }

        $count = 0;

        $mapIds = [];
        foreach ($bidStrategies as $bidStrategy) {
            /*  @var $bidStrategy BidStrategy */
            $id = $bidStrategy->getId()->toBin();
            if (!isset($mapIds[$id])) {
                $mapIds[$id] = 1;
            }
        }
        $ids = array_keys($mapIds);
        $deleteQuery = sprintf('DELETE FROM %s WHERE bid_strategy_id IN (?)', BidStrategyMapper::table());

        $this->db->beginTransaction();
        try {
            $this->db->executeQuery($deleteQuery, [$ids], [Connection::PARAM_STR_ARRAY]);

            foreach ($bidStrategies as $bidStrategy) {
                /*  @var $bidStrategy BidStrategy */
                $this->db->insert(
                    BidStrategyMapper::table(),
                    BidStrategyMapper::map($bidStrategy),
                    BidStrategyMapper::types()
                );

                ++$count;
            }
            $this->db->commit();
        } catch (DBALException $exception) {
            $this->db->rollBack();
            throw new DomainRepositoryException($exception->getMessage());
        }

        return $count;
    }

    public function deleteAll(IdCollection $ids): int
    {
        try {
            $result = $this->softDelete(BidStrategyMapper::table(), $ids->toBinArray(), 'bid_stra