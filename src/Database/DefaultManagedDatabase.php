<?php

namespace IKTO\PgMigrationDirectories\Database;

use IKTO\PgMigrationDirectories\Adapter\ConnectionAdapterInterface;
use IKTO\PgMigrationDirectories\StateManager\DefaultStateManager;

class DefaultManagedDatabase extends AbstractManagedDatabase
{
    public function __construct(ConnectionAdapterInterface $connectionAdapter, $migrationSchemaName, $storageSchemaName)
    {
        $this->connectionAdapter = $connectionAdapter;
        $this->stateManager = new DefaultStateManager($this->connectionAdapter, $migrationSchemaName);
        $this->stateManager->setMigrationSchemaLogTableSchemaName($storageSchemaName);
        $this->stateManager->setMigrationSchemaVersionTableSchemaName($storageSchemaName);
    }
}
