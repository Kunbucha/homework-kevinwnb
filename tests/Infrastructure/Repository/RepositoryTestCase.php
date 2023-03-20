<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception as DBALException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class RepositoryTestCase extends KernelTestCase
{
    /** @var Connection */
    protected $connection;

    protected function setUp(): void
    {
        parent::setUp();
   