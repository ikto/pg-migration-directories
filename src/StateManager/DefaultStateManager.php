<?php

namespace IKTO\PgMigrationDirectories\StateManager;

use IKTO\PgMigrationDirectories\Adapter\ConnectionAdapterInterface;

class DefaultStateManager extends AbstractStateManager
{
    /**
     * @var ConnectionAdapterInterface
     */
    protected $connectionAdapter;

    public function __construct(ConnectionAdapterInterface $connectionAdapter, $schemaName)
    {
        $this->connectionAdapter = $connectionAdapter;
        $this->schemaName = $schemaName;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentVersion()
    {
        if (!$this->migrationSchemaVersionTableExists()) {
            return 0;
        }

        if (!$this->migrationSchemaVersionRecordExists($this->schemaName)) {
            return 0;
        }

        [$version] = $this->connectionAdapter
            ->getRecordValues(['version'], ['name' => $this->schemaName], $this->migrationSchemaVersionTableName, $this->migrationSchemaVersionTableSchemaName);

        return $version;
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrentVersion($version)
    {
        if (!$this->migrationSchemaVersionTableExists()) {
            throw new \RuntimeException('The migration schema version table does not exist in db');
        }

        $startingVersion = $this->getCurrentVersion();

        if ($this->migrationSchemaVersionRecordExists($this->schemaName)) {
            $this->updateMigrationSchemaVersion($this->schemaName, $version);
        } else {
            $this->insertMigrationSchemaVersion($this->schemaName, $version);
        }

        if ($this->migrationSchemaLogTableExists()) {
            $this->insertMigrationSchemaLog($this->schemaName, $startingVersion, $version);
        }
    }

    /**
     * Checks whether the migration schema version table exists in db.
     *
     * @return bool
     */
    protected function migrationSchemaVersionTableExists()
    {
        return $this->connectionAdapter->tableExists($this->migrationSchemaVersionTableName, $this->migrationSchemaVersionTableSchemaName);
    }

    /**
     * Checks whether the migration schema log table exists in db.
     *
     * @return bool
     */
    protected function migrationSchemaLogTableExists()
    {
        return $this->connectionAdapter->tableExists($this->migrationSchemaLogTableName, $this->migrationSchemaLogTableSchemaName);
    }

    /**
     * Check whether the migration schema version record already exists in db.
     *
     * @param string $schemaName
     *   The migration schema name.
     *
     * @return bool
     */
    protected function migrationSchemaVersionRecordExists($schemaName)
    {
        return $this->connectionAdapter->recordExists(['name' => $schemaName], $this->migrationSchemaVersionTableName, $this->migrationSchemaVersionTableSchemaName);
    }

    /**
     * Creates migration schema version record.
     *
     * @param $schemaName
     *   The migration schema name.
     * @param $version
     *   The db version.
     */
    protected function insertMigrationSchemaVersion($schemaName, $version)
    {
        $this->connectionAdapter->insertRecord(
            [
                'name' => $schemaName,
                'version' => $version,
            ],
            $this->migrationSchemaVersionTableName,
            $this->migrationSchemaVersionTableSchemaName
        );
    }

    /**
     * Updates migration schema version record.
     *
     * @param $schemaName
     *   The migration schema name.
     * @param $version
     *   The db version.
     */
    protected function updateMigrationSchemaVersion($schemaName, $version)
    {
        $this->connectionAdapter->updateRecord(
            ['version' => $version],
            ['name' => $schemaName],
            $this->migrationSchemaVersionTableName,
            $this->migrationSchemaVersionTableSchemaName
        );
    }

    /**
     * Logs the db migration event.
     *
     * @param $schemaName
     *   The migration schema name.
     * @param $startingVersion
     *   The db version we migrating from.
     * @param $targetVersion
     *   The db version we migrating to.
     */
    protected function insertMigrationSchemaLog($schemaName, $startingVersion, $targetVersion)
    {
        $this->connectionAdapter->insertRecord(
            [
                'schema_name' => $schemaName,
                'event_time' => 'now',
                'old_version' => $startingVersion,
                'new_version' => $targetVersion,
            ],
            $this->migrationSchemaLogTableName,
            $this->migrationSchemaLogTableSchemaName
        );
    }
}
