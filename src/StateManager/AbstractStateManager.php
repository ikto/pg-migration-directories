<?php

namespace IKTO\PgMigrationDirectories\StateManager;

abstract class AbstractStateManager implements StateManagerInterface
{
    /**
     * @var string
     */
    protected $schemaName;

    /**
     * The table name for storing current db version.
     *
     * @var string
     */
    protected $migrationSchemaVersionTableName = 'migration_schema_version';

    /**
     * The table schema name for storing current db version.
     *
     * @var string
     */
    protected $migrationSchemaVersionTableSchemaName = null;

    /**
     * The table name for storing db migrations log.
     *
     * @var string
     */
    protected $migrationSchemaLogTableName = 'migration_schema_log';

    /**
     * The table schema name for storing db migrations log.
     *
     * @var string
     */
    protected $migrationSchemaLogTableSchemaName = null;

    /**
     * @param string $migrationSchemaVersionTableName
     */
    public function setMigrationSchemaVersionTableName($migrationSchemaVersionTableName)
    {
        $this->migrationSchemaVersionTableName = $migrationSchemaVersionTableName;
    }

    /**
     * @param string $migrationSchemaVersionTableSchemaName
     */
    public function setMigrationSchemaVersionTableSchemaName($migrationSchemaVersionTableSchemaName)
    {
        $this->migrationSchemaVersionTableSchemaName = $migrationSchemaVersionTableSchemaName;
    }

    /**
     * @param string $migrationSchemaLogTableName
     */
    public function setMigrationSchemaLogTableName($migrationSchemaLogTableName)
    {
        $this->migrationSchemaLogTableName = $migrationSchemaLogTableName;
    }

    /**
     * @param string $migrationSchemaLogTableSchemaName
     */
    public function setMigrationSchemaLogTableSchemaName($migrationSchemaLogTableSchemaName)
    {
        $this->migrationSchemaLogTableSchemaName = $migrationSchemaLogTableSchemaName;
    }
}
