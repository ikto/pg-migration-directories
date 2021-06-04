<?php

namespace IKTO\PgMigrationDirectories\Processor;

use IKTO\PgMigrationDirectories\Migration\DefinitionInterface;

interface ProcessorFactoryInterface
{
    /**
     * Gets the migration processor for the migration.
     *
     * @param DefinitionInterface $migration
     *   The migration to get processor for.
     *
     * @return ProcessorInterface
     */
    public function getProcessorForMigration(DefinitionInterface $migration);
}
