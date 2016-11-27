<?php

namespace Comunicart\Reset\Model;

abstract class AbstractReset
{
    /**
     * Console output if process is executed from command line.
     * 
     * @var \Symfony\Component\Console\Output\OutputInterface 
     */
    protected $_output;

    /**
     * Progress bar
     * @var \Symfony\Component\Console\Helper\ProgressBar
     */
    protected $_progressBar;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $_connection;

    /**
     * @var \Magento\Indexer\Model\IndexerFactory
     */
    protected $_indexerFactory;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;

    /** @var null|array */
    protected $_tables;

    /** @var null|array */
    protected $_executeBefore;

    /** @var null|array */
    protected $_executeAfter;

    /**
     * AbstractReset constructor.
     * @param \Magento\Framework\App\ResourceConnection $resource
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Indexer\Model\IndexerFactory $indexerFactory,
        \Magento\Framework\Event\ManagerInterface $eventManager
    )
    {
        $this->_resource = $resource;
        $this->_indexerFactory = $indexerFactory;
        $this->_eventManager = $eventManager;
    }


    /**
     * @return \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected function getConnection()
    {
        if (!$this->_connection) {
            $this->_connection = $this->_resource->getConnection();
        }
        return $this->_connection;
    }

    /**
     * @param $table
     * @return string
     */
    public function getTableName($table) {
        return $this->_resource->getTableName($table);
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    public function setOutput($output) {
        $this->_output = $output;
    }

    /**
     * @param array $indexerIds
     */
    protected function reindex($indexerIds) {
        foreach ($indexerIds as $indexerId) {
            $indexer = $this->_indexerFactory->create();
            $indexer->load($indexerId);
            $indexer->reindexAll();
        }
    }

    /**
     * Perform reset.
     */
    public function reset()
    {
        $this->_eventManager->dispatch('reset_data', [
            'object' => $this,
            'tables' => $this->_getTables(),
            'execute_before' => $this->_executeBefore(),
            'execute_after' => $this->_executeAfter()
        ]);

        $tables = $this->_getTables();
        $before = $this->_executeBefore();
        $after = $this->_executeAfter();

        if ($this->_output) {
            $total = count($tables);
            if (! empty($before)) {
                $total += count($before);
            }
            if (! empty($after)) {
                $total += count($after);
            }
            $this->_progressBar = new \Symfony\Component\Console\Helper\ProgressBar($this->_output, $total);
            $this->_progressBar->setFormat(
                '%current%/%max% [%bar%] %percent:3s%% %elapsed% %memory:6s%'
            );
            $this->_output->writeln('');
            $this->_output->writeln('<info>Performing reset...</info>');
            $this->_output->writeln('');
            $this->_progressBar->start();
            $this->_progressBar->display();
        }

        if (! empty($before)) {
            foreach ($before as $sql) {
                $this->getConnection()->query($sql);
                if ($this->_output) {
                    $this->_progressBar->advance();
                }
            }
        }

        foreach ($tables as $table) {
            $tableName = $this->getTableName($table);
            $this->getConnection()->query("DELETE FROM $tableName");
            $this->getConnection()->query("SET FOREIGN_KEY_CHECKS = 0");
            $this->getConnection()->query("TRUNCATE TABLE $tableName");
            $this->getConnection()->query("ALTER TABLE $tableName AUTO_INCREMENT=1");
            $this->getConnection()->query("SET FOREIGN_KEY_CHECKS = 1");

            if ($this->_output) {
                $this->_progressBar->advance();
            }
        }

        if (! empty($after)) {
            foreach ($after as $sql) {
                $this->getConnection()->query($sql);
                if ($this->_output) {
                    $this->_progressBar->advance();
                }
            }
        }

        $this->_progressBar->finish();
        $this->_output->writeln('');
        $this->_output->writeln('');

        $this->_output->writeln('<info>Reset has been performed successfully!</info>');

        $this->_output->writeln('');
    }

    /**
     * @param array $tables
     */
    public function setTables($tables) {
        $this->_tables = $tables;
    }

    /**
     * @param array $executeBefore
     */
    public function setExecuteBefore($executeBefore) {
        $this->_executeBefore = $executeBefore;
    }

    /**
     * @param array $executeAfter
     */
    public function setExecuteAfter($executeAfter) {
        $this->_executeAfter = $executeAfter;
    }

    /**
     * Return tables to truncate.
     */
    abstract protected function getTables();

    protected function _getTables() {
        if ($this->_tables === null) {
            $this->_tables = $this->getTables();
        }

        return $this->_tables;
    }

    /**
     * Return SQL code to execute before reset
     * @return array
     */
    abstract protected function executeBefore();

    protected function _executeBefore() {
        if ($this->_executeBefore === null) {
            $this->_executeBefore = $this->executeBefore();
        }

        return $this->_executeBefore;
    }

    /**
     * Return SQL code to execute after reset
     * @return array
     */
    abstract protected function executeAfter();

    protected function _executeAfter() {
        if ($this->_executeAfter === null) {
            $this->_executeAfter = $this->executeAfter();
        }

        return $this->_executeAfter;
    }
}
