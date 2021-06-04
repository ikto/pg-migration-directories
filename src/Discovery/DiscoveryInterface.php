<?php

namespace IKTO\PgMigrationDirectories\Discovery;

use IKTO\PgMigrationDirectories\Migration\DefinitionInterface;

interface DiscoveryInterface
{
    /**
     * Gets existing migrations array.
     *
     * @return DefinitionInterface[]
     */
    public function getMigrations();
}
