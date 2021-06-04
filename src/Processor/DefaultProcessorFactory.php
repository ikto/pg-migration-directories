<?php

namespace IKTO\PgMigrationDirectories\Processor;

use IKTO\PgMigrationDirectories\Migration\DefinitionInterface;
use IKTO\PgMigrationDirectories\Migration\SqlFilesMigrationDefinition;

class DefaultProcessorFactory implements ProcessorFactoryInterface
{
    /**
     * Gets the migration processor for the migration.
     *
     * @param DefinitionInterface $migration
     *   The migration to get processor for.
     *
     * @return ProcessorInterface
     */
    public function getProcessorForMigration(DefinitionInterface $migration)
    {
        if ($migration instanceof SqlFilesMigrationDefinition) {
            return new SqlFilesProcessor();
        }

        throw new \InvalidArgumentException(sprintf('Unable to fund processor for the "%s"', get_class($migration)));
    }
}
