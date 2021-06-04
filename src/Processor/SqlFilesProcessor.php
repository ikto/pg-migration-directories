<?php

namespace IKTO\PgMigrationDirectories\Processor;

use IKTO\PgMigrationDirectories\Adapter\ConnectionAdapterInterface;
use IKTO\PgMigrationDirectories\Migration\DefinitionInterface;
use IKTO\PgMigrationDirectories\Migration\SqlFilesMigrationDefinition;

class SqlFilesProcessor extends AbstractSqlFilesProcessor implements ProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function applyMigration(ConnectionAdapterInterface $db, DefinitionInterface $migration)
    {
        /** @var SqlFilesMigrationDefinition $migration */
        $sqlCommands = $this->getSqlCommandsFromMigration($migration);

        foreach ($sqlCommands as $sqlCommand) {
            $db->executeSqlCommand($sqlCommand);
        }
    }
}
