<?php

namespace IKTO\PgMigrationDirectories\Database;

use IKTO\PgMigrationDirectories\Adapter\ConnectionAdapterInterface;
use IKTO\PgMigrationDirectories\Migration\DefinitionInterface;
use IKTO\PgMigrationDirectories\Processor\ProcessorFactoryInterface;
use IKTO\PgMigrationDirectories\StateManager\StateManagerInterface;

abstract class AbstractManagedDatabase implements ManagedDatabaseInterface
{
    /**
     * @var ConnectionAdapterInterface
     */
    protected $connectionAdapter;

    /**
     * @var StateManagerInterface
     */
    protected $stateManager;

    /**
     * @var ProcessorFactoryInterface
     */
    protected $processorFactory;

    /**
     * @var int
     */
    protected $desiredVersion;

    /**
     * @param ProcessorFactoryInterface $processorFactory
     */
    public function setProcessorFactory(ProcessorFactoryInterface $processorFactory)
    {
        $this->processorFactory = $processorFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getDesiredVersion()
    {
        return $this->desiredVersion;
    }

    /**
     * {@inheritdoc}
     */
    public function setDesiredVersion($desiredVersion)
    {
        $this->desiredVersion = $desiredVersion;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentVersion()
    {
        return $this->stateManager->getCurrentVersion();
    }

    /**
     * {@inheritdoc}
     */
    public function applyMigration(DefinitionInterface $migration)
    {
        $processor = $this->processorFactory->getProcessorForMigration($migration);
        $processor->applyMigration($this->connectionAdapter, $migration);
        $this->stateManager->setCurrentVersion($migration->getTargetVersion());
    }

    /**
     * {@inheritdoc}
     */
    public function openTransaction()
    {
        $this->connectionAdapter->openTransaction();
    }

    /**
     * {@inheritdoc}
     */
    public function commitTransaction()
    {
        $this->connectionAdapter->commitTransaction();
    }

    /**
     * {@inheritdoc}
     */
    public function rollbackTransaction()
    {
        $this->connectionAdapter->rollbackTransaction();
    }
}
