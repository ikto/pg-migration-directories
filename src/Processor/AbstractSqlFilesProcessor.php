<?php

namespace IKTO\PgMigrationDirectories\Processor;

use IKTO\PgMigrationDirectories\Migration\SqlFilesMigrationDefinition;
use IKTO\PgMigrationDirectories\SqlFileParserTrait;
use Symfony\Component\Finder\Finder;

abstract class AbstractSqlFilesProcessor
{
    use SqlFileParserTrait;

    /**
     * Extracts SQL commands from the migration.
     *
     * @param SqlFilesMigrationDefinition $migration
     *   The migration to extract SQL commands from.
     *
     * @return string[]
     */
    protected function getSqlCommandsFromMigration(SqlFilesMigrationDefinition $migration)
    {
        $files = [];
        $finder = new Finder();
        $finder
            ->files()
            ->in($migration->getBase())
            ->name('/\.sql$/')
            ->notName('/^\./')
            ->notName('/\~$/')
            ->sortByName()
        ;

        foreach ($finder as $fileInfo) {
            $files[] = $fileInfo->getRealPath();
        }

        $commands = [];
        foreach ($files as $file) {
            $commands = array_merge($commands, $this->getSqlCommandsFromFile($file));
        }

        return $commands;
    }
}
