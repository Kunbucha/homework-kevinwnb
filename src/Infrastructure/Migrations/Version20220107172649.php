<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220107172649 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add automatic max cpm history';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<SQL
CREATE TABLE campaign_costs
(
    id               BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    created_at       TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at       TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    report_id        BIGINT(20)      NOT NULL,
    campaign_id      VARBINARY(16)   NOT NULL,
    score   