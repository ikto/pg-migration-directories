<?php

namespace IKTO\PgMigrationDirectories\Discovery;

use IKTO\PgMigrationDirectories\Migration\SqlFilesMigrationDefinition;
use Symfony\Component\Finder\Finder;

class SqlFilesDiscovery implements DiscoveryInterface
{
    /**
     * @var string
     */
    protected $base;

    /**
     * @var string
     */
    protected $schemaName;

    /**
     * StandardDiscovery constructor.
     *
     * @param string $base
     *   The base directory for migrations.
     * @param string $schemaName
     *   The schema name.
     */
    public function __construct($base, $schemaName)
    {
        $this->base = $base;
        $this->schemaName = $schemaName;
    }

    /**
     * {@inheritdoc}
     */
    public function getMigrations()
    {
        $finder = new Finder();
        $finder->directories()->in($this->base.'/'.$this->schemaName.'/Pg');

        $definitions = [];
        foreach ($finder as $directory) {
            $definitions[] = new SqlFilesMigrationDefinition($directory->getRealPath());
        }

        return $definitions;
    }
}
