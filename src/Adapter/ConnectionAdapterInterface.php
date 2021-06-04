<?php

namespace IKTO\PgMigrationDirectories\Adapter;

interface ConnectionAdapterInterface
{
    /**
     * Start transaction.
     */
    public function openTransaction();

    /**
     * Commit transaction.
     */
    public function commitTransaction();

    /**
     * Roll transaction back.
     */
    public function rollbackTransaction();

    /**
     * Execute SQL command.
     */
    public function executeSqlCommand($sqlCommand);

    /**
     * Checks whether specified table exists in db.
     *
     * @param string $tableName
     *   The table name to check.
     * @param string $tableSchema
     *   The table schema name to check.
     *
     * @return bool
     */
    public function tableExists($tableName, $tableSchema = null);

    /**
     * Checks if the table record exists.
     *
     * @param array $criteria
     *   The criteria for checking records against.
     * @param string $tableName
     *   The table name.
     * @param string|null $tableSchema
     *   The table schema name.
     *
     * @return bool
     */
    public function recordExists($criteria, $tableName, $tableSchema = null);

    /**
     * Gets the values of the particular record.
     *
     * @param array $fieldNames
     *   The field list to get values from.
     * @param array $criteria
     *   The criteria for checking records against.
     * @param string $tableName
     *   The table name.
     * @param string|null $tableSchema
     *   The table schema name.
     *
     * @return array
     */
    public function getRecordValues($fieldNames, $criteria, $tableName, $tableSchema = null);

    /**
     * Inserts new record into the table.
     *
     * @param $values
     *   The key-value associative array with the values for new row.
     * @param string $tableName
     *   The table name.
     * @param string|null $tableSchema
     *   The table schema name.
     *
     * @return mixed
     */
    public function insertRecord($values, $tableName, $tableSchema = null);

    /**
     * @param $values
     *   The key-value associative array with new values for the row.
     * @param $criteria
     *   The criteria for selecting record.
     * @param string $tableName
     *   The table name.
     * @param string|null $tableSchema
     *   The table schema name.
     *
     * @return mixed
     */
    public function updateRecord($values, $criteria, $tableName, $tableSchema = null);
}
