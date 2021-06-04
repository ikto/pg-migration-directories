<?php

namespace IKTO\PgMigrationDirectories\Processor;

use IKTO\PgMigrationDirectories\Adapter\ConnectionAdapterInterface;
use IKTO\PgMigrationDirectories\Migration\DefinitionInterface;

interface ProcessorInterface
{
    /**
     * Applies the migration via the adapter.
     *
     * @param ConnectionAdapterInterface $db
     *   The connection adapter for applying migration.
     * @param DefinitionInterface $migration
     *   The migration to apply.
     */
    public function applyMigration(ConnectionAdapterInterface $db, DefinitionInterface $migration);
}
