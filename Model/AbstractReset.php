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
     * AbstractReset constructor.
     * @param \Magento\Framework\App\ResourceConnection $resource
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Indexer\Model\IndexerFactory $indexerFactory
    )
    {
        $this->_resource = $resource;
        $this->_indexerFactory = $indexerFactory;
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
    protected function getTableName($table) {
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
        $tables = $this->getTables();
        $before = $this->executeBefore();
        $after = $this->executeAfter();
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
     * Return tables to truncate.
     */
    abstract protected function getTables();

    /**
     * Return SQL code to execute before reset
     * @return array
     */
    abstract protected function executeBefore();

    /**
     * Return SQL code to execute after reset
     * @return array
     */
    abstract protected function executeAfter();
}
